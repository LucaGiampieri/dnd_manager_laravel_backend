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
    //un regolamento può contenere molti manuali
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

    //Relazione polimorfica uno-a-molti (MorphMany):
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
}
