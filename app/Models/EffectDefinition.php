<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EffectDefinition extends Model
{
    protected $fillable = [
        'key',
        'name',
        'application_type',
        'ends_with_source',
        'condition',
        'description',
        'sort_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'ends_with_source' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function movementCostModifiers(): HasMany
    {
        return $this->hasMany(
            EffectDefinitionMovementCostModifier::class
        );
    }
}
