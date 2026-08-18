<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;

class Sense extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'key',
        'name',
        'sort_order',
        'description',
    ];

    //Converte automaticamente l'ordine in un numero intero
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
