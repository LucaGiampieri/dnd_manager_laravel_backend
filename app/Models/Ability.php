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

    //Relazione uno-a-molti (HasMany):
    //una caratteristica può ricevere bonus da molte razze
    public function raceAbilityBonuses(): HasMany
    {
        return $this->hasMany(RaceAbilityBonus::class);
    }

    //Relazione uno-a-molti (HasMany):
    //una caratteristica può ricevere bonus da molte sottorazze
    public function subraceAbilityBonuses(): HasMany
    {
        return $this->hasMany(SubraceAbilityBonus::class);
    }

    //Relazione uno-a-molti (HasMany):
    //una caratteristica può essere proposta da molte scelte razziali
    public function raceChoiceOptions(): HasMany
    {
        return $this->hasMany(
            RaceChoiceOption::class,
            'option_id'
        )
            ->where('option_type', 'ability');
    }

    //Relazione uno-a-molti (HasMany):
    //una caratteristica può essere proposta da molte scelte di sottorazza
    public function subraceChoiceOptions(): HasMany
    {
        return $this->hasMany(
            SubraceChoiceOption::class,
            'option_id'
        )
            ->where('option_type', 'ability');
    }
}
