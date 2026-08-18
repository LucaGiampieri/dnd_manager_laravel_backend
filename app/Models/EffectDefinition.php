<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EffectDefinition extends Model
{
    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
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

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'ends_with_source' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Relazione polimorfica molti-a-uno (MorphTo):
    //ogni effetto può essere definito da una fonte di tipo variabile
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    //Relazione uno-a-molti (HasMany):
    //un effetto può applicare molte modifiche ai costi di movimento
    public function movementCostModifiers(): HasMany
    {
        return $this->hasMany(
            EffectDefinitionMovementCostModifier::class
        );
    }
}
