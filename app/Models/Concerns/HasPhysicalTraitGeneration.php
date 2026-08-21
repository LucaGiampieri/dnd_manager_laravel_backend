<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait HasPhysicalTraitGeneration
{
    //Registra i controlli eseguiti prima del salvataggio
    protected static function bootHasPhysicalTraitGeneration(): void
    {
        static::saving(function (Model $physicalTrait) {
            //Controlla età, intervalli e formule
            static::validatePhysicalTrait($physicalTrait);
        });
    }

    //Calcola l'altezza utilizzando il risultato dei dadi
    public function calculateHeightCm(
        int $heightModifierRoll
    ): ?float {
        //Recupera l'altezza di partenza
        $baseHeight = $this->getAttribute(
            'base_height_cm'
        );

        //Non può calcolare l'altezza se manca il valore base
        if ($baseHeight === null) {
            return null;
        }

        //Recupera la configurazione dei dadi dell'altezza
        $heightDice = $this->diceFormula('height');

        //Senza dadi, l'altezza coincide con il valore base
        if ($heightDice === null) {
            return round((float) $baseHeight, 3);
        }

        //Controlla che il risultato sia ottenibile con i dadi indicati
        $this->validateDiceRoll(
            $heightModifierRoll,
            $heightDice['count'],
            $heightDice['size'],
            'altezza'
        );

        //Applica il risultato dei dadi all'unità di conversione
        return round(
            (float) $baseHeight
            + (
                $heightModifierRoll
                * $heightDice['unit']
            ),
            3
        );
    }

    //Calcola il peso usando dadi oppure un modificatore fisso
    public function calculateWeightKg(
        ?int $weightModifierRoll = null,
        ?int $heightModifierRoll = null
    ): ?float {
        //Recupera il peso di partenza
        $baseWeight = $this->getAttribute(
            'base_weight_kg'
        );

        //Non può calcolare il peso se manca il valore base
        if ($baseWeight === null) {
            return null;
        }

        //Recupera le due possibili configurazioni del peso
        $weightDice = $this->diceFormula('weight');

        $fixedWeightModifier = $this->getAttribute(
            'weight_modifier_fixed_kg'
        );

        //Senza modificatori, il peso coincide con il valore base
        if (
            $weightDice === null
            && $fixedWeightModifier === null
        ) {
            return round((float) $baseWeight, 3);
        }

        //Calcola l'incremento prodotto dalla formula con i dadi
        if ($weightDice !== null) {
            //Il risultato dei dadi del peso diventa obbligatorio
            if ($weightModifierRoll === null) {
                throw new InvalidArgumentException(
                    'Il calcolo del peso richiede il risultato '
                    . 'dei dadi del peso.'
                );
            }

            //Controlla che il risultato sia ottenibile con i dadi indicati
            $this->validateDiceRoll(
                $weightModifierRoll,
                $weightDice['count'],
                $weightDice['size'],
                'peso'
            );

            //Converte il risultato dei dadi in chilogrammi
            $weightIncrement = (
                $weightModifierRoll
                * $weightDice['unit']
            );
        } else {
            //Utilizza direttamente il modificatore fisso in chilogrammi
            $weightIncrement = (float) $fixedWeightModifier;
        }

        //Stabilisce se il peso dipende anche dal modificatore di altezza
        $usesHeightModifier = (bool) $this->getAttribute(
            'weight_uses_height_modifier'
        );

        //Il modificatore di altezza è obbligatorio quando richiesto
        if (
            $usesHeightModifier
            && $heightModifierRoll === null
        ) {
            throw new InvalidArgumentException(
                'Il calcolo del peso richiede il modificatore di altezza.'
            );
        }

        //Controlla il risultato dell'altezza quando la formula è locale
        if (
            $usesHeightModifier
            && $heightModifierRoll !== null
        ) {
            $heightDice = $this->diceFormula('height');

            if ($heightDice !== null) {
                $this->validateDiceRoll(
                    $heightModifierRoll,
                    $heightDice['count'],
                    $heightDice['size'],
                    'altezza'
                );
            } elseif ($heightModifierRoll < 1) {
                //Una sottorazza può utilizzare la formula
                //dell'altezza ereditata dalla razza principale
                throw new InvalidArgumentException(
                    'Il modificatore di altezza deve essere positivo.'
                );
            }
        }

        //Usa uno quando il peso non dipende dall'altezza
        $heightMultiplier = $usesHeightModifier
            ? (int) $heightModifierRoll
            : 1;

        //Applica la formula completa del peso
        return round(
            (float) $baseWeight
            + ($heightMultiplier * $weightIncrement),
            3
        );
    }

    //Attributo calcolato:
    //restituisce la formula dell'altezza in formato leggibile
    protected function heightFormula(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                //Recupera l'altezza di partenza
                $baseHeight = $this->getAttribute(
                    'base_height_cm'
                );

                //Non esiste una formula senza altezza base
                if ($baseHeight === null) {
                    return null;
                }

                //Formatta il valore base
                $formula = $this->formatNumber(
                    $baseHeight
                ) . ' cm';

                //Recupera gli eventuali dadi dell'altezza
                $heightDice = $this->diceFormula(
                    'height'
                );

                //Restituisce il valore fisso quando non sono previsti dadi
                if ($heightDice === null) {
                    return $formula;
                }

                //Aggiunge dadi e unità di conversione
                return $formula
                    . ' + '
                    . $heightDice['count']
                    . 'd'
                    . $heightDice['size']
                    . ' × '
                    . $this->formatNumber(
                        $heightDice['unit']
                    )
                    . ' cm';
            }
        );
    }

    //Attributo calcolato:
    //restituisce la formula del peso in formato leggibile
    protected function weightFormula(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                //Recupera il peso di partenza
                $baseWeight = $this->getAttribute(
                    'base_weight_kg'
                );

                //Non esiste una formula senza peso base
                if ($baseWeight === null) {
                    return null;
                }

                //Formatta il valore base
                $formula = $this->formatNumber(
                    $baseWeight
                ) . ' kg';

                //Recupera le due possibili configurazioni
                $weightDice = $this->diceFormula(
                    'weight'
                );

                $fixedWeightModifier = $this->getAttribute(
                    'weight_modifier_fixed_kg'
                );

                //Restituisce il valore base quando manca un modificatore
                if (
                    $weightDice === null
                    && $fixedWeightModifier === null
                ) {
                    return $formula;
                }

                //Aggiunge il modificatore di altezza quando richiesto
                if (
                    (bool) $this->getAttribute(
                        'weight_uses_height_modifier'
                    )
                ) {
                    $formula .= ' + modificatore altezza × ';
                } else {
                    $formula .= ' + ';
                }

                //Mostra la formula basata sui dadi
                if ($weightDice !== null) {
                    return $formula
                        . $weightDice['count']
                        . 'd'
                        . $weightDice['size']
                        . ' × '
                        . $this->formatNumber(
                            $weightDice['unit']
                        )
                        . ' kg';
                }

                //Mostra la quantità fissa già convertita in chilogrammi
                return $formula
                    . $this->formatNumber(
                        $fixedWeightModifier
                    )
                    . ' kg';
            }
        );
    }

    //Recupera e valida una formula basata sui dadi
    private function diceFormula(
        string $prefix
    ): ?array {
        //Recupera i tre componenti della formula
        $count = $this->getAttribute(
            "{$prefix}_modifier_dice_count"
        );

        $size = $this->getAttribute(
            "{$prefix}_modifier_die_size"
        );

        $unit = $this->getAttribute(
            "{$prefix}_modifier_unit_"
            . ($prefix === 'height' ? 'cm' : 'kg')
        );

        //Non è presente alcuna formula con dadi
        if (
            $count === null
            && $size === null
            && $unit === null
        ) {
            return null;
        }

        //La formula deve avere tutti i componenti
        if (
            $count === null
            || $size === null
            || $unit === null
        ) {
            throw new InvalidArgumentException(
                'La formula dei dadi deve specificare '
                . 'quantità, dado e unità.'
            );
        }

        //I componenti della formula devono essere positivi
        if (
            (int) $count < 1
            || (int) $size < 1
            || (float) $unit <= 0
        ) {
            throw new InvalidArgumentException(
                'I valori della formula dei dadi devono essere positivi.'
            );
        }

        //Restituisce i componenti nei tipi PHP corretti
        return [
            'count' => (int) $count,
            'size' => (int) $size,
            'unit' => (float) $unit,
        ];
    }

    //Controlla che un risultato sia compatibile con i dadi indicati
    private function validateDiceRoll(
        int $roll,
        int $diceCount,
        int $dieSize,
        string $label
    ): void {
        //Calcola il risultato minimo e massimo possibile
        $minimum = $diceCount;
        $maximum = $diceCount * $dieSize;

        //Rifiuta risultati impossibili
        if (
            $roll < $minimum
            || $roll > $maximum
        ) {
            throw new InvalidArgumentException(
                "Il risultato dei dadi per {$label} deve essere "
                . "compreso tra {$minimum} e {$maximum}."
            );
        }
    }

    //Esegue tutte le validazioni prima del salvataggio
    private static function validatePhysicalTrait(
        Model $physicalTrait
    ): void {
        //Controlla i valori anagrafici
        static::validatePositiveValue(
            $physicalTrait,
            'maturity_age_years',
            'L’età di maturità'
        );

        static::validatePositiveValue(
            $physicalTrait,
            'lifespan_years',
            'La durata della vita'
        );

        //Controlla i valori base delle formule
        static::validatePositiveValue(
            $physicalTrait,
            'base_height_cm',
            'L’altezza base'
        );

        static::validatePositiveValue(
            $physicalTrait,
            'base_weight_kg',
            'Il peso base'
        );

        //Controlla gli intervalli di altezza e peso
        static::validateRange(
            $physicalTrait,
            'min_height_cm',
            'max_height_cm',
            'altezza'
        );

        static::validateRange(
            $physicalTrait,
            'min_weight_kg',
            'max_weight_kg',
            'peso'
        );

        //Controlla la formula dell'altezza
        static::validateDiceConfiguration(
            $physicalTrait,
            'height',
            'base_height_cm',
            'altezza'
        );

        //Controlla la formula del peso
        static::validateWeightConfiguration(
            $physicalTrait
        );
    }

    //Controlla che un valore opzionale sia positivo
    private static function validatePositiveValue(
        Model $physicalTrait,
        string $column,
        string $label
    ): void {
        //Recupera il valore dal modello
        $value = $physicalTrait->getAttribute($column);

        //I valori assenti sono consentiti
        if ($value === null) {
            return;
        }

        //Rifiuta valori uguali o inferiori a zero
        if ((float) $value <= 0) {
            throw new InvalidArgumentException(
                "{$label} deve essere maggiore di zero."
            );
        }
    }

    //Controlla la coerenza di un intervallo minimo e massimo
    private static function validateRange(
        Model $physicalTrait,
        string $minimumColumn,
        string $maximumColumn,
        string $label
    ): void {
        //Recupera i due estremi
        $minimum = $physicalTrait->getAttribute(
            $minimumColumn
        );

        $maximum = $physicalTrait->getAttribute(
            $maximumColumn
        );

        //Controlla l'eventuale valore minimo
        if (
            $minimum !== null
            && (float) $minimum < 0
        ) {
            throw new InvalidArgumentException(
                "Il valore minimo di {$label} non può essere negativo."
            );
        }

        //Controlla l'eventuale valore massimo
        if (
            $maximum !== null
            && (float) $maximum < 0
        ) {
            throw new InvalidArgumentException(
                "Il valore massimo di {$label} non può essere negativo."
            );
        }

        //Confronta gli estremi soltanto quando sono entrambi presenti
        if (
            $minimum !== null
            && $maximum !== null
            && (float) $minimum > (float) $maximum
        ) {
            throw new InvalidArgumentException(
                "Il valore minimo di {$label} non può superare il massimo."
            );
        }
    }

    //Controlla che una formula dei dadi sia completa e valida
    private static function validateDiceConfiguration(
        Model $physicalTrait,
        string $prefix,
        string $baseColumn,
        string $label
    ): void {
        //Recupera i componenti della formula
        $count = $physicalTrait->getAttribute(
            "{$prefix}_modifier_dice_count"
        );

        $size = $physicalTrait->getAttribute(
            "{$prefix}_modifier_die_size"
        );

        $unit = $physicalTrait->getAttribute(
            "{$prefix}_modifier_unit_"
            . ($prefix === 'height' ? 'cm' : 'kg')
        );

        //Controlla se almeno un componente è presente
        $hasAnyComponent = (
            $count !== null
            || $size !== null
            || $unit !== null
        );

        //Una formula completamente assente è valida
        if (! $hasAnyComponent) {
            return;
        }

        //La formula richiede il valore base
        if (
            $physicalTrait->getAttribute(
                $baseColumn
            ) === null
        ) {
            throw new InvalidArgumentException(
                "La formula di {$label} richiede il valore base."
            );
        }

        //La formula deve specificare tutti i componenti
        if (
            $count === null
            || $size === null
            || $unit === null
        ) {
            throw new InvalidArgumentException(
                "La formula di {$label} deve specificare "
                . 'quantità, dado e unità.'
            );
        }

        //Tutti i componenti devono essere positivi
        if (
            (int) $count < 1
            || (int) $size < 1
            || (float) $unit <= 0
        ) {
            throw new InvalidArgumentException(
                "I valori della formula di {$label} devono essere positivi."
            );
        }
    }

    //Controlla la formula con dadi o quantità fissa del peso
    private static function validateWeightConfiguration(
        Model $physicalTrait
    ): void {
        //Recupera tutti i componenti possibili
        $count = $physicalTrait->getAttribute(
            'weight_modifier_dice_count'
        );

        $size = $physicalTrait->getAttribute(
            'weight_modifier_die_size'
        );

        $unit = $physicalTrait->getAttribute(
            'weight_modifier_unit_kg'
        );

        $fixed = $physicalTrait->getAttribute(
            'weight_modifier_fixed_kg'
        );

        //Controlla se è presente una configurazione con dadi
        $hasAnyDiceComponent = (
            $count !== null
            || $size !== null
            || $unit !== null
        );

        //Dadi e quantità fissa sono due alternative incompatibili
        if (
            $fixed !== null
            && $hasAnyDiceComponent
        ) {
            throw new InvalidArgumentException(
                'Il peso non può usare contemporaneamente '
                . 'dadi e modificatore fisso.'
            );
        }

        //Valida la configurazione con quantità fissa
        if ($fixed !== null) {
            if ((float) $fixed <= 0) {
                throw new InvalidArgumentException(
                    'Il modificatore fisso del peso deve essere positivo.'
                );
            }

            if (
                $physicalTrait->getAttribute(
                    'base_weight_kg'
                ) === null
            ) {
                throw new InvalidArgumentException(
                    'La formula del peso richiede il valore base.'
                );
            }

            return;
        }

        //Valida l'eventuale configurazione basata sui dadi
        static::validateDiceConfiguration(
            $physicalTrait,
            'weight',
            'base_weight_kg',
            'peso'
        );
    }

    //Formatta un numero eliminando gli zeri decimali inutili
    private function formatNumber(
        mixed $value
    ): string {
        //Mantiene fino a sei cifre decimali
        $formatted = number_format(
            (float) $value,
            6,
            '.',
            ''
        );

        //Rimuove gli zeri finali e l'eventuale punto rimasto
        return rtrim(
            rtrim($formatted, '0'),
            '.'
        );
    }
}
