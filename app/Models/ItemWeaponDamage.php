<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemWeaponDamage extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'item_weapon_profile_id',
        'damage_type_id',
        'dice_count',
        'die_size',
        'bonus',
        'primary',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_weapon_profile_id' => 'integer',
            'damage_type_id' => 'integer',
            'dice_count' => 'integer',
            'die_size' => 'integer',
            'bonus' => 'integer',
            'primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (
            ItemWeaponDamage $damage
        ): void {
            //Controlla se è stato indicato il numero dei dadi
            $hasDiceCount = $damage->dice_count !== null;

            //Controlla se è stata indicata la dimensione del dado
            $hasDieSize = $damage->die_size !== null;

            //Numero e dimensione del dado devono essere indicati insieme
            if ($hasDiceCount !== $hasDieSize) {
                throw new InvalidArgumentException(
                    'Il numero dei dadi e la dimensione del dado '
                    . 'devono essere indicati insieme.'
                );
            }

            //Controlla una normale formula basata sui dadi
            if ($hasDiceCount && $hasDieSize) {
                if ($damage->dice_count < 1) {
                    throw new InvalidArgumentException(
                        'Il numero di dadi del danno deve essere '
                        . 'almeno 1.'
                    );
                }

                if ($damage->die_size < 2) {
                    throw new InvalidArgumentException(
                        'Il dado del danno deve avere almeno 2 facce.'
                    );
                }

                return;
            }

            //Senza dadi deve essere presente un danno fisso positivo
            if ($damage->bonus < 1) {
                throw new InvalidArgumentException(
                    'Un danno privo di dadi deve possedere '
                    . 'un valore fisso positivo.'
                );
            }
        });
    }

    //Attributo calcolato:
    //restituisce la formula del danno in formato leggibile
    protected function formula(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                //Un danno senza dadi utilizza soltanto il valore fisso
                if ($this->dice_count === null) {
                    return (string) $this->bonus;
                }

                //Costruisce la parte della formula basata sui dadi
                $formula = "{$this->dice_count}d{$this->die_size}";

                //Aggiunge un eventuale bonus positivo
                if ($this->bonus > 0) {
                    return "{$formula}+{$this->bonus}";
                }

                //Aggiunge direttamente un eventuale malus
                if ($this->bonus < 0) {
                    return "{$formula}{$this->bonus}";
                }

                return $formula;
            }
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni danno appartiene a un profilo da arma
    public function weaponProfile(): BelongsTo
    {
        return $this->belongsTo(
            ItemWeaponProfile::class,
            'item_weapon_profile_id'
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni componente utilizza un tipo di danno
    public function damageType(): BelongsTo
    {
        return $this->belongsTo(DamageType::class);
    }
}
