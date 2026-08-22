<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class SpellMaterialComponent extends Model
{
    //Valori iniziali usati anche durante le validazioni del modello
    protected $attributes = [
        'cost_is_minimum' => false,
        'consumed' => false,
        'focus_replaceable' => true,
        'sort_order' => 0,
    ];

    //Campi valorizzabili tramite create o update
    protected $fillable = [
        'spell_id',
        'key',
        'name',
        'description',
        'quantity',
        'unit',
        'cost_amount',
        'currency_id',
        'cost_is_minimum',
        'consumed',
        'focus_replaceable',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'spell_id' => 'integer',
            'quantity' => 'decimal:3',
            'cost_amount' => 'decimal:2',
            'currency_id' => 'integer',
            'cost_is_minimum' => 'boolean',
            'consumed' => 'boolean',
            'focus_replaceable' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Controlla la coerenza di quantità, costo e sostituzione
    protected static function booted(): void
    {
        static::saving(function (
            SpellMaterialComponent $component
        ): void {
            //Una quantità presente deve essere positiva
            if (
                $component->quantity !== null
                && (float) $component->quantity <= 0
            ) {
                throw new InvalidArgumentException(
                    'La quantità del componente deve essere positiva.'
                );
            }

            $hasQuantity = $component->quantity !== null;
            $hasUnit = $component->unit !== null;

            //Quantità e unità devono essere indicate insieme
            if ($hasQuantity !== $hasUnit) {
                throw new InvalidArgumentException(
                    'La quantità del componente e la relativa unità '
                    . 'devono essere indicate insieme.'
                );
            }

            $hasCost = $component->cost_amount !== null;
            $hasCurrency = $component->currency_id !== null;

            //Costo e valuta devono essere indicati insieme
            if ($hasCost !== $hasCurrency) {
                throw new InvalidArgumentException(
                    'Il costo del componente e la valuta devono essere '
                    . 'indicati insieme.'
                );
            }

            //Un costo presente deve essere positivo
            if (
                $component->cost_amount !== null
                && (float) $component->cost_amount <= 0
            ) {
                throw new InvalidArgumentException(
                    'Il costo del componente deve essere positivo.'
                );
            }

            //La regola del costo minimo richiede un costo
            if (! $hasCost && $component->cost_is_minimum) {
                throw new InvalidArgumentException(
                    'La regola sul costo minimo richiede un costo.'
                );
            }

            //Un componente costoso o consumato non può usare un focus
            if (
                ($hasCost || $component->consumed)
                && $component->focus_replaceable
            ) {
                throw new InvalidArgumentException(
                    'Un componente costoso o consumato non può essere '
                    . 'sostituito da un focus.'
                );
            }
        });
    }

    //Relazione molti-a-uno:
    //il componente appartiene a un incantesimo
    public function spell(): BelongsTo
    {
        return $this->belongsTo(Spell::class);
    }

    //Relazione molti-a-uno:
    //il costo del componente utilizza una valuta del catalogo
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
