<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Feature extends Model
{
    //Aggiunge i riferimenti ai manuali e le relazioni tra contenuti
    use HasSourceReferences;

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'ruleset_id',
        'key',
        'name',
        'type',
        'level',
        'description',
        'max_uses',
        'recharge',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ruleset_id' => 'integer',
            'level' => 'integer',
            'max_uses' => 'integer',
        ];
    }

    //Relazione molti-a-uno:
    //ogni capacità appartiene a un regolamento
    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    //Relazione uno-a-molti:
    //una capacità può essere assegnata a più razze
    public function raceAssignments(): HasMany
    {
        return $this->hasMany(RaceFeature::class);
    }

    //Relazione molti-a-molti:
    //una capacità può essere concessa da più razze
    public function races(): BelongsToMany
    {
        return $this->belongsToMany(
            Race::class,
            'race_features'
        )
            ->withPivot([
                'level',
                'sort_order',
                'notes',
            ])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    //Relazione uno-a-molti:
    //una capacità può essere assegnata a più sottorazze
    public function subraceAssignments(): HasMany
    {
        return $this->hasMany(SubraceFeature::class);
    }

    //Relazione molti-a-molti:
    //una capacità può essere concessa da più sottorazze
    public function subraces(): BelongsToMany
    {
        return $this->belongsToMany(
            Subrace::class,
            'subrace_features'
        )
            ->withPivot([
                'level',
                'sort_order',
                'notes',
            ])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    //Relazione polimorfica uno-a-molti:
    //una capacità può produrre diversi effetti meccanici
    public function effectDefinitions(): MorphMany
    {
        return $this->morphMany(
            EffectDefinition::class,
            'source'
        )->orderBy('sort_order');
    }
}
