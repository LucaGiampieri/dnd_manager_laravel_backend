<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArmorProficiencyItem extends Pivot
{
    //Indica esplicitamente la tabella pivot utilizzata
    protected $table = 'armor_proficiency_items';

    //La tabella pivot possiede una chiave primaria incrementale
    public $incrementing = true;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'armor_proficiency_id',
        'item_id',
        'notes',
    ];

    //Converte automaticamente le chiavi esterne in numeri interi
    protected function casts(): array
    {
        return [
            'armor_proficiency_id' => 'integer',
            'item_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni assegnazione appartiene a una competenza nelle armature
    public function armorProficiency(): BelongsTo
    {
        return $this->belongsTo(
            ArmorProficiency::class
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni assegnazione indica una singola armatura
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
