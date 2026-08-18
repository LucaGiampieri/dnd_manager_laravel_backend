<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EffectDefinitionMovementCostModifier extends Model
{
    use HasSourceReferences;

    protected $fillable = [
        'effect_definition_id',
        'key',
        'context_key',
        'waived_by_movement_type_id',
        'cost_basis',
        'operation',
        'value',
        'condition',
        'sort_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:3',
            'sort_order' => 'integer',
        ];
    }

    public function effectDefinition(): BelongsTo
    {
        return $this->belongsTo(EffectDefinition::class);
    }

    public function waivedByMovementType(): BelongsTo
    {
        return $this->belongsTo(
            MovementType::class,
            'waived_by_movement_type_id'
        );
    }
}
