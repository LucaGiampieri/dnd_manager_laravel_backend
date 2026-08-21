<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreatureType extends Model
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

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    //Relazione uno-a-molti (HasMany):
    //un tipo di creatura può essere utilizzato da molte razze
    public function races(): HasMany
    {
        return $this->hasMany(Race::class)
            ->orderBy('sort_order');
    }
}
