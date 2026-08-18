<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'name',
        'code',
        'value_in_copper_pieces',
        'sort_order',
        'coin_weight_kg',
        'is_common',
        'description',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'value_in_copper_pieces' => 'integer',
            'sort_order' => 'integer',
            'coin_weight_kg' => 'decimal:4',
            'is_common' => 'boolean',
        ];
    }
}
