<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WeaponProficiencyItem extends Pivot
{
    //Indica esplicitamente la tabella pivot utilizzata
    protected $table = 'weapon_proficiency_items';

    //La tabella pivot possiede una chiave primaria incrementale
    public $incrementing = true;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'weapon_proficiency_id',
        'item_id',
        'notes',
    ];

    //Converte automaticamente le chiavi esterne in numeri interi
    protected function casts(): array
    {
        return [
            'weapon_proficiency_id' => 'integer',
            'item_id' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni assegnazione appartiene a una competenza nelle armi
    public function weaponProficiency(): BelongsTo
    {
        return $this->belongsTo(
            WeaponProficiency::class
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni assegnazione indica una singola arma
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
