<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class SubraceMovement extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'subrace_id',
        'movement_type_id',
        'speed_meters',
        'condition',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'subrace_id' => 'integer',
            'movement_type_id' => 'integer',
            'speed_meters' => 'decimal:3',
        ];
    }

    //Registra i controlli eseguiti prima del salvataggio
    protected static function booted(): void
    {
        //Impedisce di salvare velocità nulle o negative
        static::saving(function (SubraceMovement $movement) {
            if ((float) $movement->speed_meters <= 0) {
                throw new InvalidArgumentException(
                    'La velocità della sottorazza deve essere maggiore di zero.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni movimento appartiene a una sottorazza
    public function subrace(): BelongsTo
    {
        return $this->belongsTo(Subrace::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni movimento utilizza un tipo del catalogo
    public function movementType(): BelongsTo
    {
        return $this->belongsTo(MovementType::class);
    }
}
