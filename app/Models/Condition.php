<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Condition extends Model
{
    use HasSourceReferences;

    protected $fillable = [
        'ruleset_id',
        'key',
        'name',
        'description',
        'is_level_based',
        'maximum_level',
    ];

    protected function casts(): array
    {
        return [
            'is_level_based' => 'boolean',
            'maximum_level' => 'integer',
        ];
    }

    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    public function levels(): HasMany
    {
        return $this->hasMany(ConditionLevel::class)
            ->orderBy('level');
    }
}
