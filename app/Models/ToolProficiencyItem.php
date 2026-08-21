<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ToolProficiencyItem extends Pivot
{
    //Indica esplicitamente la tabella pivot utilizzata
    protected $table = 'tool_proficiency_items';

    //La tabella pivot possiede una chiave primaria incrementale
    public $incrementing = true;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'tool_proficiency_id',
        'item_id',
        'notes',
    ];

    //Converte automaticamente le chiavi esterne in numeri interi
    protected function casts(): array
    {
        return [
            'tool_proficiency_id' => 'integer',
            'item_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni assegnazione appartiene a una competenza negli strumenti
    public function toolProficiency(): BelongsTo
    {
        return $this->belongsTo(
            ToolProficiency::class
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni assegnazione indica un singolo strumento
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
