<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class RaceMovement extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'race_id',
        'movement_type_id',
        'speed_meters',
        'condition',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'race_id' => 'integer',
            'movement_type_id' => 'integer',
            'speed_meters' => 'decimal:3',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        //Impedisce di salvare velocità nulle o negative
        static::saving(function (RaceMovement $movement) {
            if ((float) $movement->speed_meters <= 0) {
                throw new InvalidArgumentException(
                    'La velocità razziale deve essere maggiore di zero.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni movimento appartiene a una razza
    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni movimento utilizza un tipo del catalogo
    public function movementType(): BelongsTo
    {
        return $this->belongsTo(MovementType::class);
    }
}
