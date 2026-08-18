<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ruleset extends Model
{
    protected $fillable = [
        'key',
        'name',
        'edition',
        'revision',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function sourceBooks(): HasMany
    {
        return $this->hasMany(SourceBook::class);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(Condition::class);
    }

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
