<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpellSchool extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'key',
        'name',
        'description',
    ];

    //Relazione uno-a-molti (HasMany):
    //una scuola può comprendere molti incantesimi
    public function spells(): HasMany
    {
        return $this->hasMany(Spell::class);
    }
}
