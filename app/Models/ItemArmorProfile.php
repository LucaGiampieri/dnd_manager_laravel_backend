<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemArmorProfile extends Model
{
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
        'doff_time_minutes',
        'notes',
    ];

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
            'doff_time_minutes' => 'integer',
        ];
    }

    //Controlla la coerenza del calcolo della Classe Armatura
    protected static function booted(): void
    {
        static::saving(function (ItemArmorProfile $profile): void {
            if ($profile->armor_class_value < 1) {
                throw new InvalidArgumentException(
                    'Il valore della Classe Armatura deve essere positivo.'
                );
            }

            if (
                $profile->dexterity_modifier === 'capped'
                && $profile->max_dexterity_bonus === null
            ) {
                throw new InvalidArgumentException(
                    'Una armatura con Destrezza limitata deve indicare '
                    . 'il bonus massimo applicabile.'
                );
            }

            if (
                $profile->dexterity_modifier !== 'capped'
                && $profile->max_dexterity_bonus !== null
            ) {
                throw new InvalidArgumentException(
                    'Il bonus massimo di Destrezza può essere utilizzato '
                    . 'soltanto con il modificatore capped.'
                );
            }

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
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function requirementAbility(): BelongsTo
    {
        return $this->belongsTo(
            Ability::class,
            'requirement_ability_id'
        );
    }
}
