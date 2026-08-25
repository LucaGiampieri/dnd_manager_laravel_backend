<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class EffectDefinitionDuration extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'effect_definition_id',
        'key',
        'duration_type',
        'duration_value',
        'duration_unit',
        'turn_reference',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'effect_definition_id' => 'integer',
            'duration_value' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Controlla i campi richiesti dal tipo di durata
    protected static function booted(): void
    {
        static::saving(function (
            EffectDefinitionDuration $duration
        ): void {
            $isFixed = $duration->duration_type === 'fixed';
            $hasValue = $duration->duration_value !== null;
            $hasUnit = $duration->duration_unit !== null;

            if ($isFixed && (! $hasValue || ! $hasUnit)) {
                throw new InvalidArgumentException(
                    'Una durata fissa deve indicare quantità e unità.'
                );
            }

            if ($hasValue && $duration->duration_value < 1) {
                throw new InvalidArgumentException(
                    'La quantità della durata deve essere positiva.'
                );
            }

            if ($hasValue !== $hasUnit) {
                throw new InvalidArgumentException(
                    'Quantità e unità della durata '
                    . 'devono essere indicate insieme.'
                );
            }

            if (
                in_array(
                    $duration->duration_type,
                    ['until_start_turn', 'until_end_turn'],
                    true
                )
                && $duration->turn_reference === null
            ) {
                throw new InvalidArgumentException(
                    'Una durata legata a un turno deve indicare '
                    . 'la creatura di riferimento.'
                );
            }
        });
    }

    //Relazione molti-a-uno: la durata appartiene a un effetto
    public function effectDefinition(): BelongsTo
    {
        return $this->belongsTo(EffectDefinition::class);
    }
}
