<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemContainerProfile extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'item_id',
        'capacity_weight_kg',
        'capacity_volume_liters',
        'ignores_contents_weight',
        'is_extradimensional',
        'retrieval_action',
        'dimensions',
        'living_creature_rules',
        'rupture_rules',
        'nesting_rules',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'capacity_weight_kg' => 'float',
            'capacity_volume_liters' => 'float',
            'ignores_contents_weight' => 'boolean',
            'is_extradimensional' => 'boolean',
        ];
    }

    //Controlla la coerenza del contenitore
    protected static function booted(): void
    {
        static::saving(function (
            ItemContainerProfile $profile
        ): void {
            //Applica i valori predefiniti anche ai nuovi modelli
            $profile->ignores_contents_weight ??= false;
            $profile->is_extradimensional ??= false;
            $profile->retrieval_action ??= 'object_interaction';

            //Il peso massimo deve essere positivo quando indicato
            if (
                $profile->capacity_weight_kg !== null
                && $profile->capacity_weight_kg <= 0
            ) {
                throw new InvalidArgumentException(
                    'La capacità di peso del contenitore '
                    . 'deve essere positiva.'
                );
            }

            //Il volume massimo deve essere positivo quando indicato
            if (
                $profile->capacity_volume_liters !== null
                && $profile->capacity_volume_liters <= 0
            ) {
                throw new InvalidArgumentException(
                    'La capacità di volume del contenitore '
                    . 'deve essere positiva.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni profilo appartiene a un oggetto contenitore
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
