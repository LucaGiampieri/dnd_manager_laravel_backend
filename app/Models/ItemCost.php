<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCost extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'item_id',
        'currency_id',
        'amount',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'currency_id' => 'integer',
            'amount' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni prezzo appartiene a un oggetto
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni prezzo utilizza una valuta
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
