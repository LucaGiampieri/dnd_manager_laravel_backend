<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatureStatBlockMovement extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'creature_stat_block_id',
        'movement_type_id',
        'speed',
        'can_hover',
        'condition',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_id' => 'integer',
            'movement_type_id' => 'integer',
            'speed' => 'float',
            'can_hover' => 'boolean',
        ];
    }

    //Relazione molti-a-uno: il movimento appartiene a uno stat block
    public function creatureStatBlock(): BelongsTo
    {
        return $this->belongsTo(CreatureStatBlock::class);
    }

    //Relazione molti-a-uno: ogni velocità usa un tipo di movimento
    public function movementType(): BelongsTo
    {
        return $this->belongsTo(MovementType::class);
    }
}
