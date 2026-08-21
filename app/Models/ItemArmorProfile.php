<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemArmorProfile extends Model
{
    //Campi valorizzabili tramite create oppure update
    protected $fillable = [
        'item_id',
        'armor_category',
        'armor_class_operation',
        'armor_class_value',
        'dexterity_modifier',
        'max_dexterity_bonus',
        'requirement_ability_id',
        'minimum_ability_score',
        'stealth_disadvantage',
        'don_time_minutes',
        'don_time_actions',
        'doff_time_minutes',
        'doff_time_actions',
        'notes',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'armor_class_value' => 'integer',
            'max_dexterity_bonus' => 'integer',
            'requirement_ability_id' => 'integer',
            'minimum_ability_score' => 'integer',
            'stealth_disadvantage' => 'boolean',
            'don_time_minutes' => 'integer',
            'don_time_actions' => 'integer',
            'doff_time_minutes' => 'integer',
            'doff_time_actions' => 'integer',
        ];
    }

    //Controlla la coerenza del profilo prima del salvataggio
    protected static function booted(): void
    {
        static::saving(function (ItemArmorProfile $profile): void {
            //La Classe Armatura deve sempre avere un valore positivo
            if ($profile->armor_class_value < 1) {
                throw new InvalidArgumentException(
                    'Il valore della Classe Armatura deve essere positivo.'
                );
            }

            //Un modificatore limitato deve indicare il limite massimo
            if (
                $profile->dexterity_modifier === 'capped'
                && $profile->max_dexterity_bonus === null
            ) {
                throw new InvalidArgumentException(
                    'Una armatura con Destrezza limitata deve indicare '
                    . 'il bonus massimo applicabile.'
                );
            }

            //Il limite non è applicabile agli altri tipi di modificatore
            if (
                $profile->dexterity_modifier !== 'capped'
                && $profile->max_dexterity_bonus !== null
            ) {
                throw new InvalidArgumentException(
                    'Il bonus massimo di Destrezza può essere utilizzato '
                    . 'soltanto con il modificatore capped.'
                );
            }

            //Controlla la presenza completa del requisito
            $hasRequirementAbility =
                $profile->requirement_ability_id !== null;

            $hasMinimumScore =
                $profile->minimum_ability_score !== null;

            if ($hasRequirementAbility !== $hasMinimumScore) {
                throw new InvalidArgumentException(
                    'La caratteristica richiesta e il suo punteggio minimo '
                    . 'devono essere indicati insieme.'
                );
            }

            //Il requisito deve rispettare i limiti delle caratteristiche
            if (
                $profile->minimum_ability_score !== null
                && (
                    $profile->minimum_ability_score < 1
                    || $profile->minimum_ability_score > 30
                )
            ) {
                throw new InvalidArgumentException(
                    'Il requisito di caratteristica deve essere '
                    . 'compreso tra 1 e 30.'
                );
            }

            //Controlla il tempo necessario per indossare l'oggetto
            self::validateTime(
                $profile->don_time_minutes,
                $profile->don_time_actions,
                'indossamento'
            );

            //Controlla il tempo necessario per rimuovere l'oggetto
            self::validateTime(
                $profile->doff_time_minutes,
                $profile->doff_time_actions,
                'rimozione'
            );
        });
    }

    //Controlla che un tempo utilizzi minuti oppure azioni, non entrambi
    private static function validateTime(
        ?int $minutes,
        ?int $actions,
        string $operation
    ): void {
        //Lo stesso tempo non può utilizzare due unità differenti
        if ($minutes !== null && $actions !== null) {
            throw new InvalidArgumentException(
                "Il tempo di {$operation} deve essere espresso "
                . 'in minuti oppure in azioni, non in entrambi.'
            );
        }

        //I minuti devono essere positivi
        if ($minutes !== null && $minutes < 1) {
            throw new InvalidArgumentException(
                "I minuti necessari per {$operation} devono essere positivi."
            );
        }

        //Le azioni devono essere positive
        if ($actions !== null && $actions < 1) {
            throw new InvalidArgumentException(
                "Le azioni necessarie per {$operation} devono essere positive."
            );
        }
    }

    //Relazione molti-a-uno (BelongsTo):
    //ogni profilo appartiene a un singolo oggetto
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    //Relazione molti-a-uno (BelongsTo):
    //il profilo può richiedere un punteggio minimo di caratteristica
    public function requirementAbility(): BelongsTo
    {
        return $this->belongsTo(
            Ability::class,
            'requirement_ability_id'
        );
    }
}
