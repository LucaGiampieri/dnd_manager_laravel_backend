<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class ItemWeaponProfile extends Model
{
    protected $fillable = [
        'item_id',
        'weapon_category',
        'attack_type',
        'range',
        'long_range',
        'uses_ammunition',
        'capacity',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'item_id' => 'integer',
            'range' => 'float',
            'long_range' => 'float',
            'uses_ammunition' => 'boolean',
            'capacity' => 'integer',
        ];
    }

    //Controlla la validità delle gittate
    protected static function booted(): void
    {
        static::saving(function (ItemWeaponProfile $profile): void {
            if (
                $profile->range !== null
                && $profile->range < 0
            ) {
                throw new InvalidArgumentException(
                    'La gittata normale non può essere negativa.'
                );
            }

            if (
                $profile->long_range !== null
                && $profile->long_range < 0
            ) {
                throw new InvalidArgumentException(
                    'La gittata lunga non può essere negativa.'
                );
            }

            if (
                $profile->range !== null
                && $profile->long_range !== null
                && $profile->long_range < $profile->range
            ) {
                throw new InvalidArgumentException(
                    'La gittata lunga non può essere inferiore '
                    . 'alla gittata normale.'
                );
            }

            if (
                $profile->capacity !== null
                && $profile->capacity < 1
            ) {
                throw new InvalidArgumentException(
                    'La capacità deve essere almeno pari a 1.'
                );
            }
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function damages(): HasMany
    {
        return $this->hasMany(ItemWeaponDamage::class)
            ->orderBy('sort_order');
    }

    public function propertyAssignments(): HasMany
    {
        return $this->hasMany(ItemWeaponProperty::class);
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(
            WeaponProperty::class,
            'item_weapon_properties'
        )
            ->using(ItemWeaponProperty::class)
            ->withPivot([
                'id',
                'value',
                'value_text',
                'notes',
            ])
            ->withTimestamps()
            ->orderBy('weapon_properties.sort_order');
    }
}
