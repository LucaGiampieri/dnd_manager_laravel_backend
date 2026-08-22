<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemMagicApplicability extends Model
{
    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'item_id',
        'key',
        'target_scope',
        'target_item_id',
        'target_item_type_id',
        'weapon_category',
        'armor_category',
        'requires_nonmagical',
        'condition',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'target_item_id' => 'integer',
            'target_item_type_id' => 'integer',
            'requires_nonmagical' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Controlla che l'ambito utilizzi soltanto i campi necessari
    protected static function booted(): void
    {
        static::saving(function (
            ItemMagicApplicability $applicability
        ): void {
            //Applica i valori predefiniti anche ai nuovi modelli
            $applicability->requires_nonmagical ??= true;
            $applicability->sort_order ??= 0;

            //Recupera l'oggetto magico proprietario
            $magicItem = Item::query()
                ->find($applicability->item_id);

            //Una regola di applicabilità appartiene a un oggetto magico
            if (
                $magicItem === null
                || ! $magicItem->is_magical
            ) {
                throw new InvalidArgumentException(
                    'Una regola di applicabilità deve appartenere '
                    . 'a un oggetto magico.'
                );
            }

            //Conta i riferimenti specifici compilati
            $specificValues = array_filter([
                'target_item_id' =>
                    $applicability->target_item_id,
                'target_item_type_id' =>
                    $applicability->target_item_type_id,
                'weapon_category' =>
                    $applicability->weapon_category,
                'armor_category' =>
                    $applicability->armor_category,
            ], fn ($value) => $value !== null);

            //Gli ambiti generici non devono indicare riferimenti specifici
            if (
                in_array(
                    $applicability->target_scope,
                    [
                        'any_weapon',
                        'any_armor',
                        'special',
                    ],
                    true
                )
                && $specificValues !== []
            ) {
                throw new InvalidArgumentException(
                    'Un ambito generico non può indicare '
                    . 'un oggetto o una categoria specifica.'
                );
            }

            //Un oggetto specifico richiede soltanto target_item_id
            if (
                $applicability->target_scope === 'specific_item'
                && (
                    $applicability->target_item_id === null
                    || count($specificValues) !== 1
                )
            ) {
                throw new InvalidArgumentException(
                    'L’ambito specific_item deve indicare '
                    . 'un solo oggetto base.'
                );
            }

            //Una tipologia richiede soltanto target_item_type_id
            if (
                $applicability->target_scope === 'item_type'
                && (
                    $applicability->target_item_type_id === null
                    || count($specificValues) !== 1
                )
            ) {
                throw new InvalidArgumentException(
                    'L’ambito item_type deve indicare '
                    . 'una sola tipologia di oggetto.'
                );
            }

            //Una categoria di arma richiede weapon_category
            if (
                $applicability->target_scope === 'weapon_category'
                && (
                    $applicability->weapon_category === null
                    || count($specificValues) !== 1
                )
            ) {
                throw new InvalidArgumentException(
                    'L’ambito weapon_category deve indicare '
                    . 'una sola categoria di arma.'
                );
            }

            //Una categoria di armatura richiede armor_category
            if (
                $applicability->target_scope === 'armor_category'
                && (
                    $applicability->armor_category === null
                    || count($specificValues) !== 1
                )
            ) {
                throw new InvalidArgumentException(
                    'L’ambito armor_category deve indicare '
                    . 'una sola categoria di armatura.'
                );
            }
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni regola appartiene a un oggetto magico
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //una regola può indicare un preciso oggetto base
    public function targetItem(): BelongsTo
    {
        return $this->belongsTo(
            Item::class,
            'target_item_id'
        );
    }

    //Relazione molti-a-uno (BelongsTo):
    //una regola può indicare una tipologia di oggetto
    public function targetItemType(): BelongsTo
    {
        return $this->belongsTo(
            ItemType::class,
            'target_item_type_id'
        );
    }
}
