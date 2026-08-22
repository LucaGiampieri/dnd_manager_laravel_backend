<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemSpellCasting extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'item_id',
        'spell_id',
        'item_resource_id',
        'key',
        'activation_type',
        'activation_value',
        'resource_cost',
        'cast_at_level',
        'save_dc',
        'spell_attack_bonus',
        'requires_components',
        'requires_concentration',
        'condition',
        'description',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'spell_id' => 'integer',
            'item_resource_id' => 'integer',
            'activation_value' => 'integer',
            'resource_cost' => 'integer',
            'cast_at_level' => 'integer',
            'save_dc' => 'integer',
            'spell_attack_bonus' => 'integer',
            'requires_components' => 'boolean',
            'requires_concentration' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Controlla la coerenza del lancio prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (ItemSpellCasting $casting): void {
            //Applica anche ai nuovi modelli i valori predefiniti del database
            $casting->activation_type ??= 'spell_casting_time';
            $casting->activation_value ??= 1;
            $casting->resource_cost ??= 0;
            $casting->requires_components ??= false;
            $casting->sort_order ??= 0;

            //Il tempo di attivazione deve essere positivo
            if ($casting->activation_value < 1) {
                throw new InvalidArgumentException(
                    'Il tempo di attivazione deve essere almeno 1.'
                );
            }

            //Una risorsa è obbligatoria quando il lancio ha un costo
            if (
                $casting->item_resource_id === null
                && $casting->resource_cost !== 0
            ) {
                throw new InvalidArgumentException(
                    'Un lancio con un costo deve indicare la risorsa '
                    . 'dell’oggetto che viene consumata.'
                );
            }

            //Una risorsa collegata deve avere un costo positivo
            if (
                $casting->item_resource_id !== null
                && $casting->resource_cost < 1
            ) {
                throw new InvalidArgumentException(
                    'Un lancio collegato a una risorsa deve consumarne '
                    . 'almeno una unità.'
                );
            }

            //Controlla che la risorsa appartenga allo stesso oggetto
            if ($casting->item_resource_id !== null) {
                $resource = ItemResource::query()
                    ->find($casting->item_resource_id);

                if (
                    $resource === null
                    || $resource->item_id !== $casting->item_id
                ) {
                    throw new InvalidArgumentException(
                        'La risorsa utilizzata dal lancio deve appartenere '
                        . 'allo stesso oggetto.'
                    );
                }

                //Il costo non può superare il massimo della risorsa
                if ($casting->resource_cost > $resource->maximum) {
                    throw new InvalidArgumentException(
                        'Il costo del lancio non può superare il valore '
                        . 'massimo della risorsa.'
                    );
                }
            }

            //Recupera l'incantesimo utilizzato dal lancio
            $spell = Spell::query()->find($casting->spell_id);

            if ($spell === null) {
                throw new InvalidArgumentException(
                    'L’incantesimo indicato non è stato trovato.'
                );
            }

            //Il livello scelto non può essere inferiore a quello base
            if (
                $casting->cast_at_level !== null
                && $casting->cast_at_level < $spell->level
            ) {
                throw new InvalidArgumentException(
                    'Il livello di lancio non può essere inferiore '
                    . 'al livello base dell’incantesimo.'
                );
            }

            //Gli incantesimi della quinta edizione arrivano al livello 9
            if (
                $casting->cast_at_level !== null
                && $casting->cast_at_level > 9
            ) {
                throw new InvalidArgumentException(
                    'Il livello di lancio deve essere compreso tra '
                    . 'il livello base e 9.'
                );
            }

            //Controlla l'intervallo della CD fissa
            if (
                $casting->save_dc !== null
                && (
                    $casting->save_dc < 1
                    || $casting->save_dc > 30
                )
            ) {
                throw new InvalidArgumentException(
                    'La CD dell’oggetto deve essere compresa tra 1 e 30.'
                );
            }

            //Controlla l'intervallo del bonus di attacco
            if (
                $casting->spell_attack_bonus !== null
                && (
                    $casting->spell_attack_bonus < -30
                    || $casting->spell_attack_bonus > 30
                )
            ) {
                throw new InvalidArgumentException(
                    'Il bonus di attacco dell’oggetto deve essere '
                    . 'compreso tra -30 e 30.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni lancio appartiene a un oggetto
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni lancio utilizza un incantesimo
    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //un lancio può consumare una risorsa dell'oggetto
    public function resource(): BelongsTo
    {
        return $this->belongsTo(
            ItemResource::class,
            'item_resource_id'
        );
    }
}
