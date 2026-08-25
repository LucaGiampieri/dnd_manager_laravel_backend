<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use InvalidArgumentException;

class EffectDefinitionHealing extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'effect_definition_id',
        'key',
        'healing_type',
        'dice_count',
        'die_size',
        'flat_bonus',
        'modifier_source_type',
        'modifier_ability_id',
        'modifier_multiplier',
        'average_healing',
        'temporary_hit_point_rule',
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
            'dice_count' => 'integer',
            'die_size' => 'integer',
            'flat_bonus' => 'float',
            'modifier_ability_id' => 'integer',
            'modifier_multiplier' => 'float',
            'average_healing' => 'float',
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Controlla la formula di guarigione prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (
            EffectDefinitionHealing $healing
        ): void {
            //Applica i valori predefiniti anche ai nuovi modelli
            $healing->healing_type ??= 'hit_points';
            $healing->flat_bonus ??= 0;
            $healing->modifier_source_type ??= 'none';
            $healing->modifier_multiplier ??= 1;
            $healing->is_primary ??= false;
            $healing->sort_order ??= 0;

            //I dati della formula con dadi devono essere completi
            $hasDiceCount = $healing->dice_count !== null;
            $hasDieSize = $healing->die_size !== null;

            if ($hasDiceCount !== $hasDieSize) {
                throw new InvalidArgumentException(
                    'Il numero dei dadi e il tipo di dado '
                    . 'devono essere indicati insieme.'
                );
            }

            //Il numero dei dadi deve essere positivo
            if (
                $healing->dice_count !== null
                && $healing->dice_count < 1
            ) {
                throw new InvalidArgumentException(
                    'La formula di guarigione deve utilizzare '
                    . 'almeno un dado.'
                );
            }

            //Sono ammessi soltanto i dadi standard
            if (
                $healing->die_size !== null
                && ! in_array(
                    $healing->die_size,
                    [
                        4,
                        6,
                        8,
                        10,
                        12,
                        20,
                        100,
                    ],
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'Il dado di guarigione indicato non è valido.'
                );
            }

            //Una guarigione non può avere un bonus fisso negativo
            if ($healing->flat_bonus < 0) {
                throw new InvalidArgumentException(
                    'Il bonus fisso di guarigione non può essere negativo.'
                );
            }

            //La formula deve produrre almeno un valore di guarigione
            if (
                ! $hasDiceCount
                && (float) $healing->flat_bonus === 0.0
                && $healing->modifier_source_type === 'none'
            ) {
                throw new InvalidArgumentException(
                    'La guarigione deve indicare dadi, bonus fisso '
                    . 'o un modificatore.'
                );
            }
        });

        static::deleting(function (
            EffectDefinitionHealing $healing
        ): void {
            //Le progressioni polimorfiche non possiedono una FK
            $healing->scalings()->delete();
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni guarigione appartiene a un effetto
    public function effectDefinition(): BelongsTo
    {
        return $this->belongsTo(EffectDefinition::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //una guarigione può utilizzare una caratteristica
    public function modifierAbility(): BelongsTo
    {
        return $this->belongsTo(
            Ability::class,
            'modifier_ability_id'
        );
    }

    //Relazione polimorfica: una cura può avere più progressioni
    public function scalings(): MorphMany
    {
        return $this->morphMany(
            EffectDefinitionScaling::class,
            'scalable'
        )->orderBy('sort_order');
    }
}
