<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class EffectDefinitionForcedMovement extends Model
{
    //Valori iniziali condivisi dai movimenti forzati
    protected $attributes = [
        'origin_type' => 'source',
        'up_to_distance' => false,
        'straight_line' => true,
        'stops_at_obstacle' => true,
        'opportunity_attack_rule' => 'default',
        'sort_order' => 0,
    ];

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'effect_definition_id',
        'key',
        'movement_type',
        'origin_type',
        'direction_type',
        'distance',
        'up_to_distance',
        'straight_line',
        'stops_at_obstacle',
        'opportunity_attack_rule',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'effect_definition_id' => 'integer',
            'distance' => 'float',
            'up_to_distance' => 'boolean',
            'straight_line' => 'boolean',
            'stops_at_obstacle' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Controlla la distanza prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (
            EffectDefinitionForcedMovement $movement
        ): void {
            if (
                $movement->movement_type !== 'special'
                && $movement->distance === null
            ) {
                throw new InvalidArgumentException(
                    'Un movimento forzato deve indicare una distanza.'
                );
            }

            if (
                $movement->distance !== null
                && $movement->distance <= 0
            ) {
                throw new InvalidArgumentException(
                    'La distanza del movimento deve essere positiva.'
                );
            }
        });
    }

    //Relazione molti-a-uno: il movimento appartiene a un effetto
    public function effectDefinition(): BelongsTo
    {
        return $this->belongsTo(EffectDefinition::class);
    }
}
