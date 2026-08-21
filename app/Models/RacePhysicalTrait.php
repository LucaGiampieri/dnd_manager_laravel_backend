<?php

namespace App\Models;

use App\Models\Concerns\HasPhysicalTraitGeneration;
use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RacePhysicalTrait extends Model
{
    //Aggiunge il calcolo condiviso di altezza e peso
    use HasPhysicalTraitGeneration;

    //Aggiunge riferimenti ai manuali e relazioni con altri contenuti
    use HasSourceReferences;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'race_id',
        'maturity_age_years',
        'lifespan_years',
        'min_height_cm',
        'max_height_cm',
        'min_weight_kg',
        'max_weight_kg',
        'base_height_cm',
        'height_modifier_dice_count',
        'height_modifier_die_size',
        'height_modifier_unit_cm',
        'base_weight_kg',
        'weight_modifier_dice_count',
        'weight_modifier_die_size',
        'weight_modifier_unit_kg',
        'weight_modifier_fixed_kg',
        'weight_uses_height_modifier',
        'appearance',
        'notes',
    ];

    //Aggiunge le formule leggibili nelle conversioni in array o JSON
    protected $appends = [
        'height_formula',
        'weight_formula',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'race_id' => 'integer',
            'maturity_age_years' => 'integer',
            'lifespan_years' => 'integer',
            'min_height_cm' => 'integer',
            'max_height_cm' => 'integer',
            'min_weight_kg' => 'decimal:3',
            'max_weight_kg' => 'decimal:3',
            'base_height_cm' => 'decimal:3',
            'height_modifier_dice_count' => 'integer',
            'height_modifier_die_size' => 'integer',
            'height_modifier_unit_cm' => 'decimal:3',
            'base_weight_kg' => 'decimal:3',
            'weight_modifier_dice_count' => 'integer',
            'weight_modifier_die_size' => 'integer',
            'weight_modifier_unit_kg' => 'decimal:6',
            'weight_modifier_fixed_kg' => 'decimal:6',
            'weight_uses_height_modifier' => 'boolean',
        ];
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni configurazione fisica appartiene a una razza
    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }
}
