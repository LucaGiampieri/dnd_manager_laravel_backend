<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ruleset extends Model
{
    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'key',
        'name',
        'edition',
        'revision',
        'description',
        'is_active',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    //Relazione uno-a-molti (HasMany):
    //un regolamento può comprendere molti manuali
    public function sourceBooks(): HasMany
    {
        return $this->hasMany(SourceBook::class);
    }

    //Relazione uno-a-molti (HasMany):
    //un regolamento può definire molte condizioni
    public function conditions(): HasMany
    {
        return $this->hasMany(Condition::class);
    }

    //Relazione uno-a-molti polimorfica (MorphMany):
    //un regolamento può essere la fonte di molte definizioni di effetto
    public function effectDefinitions(): MorphMany
    {
        return $this->morphMany(
            EffectDefinition::class,
            'source'
        );
    }

    //Relazione uno-a-molti (HasMany):
    //un regolamento può contenere molti allineamenti
    public function alignments(): HasMany
    {
        return $this->hasMany(Alignment::class);
    }

    //Relazione uno-a-molti (HasMany):
    //un regolamento può definire molti gradi di sfida
    public function challengeRatings(): HasMany
    {
        return $this->hasMany(ChallengeRating::class)
            ->orderBy('sort_order');
    }

    //Relazione uno-a-molti (HasMany):
    //un regolamento può comprendere molte razze e stirpi
    public function races(): HasMany
    {
        return $this->hasMany(Race::class)
            ->orderBy('sort_order');
    }

    //Relazione uno-a-molti (HasMany):
    //un regolamento può definire molte regole opzionali
    public function optionalRules(): HasMany
    {
        return $this->hasMany(OptionalRule::class)
            ->orderBy('sort_order');
    }

    //Relazione uno-a-molti:
    //un regolamento può definire molte capacità
    public function features(): HasMany
    {
        return $this->hasMany(Feature::class)
            ->orderBy('name');
    }
}
