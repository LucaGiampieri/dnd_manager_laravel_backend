<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class ItemWeaponDamage extends Model
{
    protected $fillable = [
        'item_weapon_profile_id',
        'damage_type_id',
        'dice_count',
        'die_size',
        'bonus',
        'primary',
        'sort_order',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'item_weapon_profile_id' => 'integer',
            'damage_type_id' => 'integer',
            'dice_count' => 'integer',
            'die_size' => 'integer',
            'bonus' => 'integer',
            'primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    //Impedisce formule di danno impossibili
    protected static function booted(): void
    {
        static::saving(function (ItemWeaponDamage $damage): void {
            if ($damage->dice_count < 1) {
                throw new InvalidArgumentException(
                    'Il numero di dadi del danno deve essere almeno 1.'
                );
            }

            if ($damage->die_size < 2) {
                throw new InvalidArgumentException(
                    'Il dado del danno deve avere almeno 2 facce.'
                );
            }
        });
    }

    public function weaponProfile(): BelongsTo
    {
        return $this->belongsTo(
            ItemWeaponProfile::class,
            'item_weapon_profile_id'
        );
    }

    public function damageType(): BelongsTo
    {
        return $this->belongsTo(DamageType::class);
    }
}
