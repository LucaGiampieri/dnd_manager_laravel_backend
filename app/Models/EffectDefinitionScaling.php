<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InvalidArgumentException;

class EffectDefinitionScaling extends Model
{
    //Valori iniziali delle operazioni di progressione
    protected $attributes = [
        'operation' => 'add',
        'source_offset' => 0,
        'multiplier' => 1,
        'divisor' => 1,
        'flat_value' => 0,
        'rounding' => 'none',
        'sort_order' => 0,
    ];

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'key',
        'target_field',
        'source_type',
        'class_id',
        'ability_id',
        'operation',
        'minimum_source',
        'maximum_source',
        'fixed_value',
        'source_offset',
        'multiplier',
        'divisor',
        'flat_value',
        'rounding',
        'minimum_result',
        'maximum_result',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'class_id' => 'integer',
            'ability_id' => 'integer',
            'minimum_source' => 'float',
            'maximum_source' => 'float',
            'fixed_value' => 'float',
            'source_offset' => 'float',
            'multiplier' => 'float',
            'divisor' => 'float',
            'flat_value' => 'float',
            'minimum_result' => 'float',
            'maximum_result' => 'float',
            'sort_order' => 'integer',
        ];
    }

    //Controlla che la regola matematica sia coerente
    protected static function booted(): void
    {
        static::saving(function (
            EffectDefinitionScaling $scaling
        ): void {
            if ((float) $scaling->divisor === 0.0) {
                throw new InvalidArgumentException(
                    'Il divisore della progressione non può essere zero.'
                );
            }

            if (
                $scaling->minimum_source !== null
                && $scaling->maximum_source !== null
                && $scaling->minimum_source > $scaling->maximum_source
            ) {
                throw new InvalidArgumentException(
                    'Il valore minimo della sorgente non può superare '
                    . 'il valore massimo.'
                );
            }

            if (
                $scaling->minimum_result !== null
                && $scaling->maximum_result !== null
                && $scaling->minimum_result > $scaling->maximum_result
            ) {
                throw new InvalidArgumentException(
                    'Il risultato minimo non può superare '
                    . 'il risultato massimo.'
                );
            }

            if (
                $scaling->source_type === 'fixed'
                && $scaling->fixed_value === null
            ) {
                throw new InvalidArgumentException(
                    'Una progressione fissa deve indicare il suo valore.'
                );
            }

            if (
                $scaling->source_type === 'class_level'
                && $scaling->class_id === null
            ) {
                throw new InvalidArgumentException(
                    'Una progressione per livello di classe '
                    . 'deve indicare la classe.'
                );
            }

            if (
                in_array(
                    $scaling->source_type,
                    ['ability_score', 'ability_modifier'],
                    true
                )
                && $scaling->ability_id === null
            ) {
                throw new InvalidArgumentException(
                    'Una progressione basata su una caratteristica '
                    . 'deve indicare la caratteristica.'
                );
            }
        });
    }

    //Relazione polimorfica: identifica la formula modificata
    public function scalable(): MorphTo
    {
        return $this->morphTo();
    }

    //Relazione molti-a-uno: classe usata dalla progressione
    public function characterClass(): BelongsTo
    {
        return $this->belongsTo(
            CharacterClass::class,
            'class_id'
        );
    }

    //Relazione molti-a-uno: caratteristica usata dalla progressione
    public function ability(): BelongsTo
    {
        return $this->belongsTo(Ability::class);
    }
}
