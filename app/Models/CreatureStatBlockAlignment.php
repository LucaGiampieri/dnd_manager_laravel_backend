<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CreatureStatBlockAlignment extends Pivot
{
    //Nome della tabella pivot gestita dal modello
    protected $table = 'creature_stat_block_alignments';

    //Indica che la tabella utilizza un ID auto-incrementale
    public $incrementing = true;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'creature_stat_block_id',
        'alignment_id',
        'is_typical',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'creature_stat_block_id' => 'integer',
            'alignment_id' => 'integer',
            'is_typical' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni collegamento appartiene a un solo stat block
    public function creatureStatBlock(): BelongsTo
    {
        return $this->belongsTo(CreatureStatBlock::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni collegamento appartiene a un solo allineamento
    public function alignment(): BelongsTo
    {
        return $this->belongsTo(Alignment::class);
    }
}
