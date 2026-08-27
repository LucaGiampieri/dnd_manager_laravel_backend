<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class CreatureStatBlockActionDamage extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'creature_stat_block_action_id',
        'creature_stat_block_attack_id',
        'damage_type_id',
        'dice_count',
        'die_size',
        'bonus',
        'average_damage',
        'is_primary',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_action_id' => 'integer',
            'creature_stat_block_attack_id' => 'integer',
            'damage_type_id' => 'integer',
            'dice_count' => 'integer',
            'die_size' => 'integer',
            'bonus' => 'integer',
            'average_damage' => 'integer',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Controlla che numero e tipo dei dadi siano sempre coerenti
    protected static function booted(): void
    {
        static::saving(function (
            CreatureStatBlockActionDamage $damage
        ): void {
            if (
                ($damage->dice_count === null)
                !== ($damage->die_size === null)
            ) {
                throw new InvalidArgumentException(
                    'Numero e tipo dei dadi del danno devono '
                    . 'essere indicati insieme.'
                );
            }
        });
    }

    //Formula leggibile del danno base dello stat block
    protected function formula(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $formula = $this->dice_count === null
                    ? ''
                    : "{$this->dice_count}d{$this->die_size}";

                if ($this->bonus > 0) {
                    return trim($formula . " + {$this->bonus}");
                }

                if ($this->bonus < 0) {
                    return trim(
                        $formula . ' - ' . abs($this->bonus)
                    );
                }

                return $formula;
            }
        );
    }

    //Relazione molti-a-uno: il danno appartiene a una azione
    public function action(): BelongsTo
    {
        return $this->belongsTo(
            CreatureStatBlockAction::class,
            'creature_stat_block_action_id'
        );
    }

    //Relazione molti-a-uno: il danno può appartenere a un attacco
    public function attack(): BelongsTo
    {
        return $this->belongsTo(
            CreatureStatBlockAttack::class,
            'creature_stat_block_attack_id'
        );
    }

    //Relazione molti-a-uno: ogni formula usa un tipo di danno
    public function damageType(): BelongsTo
    {
        return $this->belongsTo(DamageType::class);
    }
}
