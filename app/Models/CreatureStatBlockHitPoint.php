<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class CreatureStatBlockHitPoint extends Model
{
    //Dadi Vita normalmente utilizzati dagli stat block
    public const ALLOWED_HIT_DIE_SIZES = [
        4,
        6,
        8,
        10,
        12,
        20,
    ];

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'creature_stat_block_id',
        'average_hit_points',
        'hit_dice_count',
        'hit_die_size',
        'hit_dice_modifier',
        'special_calculation',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_id' => 'integer',
            'average_hit_points' => 'integer',
            'hit_dice_count' => 'integer',
            'hit_die_size' => 'integer',
            'hit_dice_modifier' => 'integer',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        //Controlla la validità della definizione dei Punti Ferita
        static::saving(function (
            CreatureStatBlockHitPoint $hitPoints
        ) {
            //Normalizza il modificatore quando non specificato
            if ($hitPoints->hit_dice_modifier === null) {
                $hitPoints->hit_dice_modifier = 0;
            }

            //Verifica che il valore medio sia positivo
            if (
                $hitPoints->average_hit_points !== null
                && $hitPoints->average_hit_points < 1
            ) {
                throw new InvalidArgumentException(
                    'I Punti Ferita medi devono essere almeno 1.'
                );
            }

            //Verifica che numero e tipo dei dadi siano presenti insieme
            if (
                ($hitPoints->hit_dice_count === null)
                !== ($hitPoints->hit_die_size === null)
            ) {
                throw new InvalidArgumentException(
                    'Il numero e il tipo dei Dadi Vita devono essere indicati insieme.'
                );
            }

            //Verifica che il numero dei dadi sia positivo
            if (
                $hitPoints->hit_dice_count !== null
                && $hitPoints->hit_dice_count < 1
            ) {
                throw new InvalidArgumentException(
                    'Il numero dei Dadi Vita deve essere almeno 1.'
                );
            }

            //Verifica che il Dado Vita utilizzi un formato valido
            if (
                $hitPoints->hit_die_size !== null
                && ! in_array(
                    $hitPoints->hit_die_size,
                    self::ALLOWED_HIT_DIE_SIZES,
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'Il tipo di Dado Vita non è valido.'
                );
            }

            //Controlla la presenza di almeno una definizione dei PF
            $hasAverage =
                $hitPoints->average_hit_points !== null;

            $hasDice =
                $hitPoints->hit_dice_count !== null
                && $hitPoints->hit_die_size !== null;

            $hasSpecialCalculation =
                $hitPoints->special_calculation !== null
                && trim($hitPoints->special_calculation) !== '';

            if (
                ! $hasAverage
                && ! $hasDice
                && ! $hasSpecialCalculation
            ) {
                throw new InvalidArgumentException(
                    'È necessario indicare i PF medi, i Dadi Vita o un calcolo speciale.'
                );
            }
        });
    }

    //Attributo calcolato:
    //restituisce la formula standard dei Dadi Vita
    protected function hitDiceFormula(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                //Utilizza il calcolo speciale se non esiste una formula standard
                if (
                    $this->hit_dice_count === null
                    || $this->hit_die_size === null
                ) {
                    return $this->special_calculation;
                }

                //Crea la parte principale della formula
                $formula =
                    $this->hit_dice_count
                    . 'd'
                    . $this->hit_die_size;

                //Aggiunge un eventuale bonus
                if ($this->hit_dice_modifier > 0) {
                    return $formula
                        . ' + '
                        . $this->hit_dice_modifier;
                }

                //Aggiunge un eventuale malus
                if ($this->hit_dice_modifier < 0) {
                    return $formula
                        . ' - '
                        . abs($this->hit_dice_modifier);
                }

                //Restituisce la formula senza modificatore
                return $formula;
            }
        );
    }

    //Attributo calcolato:
    //calcola la media matematica della formula dei Dadi Vita
    protected function calculatedAverageHitPoints(): Attribute
    {
        return Attribute::make(
            get: function (): ?int {
                //Interrompe il calcolo quando mancano i dadi
                if (
                    $this->hit_dice_count === null
                    || $this->hit_die_size === null
                ) {
                    return null;
                }

                //Calcola la media e arrotonda verso il basso
                return max(
                    1,
                    (int) floor(
                        $this->hit_dice_count
                        * (($this->hit_die_size + 1) / 2)
                        + $this->hit_dice_modifier
                    )
                );
            }
        );
    }

    //Attributo calcolato:
    //usa la media pubblicata oppure quella ricavata dalla formula
    protected function effectiveAverageHitPoints(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int =>
                $this->average_hit_points
                ?? $this->calculated_average_hit_points
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni definizione dei Punti Ferita appartiene a uno stat block
    public function creatureStatBlock(): BelongsTo
    {
        return $this->belongsTo(CreatureStatBlock::class);
    }
}
