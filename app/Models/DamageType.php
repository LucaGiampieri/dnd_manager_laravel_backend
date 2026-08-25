<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DamageType extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'name',
        'description',
    ];

    //Relazione uno-a-molti: un tipo può essere usato da molti effetti
    public function effectDefinitionDamages(): HasMany
    {
        return $this->hasMany(EffectDefinitionDamage::class);
    }
}
