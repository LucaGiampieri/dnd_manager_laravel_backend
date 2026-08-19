<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ability extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'name',
        'short_name',
        'description',
    ];

    //Relazione uno-a-molti (HasMany):
    //una caratteristica può essere collegata a molte abilità
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    //Relazione uno-a-molti (HasMany):
    //una caratteristica può essere utilizzata da molti stat block
    public function creatureStatBlockAbilities(): HasMany
    {
        return $this->hasMany(
            CreatureStatBlockAbility::class
        );
    }
}
