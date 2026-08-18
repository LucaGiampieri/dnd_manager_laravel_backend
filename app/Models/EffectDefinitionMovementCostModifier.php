<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EffectDefinitionMovementCostModifier extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
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

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'value' => 'decimal:3',
            'sort_order' => 'integer',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni modifica appartiene a una sola definizione di effetto
    public function effectDefinition(): BelongsTo
    {
        return $this->belongsTo(EffectDefinition::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //una modifica può essere ignorata da un tipo di movimento specifico
    public function waivedByMovementType(): BelongsTo
    {
        return $this->belongsTo(
            MovementType::class,
            'waived_by_movement_type_id'
        );
    }
}
