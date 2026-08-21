<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class RaceSense extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'race_id',
        'sense_id',
        'range_meters',
        'is_blind_beyond_range',
        'condition',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'race_id' => 'integer',
            'sense_id' => 'integer',
            'range_meters' => 'decimal:3',
            'is_blind_beyond_range' => 'boolean',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (RaceSense $assignment) {
            //NULL rappresenta un senso privo di portata numerica
            if (
                $assignment->range_meters !== null
                && (float) $assignment->range_meters <= 0
            ) {
                throw new InvalidArgumentException(
                    'La portata del senso deve essere maggiore di zero.'
                );
            }
        });
    }

    //Ogni assegnazione appartiene a una razza
    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    //Ogni assegnazione utilizza un senso del catalogo
    public function sense(): BelongsTo
    {
        return $this->belongsTo(Sense::class);
    }
}
