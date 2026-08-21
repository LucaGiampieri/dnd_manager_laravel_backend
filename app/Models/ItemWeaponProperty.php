<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ItemWeaponProperty extends Pivot
{
    protected $table = 'item_weapon_properties';

    public $incrementing = true;

    protected $fillable = [
        'item_weapon_profile_id',
        'weapon_property_id',
        'value',
        'value_text',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'item_weapon_profile_id' => 'integer',
            'weapon_property_id' => 'integer',
            'value' => 'float',
        ];
    }

    public function weaponProfile(): BelongsTo
    {
        return $this->belongsTo(
            ItemWeaponProfile::class,
            'item_weapon_profile_id'
        );
    }

    public function weaponProperty(): BelongsTo
    {
        return $this->belongsTo(WeaponProperty::class);
    }
}
