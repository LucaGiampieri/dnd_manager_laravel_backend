<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;

class CreatureTag extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'key',
        'name',
        'description',
        'notes',
        'sort_order',
    ];

    //Converte automaticamente l'ordine in un numero intero
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
