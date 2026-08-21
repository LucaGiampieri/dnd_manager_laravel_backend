<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemType extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'key',
        'name',
        'description',
        'sort_order',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    //Relazione uno-a-molti (HasMany):
    //una tipologia può comprendere molti oggetti
    public function items(): HasMany
    {
        return $this->hasMany(Item::class)
            ->orderBy('sort_order')
            ->orderBy('name');
    }
}
