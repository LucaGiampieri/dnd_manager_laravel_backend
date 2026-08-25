<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use InvalidArgumentException;

class EffectDefinitionDamage extends Model
{
    //Valori iniziali condivisi dalle formule di danno
    protected $attributes = [
        'flat_bonus' => 0,
        'modifier_source_type' => 'none',
        'modifier_multiplier' => 1,
        'is_primary' => false,
        'sort_order' => 0,
    ];

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'effect_definition_id',
        'key',
        'damage_type_id',
        'dice_count',
        'die_size',
        'flat_bonus',
        'modifier_source_type',
        'modifier_ability_id',
        'modifier_multiplier',
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
            'effect_definition_id' => 'integer',
            'damage_type_id' => 'integer',
            'dice_count' => 'integer',
            'die_size' => 'integer',
            'flat_bonus' => 'float',
            'modifier_ability_id' => 'integer',
            'modifier_multiplier' => 'float',
            'average_damage' => 'float',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Controlla la formula e pulisce le progressioni polimorfiche
    protected static function booted(): void
    {
        static::saving(function (
            EffectDefinitionDamage $damage
        ): void {
            $hasDiceCount = $damage->dice_count !== null;
            $hasDieSize = $damage->die_size !== null;

            //Numero e dimensione del dado devono comparire insieme
            if ($hasDiceCount !== $hasDieSize) {
                throw new InvalidArgumentException(
                    'Il numero dei dadi e il tipo di dado del danno '
                    . 'devono essere indicati insieme.'
                );
            }

            if ($hasDiceCount && $damage->dice_count < 1) {
                throw new InvalidArgumentException(
                    'La formula del danno deve utilizzare almeno un dado.'
                );
            }

            //Accetta soltanto i dadi standard utilizzati dal regolamento
            if (
                $hasDieSize
                && ! in_array(
                    $damage->die_size,
                    [4, 6, 8, 10, 12, 20, 100],
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'Il dado del danno indicato non è valido.'
                );
            }

            //Una formula deve contenere almeno una componente numerica
            if (
                ! $hasDiceCount
                && (float) $damage->flat_bonus === 0.0
                && $damage->modifier_source_type === 'none'
            ) {
                throw new InvalidArgumentException(
                    'Il danno deve indicare dadi, bonus fisso '
                    . 'o un modificatore.'
                );
            }

            //Una caratteristica esplicita richiede un modificatore
            if (
                $damage->modifier_ability_id !== null
                && $damage->modifier_source_type === 'none'
            ) {
                throw new InvalidArgumentException(
                    'La caratteristica del danno richiede '
                    . 'una origine del modificatore.'
                );
            }
        });

        static::deleting(function (
            EffectDefinitionDamage $damage
        ): void {
            //Le progressioni sono polimorfiche e non possiedono una FK
            $damage->scalings()->delete();
        });
    }

    //Formula leggibile utilizzabile dalle API e dalle interfacce
    protected function formula(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $parts = [];

                if ($this->dice_count !== null) {
                    $parts[] = "{$this->dice_count}d{$this->die_size}";
                }

                if ((float) $this->flat_bonus !== 0.0) {
                    $bonus = (float) $this->flat_bonus;
                    $parts[] = $bonus > 0 ? "+{$bonus}" : (string) $bonus;
                }

                if ($this->modifier_source_type !== 'none') {
                    $multiplier = (float) $this->modifier_multiplier;
                    $parts[] = $multiplier === 1.0
                        ? '+modificatore'
                        : "+{$multiplier}×modificatore";
                }

                return implode(' ', $parts);
            }
        );
    }

    //Relazione molti-a-uno: ogni danno appartiene a un effetto
    public function effectDefinition(): BelongsTo
    {
        return $this->belongsTo(EffectDefinition::class);
    }

    //Relazione molti-a-uno: ogni formula usa un tipo di danno
    public function damageType(): BelongsTo
    {
        return $this->belongsTo(DamageType::class);
    }

    //Relazione molti-a-uno: il danno può usare una caratteristica
    public function modifierAbility(): BelongsTo
    {
        return $this->belongsTo(
            Ability::class,
            'modifier_ability_id'
        );
    }

    //Relazione polimorfica: una formula può avere molte progressioni
    public function scalings(): MorphMany
    {
        return $this->morphMany(
            EffectDefinitionScaling::class,
            'scalable'
        )->orderBy('sort_order');
    }
}
