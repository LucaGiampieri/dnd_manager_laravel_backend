<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class EffectDefinitionRollModifier extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'effect_definition_id',
        'roll_type',
        'ability_id',
        'skill_id',
        'modifier_type',
        'value',
        'dice_count',
        'die_size',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'effect_definition_id' => 'integer',
            'ability_id' => 'integer',
            'skill_id' => 'integer',
            'value' => 'float',
            'dice_count' => 'integer',
            'die_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Controlla la formula del modificatore
    protected static function booted(): void
    {
        static::saving(function (
            EffectDefinitionRollModifier $modifier
        ): void {
            //Applica l'ordine predefinito ai nuovi modelli
            $modifier->sort_order ??= 0;

            //Set, minimo e massimo richiedono sempre un valore fisso
            if (
                in_array(
                    $modifier->modifier_type,
                    [
                        'set',
                        'minimum',
                        'maximum',
                    ],
                    true
                )
                && $modifier->value === null
            ) {
                throw new InvalidArgumentException(
                    'Un modificatore numerico deve indicare un valore.'
                );
            }

            //I dati del dado devono essere indicati insieme
            $hasDiceCount = $modifier->dice_count !== null;
            $hasDieSize = $modifier->die_size !== null;

            if ($hasDiceCount !== $hasDieSize) {
                throw new InvalidArgumentException(
                    'Il numero dei dadi e il tipo di dado '
                    . 'devono essere indicati insieme.'
                );
            }

            //Sono ammessi soltanto dadi standard
            if (
                $modifier->die_size !== null
                && ! in_array(
                    $modifier->die_size,
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
                    'Il dado del modificatore non è valido.'
                );
            }

            //Bonus e penalità possono usare un valore oppure un dado
            if (
                in_array(
                    $modifier->modifier_type,
                    ['bonus', 'penalty'],
                    true
                )
                && $modifier->value === null
                && ! $hasDiceCount
            ) {
                throw new InvalidArgumentException(
                    'Un bonus o una penalità deve indicare '
                    . 'un valore oppure un dado.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni modificatore appartiene a un effetto
    public function effectDefinition(): BelongsTo
    {
        return $this->belongsTo(EffectDefinition::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //un modificatore può riguardare una caratteristica
    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //un modificatore può riguardare una abilità
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
