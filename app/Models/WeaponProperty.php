<?php

namespace App\Models;

use App\Models\Concerns\HasSourceReferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeaponProperty extends Model
{
    //Le proprietà ufficiali possono essere collegate ai manuali
    use HasSourceReferences;

    protected $fillable = [
        'ruleset_id',
        'key',
        'name',
        'description',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'ruleset_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function ruleset(): BelongsTo
    {
        return $this->belongsTo(Ruleset::class);
    }

    public function itemWeaponPropertyAssignments(): HasMany
    {
        return $this->hasMany(ItemWeaponProperty::class);
    }

    public function weaponProfiles(): BelongsToMany
    {
        return $this->belongsToMany(
            ItemWeaponProfile::class,
            'item_weapon_properties'
        )
            ->using(ItemWeaponProperty::class)
            ->withPivot([
                'id',
                'value',
                'value_text',
                'notes',
            ])
            ->withTimestamps();
    }
}
