<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemResource extends Model
{
    //Campi valorizzabili tramite create oppure update
    protected $fillable = [
        'item_id',
        'key',
        'name',
        'resource_type',
        'maximum',
        'expended_per_use',
        'recharge_type',
        'recharge_all',
        'recharge_fixed',
        'recharge_dice_count',
        'recharge_die_size',
        'recharge_bonus',
        'empty_behavior',
        'empty_behavior_condition',
        'description',
        'sort_order',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'maximum' => 'integer',
            'expended_per_use' => 'integer',
            'recharge_all' => 'boolean',
            'recharge_fixed' => 'integer',
            'recharge_dice_count' => 'integer',
            'recharge_die_size' => 'integer',
            'recharge_bonus' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    //Controlla la coerenza della risorsa prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (ItemResource $resource): void {
            //Applica anche al modello i valori predefiniti del database
            //prima di eseguire tutti i controlli di coerenza
            $resource->resource_type =
                $resource->resource_type ?? 'charges';

            $resource->expended_per_use =
                $resource->expended_per_use ?? 1;

            $resource->recharge_type =
                $resource->recharge_type ?? 'none';

            $resource->recharge_all =
                $resource->recharge_all ?? false;

            $resource->recharge_bonus =
                $resource->recharge_bonus ?? 0;

            $resource->empty_behavior =
                $resource->empty_behavior ?? 'inactive';

            $resource->sort_order =
                $resource->sort_order ?? 0;

            //La risorsa deve possedere un valore massimo positivo
            if ($resource->maximum < 1) {
                throw new InvalidArgumentException(
                    'Il valore massimo della risorsa deve essere positivo.'
                );
            }

            //Ogni utilizzo deve consumare almeno una unità
            if ($resource->expended_per_use < 1) {
                throw new InvalidArgumentException(
                    'La quantità consumata per utilizzo deve essere positiva.'
                );
            }

            //Non è possibile consumare più del massimo disponibile
            if ($resource->expended_per_use > $resource->maximum) {
                throw new InvalidArgumentException(
                    'La quantità consumata per utilizzo non può superare '
                    . 'il valore massimo della risorsa.'
                );
            }

            //Numero e dimensione dei dadi devono essere presenti insieme
            $hasDiceCount =
                $resource->recharge_dice_count !== null;

            $hasDieSize =
                $resource->recharge_die_size !== null;

            if ($hasDiceCount !== $hasDieSize) {
                throw new InvalidArgumentException(
                    'Numero e dimensione dei dadi di recupero '
                    . 'devono essere indicati insieme.'
                );
            }

            //Controlla i valori della formula con dadi
            if ($hasDiceCount) {
                if (
                    $resource->recharge_dice_count < 1
                    || $resource->recharge_die_size < 2
                ) {
                    throw new InvalidArgumentException(
                        'La formula di recupero con dadi non è valida.'
                    );
                }
            }

            //Il bonus richiede una formula con dadi
            if (
                ! $hasDiceCount
                && $resource->recharge_bonus !== 0
            ) {
                throw new InvalidArgumentException(
                    'Il bonus di recupero può essere utilizzato '
                    . 'soltanto con una formula con dadi.'
                );
            }

            //Conta i metodi numerici di recupero configurati
            $rechargeMethods = collect([
                $resource->recharge_all,
                $resource->recharge_fixed !== null,
                $hasDiceCount,
            ])->filter()->count();

            //Non possono essere presenti più formule contemporaneamente
            if ($rechargeMethods > 1) {
                throw new InvalidArgumentException(
                    'La risorsa può utilizzare un solo metodo di recupero.'
                );
            }

            //Una risorsa senza recupero non può avere una formula
            if (
                $resource->recharge_type === 'none'
                && $rechargeMethods > 0
            ) {
                throw new InvalidArgumentException(
                    'Una risorsa senza recupero non può avere '
                    . 'una formula di recupero.'
                );
            }

            //I recuperi automatici devono indicare una quantità
            if (
                in_array(
                    $resource->recharge_type,
                    [
                        'dawn',
                        'dusk',
                        'short_rest',
                        'long_rest',
                    ],
                    true
                )
                && $rechargeMethods === 0
            ) {
                throw new InvalidArgumentException(
                    'Un recupero automatico deve indicare '
                    . 'la quantità recuperata.'
                );
            }

            //La quantità fissa recuperata deve essere positiva
            if (
                $resource->recharge_fixed !== null
                && $resource->recharge_fixed < 1
            ) {
                throw new InvalidArgumentException(
                    'La quantità fissa recuperata deve essere positiva.'
                );
            }

            //Le regole speciali di esaurimento richiedono una descrizione
            if (
                in_array(
                    $resource->empty_behavior,
                    [
                        'roll_destroy',
                        'special',
                    ],
                    true
                )
                && $resource->empty_behavior_condition === null
            ) {
                throw new InvalidArgumentException(
                    'Il comportamento speciale a risorsa esaurita '
                    . 'deve essere descritto.'
                );
            }
        });
    }

    //Restituisce la formula leggibile del recupero
    protected function rechargeFormula(): Attribute
    {
        return Attribute::get(function (): ?string {
            //Recupera completamente la risorsa
            if ($this->recharge_all) {
                return 'all';
            }

            //Recupera una quantità fissa
            if ($this->recharge_fixed !== null) {
                return (string) $this->recharge_fixed;
            }

            //Non possiede una formula con dadi
            if ($this->recharge_dice_count === null) {
                return null;
            }

            //Costruisce la formula base
            $formula =
                "{$this->recharge_dice_count}"
                . "d{$this->recharge_die_size}";

            //Aggiunge un eventuale bonus positivo
            if ($this->recharge_bonus > 0) {
                return $formula . "+{$this->recharge_bonus}";
            }

            //Aggiunge un eventuale modificatore negativo
            if ($this->recharge_bonus < 0) {
                return $formula . $this->recharge_bonus;
            }

            return $formula;
        });
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni risorsa appartiene a un singolo oggetto
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
