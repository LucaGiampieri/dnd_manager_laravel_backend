<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemConsumableProfile extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'item_id',
        'activation_type',
        'activation_action',
        'activation_value',
        'target_scope',
        'uses_per_item',
        'consumed_on_use',
        'leaves_container',
        'special_rules',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'activation_value' => 'integer',
            'uses_per_item' => 'integer',
            'consumed_on_use' => 'boolean',
            'leaves_container' => 'boolean',
        ];
    }

    //Controlla la coerenza del consumabile
    protected static function booted(): void
    {
        static::saving(function (
            ItemConsumableProfile $profile
        ): void {
            //Applica i valori predefiniti anche ai nuovi modelli
            $profile->activation_action ??= 'action';
            $profile->activation_value ??= 1;
            $profile->target_scope ??= 'self';
            $profile->uses_per_item ??= 1;
            $profile->consumed_on_use ??= true;
            $profile->leaves_container ??= false;

            //Il tempo di attivazione deve essere positivo
            if ($profile->activation_value < 1) {
                throw new InvalidArgumentException(
                    'Il tempo di utilizzo del consumabile '
                    . 'deve essere almeno 1.'
                );
            }

            //Ogni consumabile deve contenere almeno un utilizzo
            if ($profile->uses_per_item < 1) {
                throw new InvalidArgumentException(
                    'Un consumabile deve contenere almeno un utilizzo.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni profilo appartiene a un oggetto consumabile
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
