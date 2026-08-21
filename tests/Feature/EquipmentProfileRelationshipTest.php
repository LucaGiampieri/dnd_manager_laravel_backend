<?php

use App\Models\Ability;
use App\Models\DamageType;
use App\Models\Item;
use App\Models\ItemArmorProfile;
use App\Models\ItemType;
use App\Models\ItemWeaponDamage;
use App\Models\ItemWeaponProfile;
use App\Models\ItemWeaponProperty;
use App\Models\Ruleset;
use App\Models\WeaponProperty;
use Database\Seeders\AbilitySeeder;
use Database\Seeders\DamageTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        RulesetSeeder::class,
        AbilitySeeder::class,
        DamageTypeSeeder::class,
    ]);

    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    $this->damageType = DamageType::query()
        ->firstOrFail();

    $this->strength = Ability::query()
        ->where('short_name', 'FOR')
        ->firstOrFail();

    $this->weaponType = ItemType::query()->create([
        'key' => 'weapon',
        'name' => 'Arma',
        'sort_order' => 10,
    ]);

    $this->armorType = ItemType::query()->create([
        'key' => 'armor',
        'name' => 'Armatura',
        'sort_order' => 20,
    ]);
});

it('gestisce profilo danni e proprietà di una arma', function () {
    $weapon = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_sword',
        'name' => 'Spada di prova',
        'item_type_id' => $this->weaponType->id,
        'weight_kg' => '1.500',
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    $profile = $weapon->weaponProfile()->create([
        'weapon_category' => 'martial',
        'attack_type' => 'melee',
        'range' => 1.5,
        'long_range' => null,
        'uses_ammunition' => false,
        'capacity' => null,
    ]);

    $damage = $profile->damages()->create([
        'damage_type_id' => $this->damageType->id,
        'dice_count' => 1,
        'die_size' => 8,
        'bonus' => 0,
        'primary' => true,
        'sort_order' => 10,
    ]);

    $property = WeaponProperty::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_versatile',
        'name' => 'Versatile di prova',
        'description' => 'Proprietà utilizzata soltanto nel test.',
        'sort_order' => 10,
    ]);

    $profile->properties()->attach($property->id, [
        'value' => null,
        'value_text' => '1d10',
        'notes' => null,
    ]);

    $weapon->load([
        'weaponProfile.damages.damageType',
        'weaponProfile.properties',
    ]);

    expect($weapon->weaponProfile->is($profile))->toBeTrue()
        ->and($profile->item->is($weapon))->toBeTrue()
        ->and($profile->weapon_category)->toBe('martial')
        ->and($profile->attack_type)->toBe('melee')
        ->and($profile->damages)->toHaveCount(1)
        ->and($profile->damages->first()->is($damage))->toBeTrue()
        ->and($damage->dice_count)->toBe(1)
        ->and($damage->die_size)->toBe(8)
        ->and($damage->primary)->toBeTrue()
        ->and($damage->damageType->is($this->damageType))->toBeTrue()
        ->and($profile->properties)->toHaveCount(1)
        ->and($profile->properties->first()->is($property))->toBeTrue()
        ->and($profile->properties->first()->pivot->value_text)
        ->toBe('1d10');
});

it('gestisce il profilo meccanico di una armatura', function () {
    $armor = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_armor',
        'name' => 'Armatura di prova',
        'item_type_id' => $this->armorType->id,
        'weight_kg' => '10.000',
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    $profile = $armor->armorProfile()->create([
        'armor_category' => 'medium',
        'armor_class_operation' => 'set',
        'armor_class_value' => 14,
        'dexterity_modifier' => 'capped',
        'max_dexterity_bonus' => 2,
        'requirement_ability_id' => $this->strength->id,
        'minimum_ability_score' => 13,
        'stealth_disadvantage' => true,
        'don_time_minutes' => 5,
        'doff_time_minutes' => 1,
    ]);

    $armor->load('armorProfile.requirementAbility');

    expect($armor->armorProfile->is($profile))->toBeTrue()
        ->and($profile->item->is($armor))->toBeTrue()
        ->and($profile->armor_category)->toBe('medium')
        ->and($profile->armor_class_value)->toBe(14)
        ->and($profile->max_dexterity_bonus)->toBe(2)
        ->and($profile->stealth_disadvantage)->toBeTrue()
        ->and($profile->requirementAbility->is($this->strength))
        ->toBeTrue();
});

it('rifiuta formule meccaniche non valide', function () {
    expect(
        fn () => ItemWeaponProfile::query()->create([
            'item_id' => 1,
            'weapon_category' => 'martial',
            'attack_type' => 'ranged',
            'range' => 30,
            'long_range' => 10,
            'uses_ammunition' => true,
        ])
    )->toThrow(\InvalidArgumentException::class);

    expect(
        fn () => ItemWeaponDamage::query()->create([
            'item_weapon_profile_id' => 1,
            'damage_type_id' => $this->damageType->id,
            'dice_count' => 0,
            'die_size' => 8,
            'bonus' => 0,
            'primary' => true,
            'sort_order' => 10,
        ])
    )->toThrow(\InvalidArgumentException::class);

    expect(
        fn () => ItemArmorProfile::query()->create([
            'item_id' => 1,
            'armor_category' => 'medium',
            'armor_class_operation' => 'set',
            'armor_class_value' => 14,
            'dexterity_modifier' => 'capped',
            'max_dexterity_bonus' => null,
            'stealth_disadvantage' => false,
        ])
    )->toThrow(\InvalidArgumentException::class);
});

it('rifiuta profili e proprietà duplicate', function () {
    $weapon = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'unique_weapon',
        'name' => 'Arma unica',
        'item_type_id' => $this->weaponType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    $weapon->weaponProfile()->create([
        'weapon_category' => 'simple',
        'attack_type' => 'melee',
        'range' => 1.5,
        'uses_ammunition' => false,
    ]);

    expect(
        fn () => $weapon->weaponProfile()->create([
            'weapon_category' => 'martial',
            'attack_type' => 'melee',
            'range' => 1.5,
            'uses_ammunition' => false,
        ])
    )->toThrow(QueryException::class);
});

it('elimina profili danni e proprietà assegnate insieme alla arma', function () {
    $weapon = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'temporary_weapon',
        'name' => 'Arma temporanea',
        'item_type_id' => $this->weaponType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    $profile = $weapon->weaponProfile()->create([
        'weapon_category' => 'simple',
        'attack_type' => 'melee',
        'range' => 1.5,
        'uses_ammunition' => false,
    ]);

    $damage = $profile->damages()->create([
        'damage_type_id' => $this->damageType->id,
        'dice_count' => 1,
        'die_size' => 6,
        'bonus' => 0,
        'primary' => true,
        'sort_order' => 10,
    ]);

    $property = WeaponProperty::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'temporary_property',
        'name' => 'Proprietà temporanea',
        'sort_order' => 10,
    ]);

    $profile->properties()->attach($property->id);

    $profileId = $profile->id;
    $damageId = $damage->id;

    $assignmentId = $profile
        ->propertyAssignments()
        ->value('id');

    $weapon->delete();

    expect(
        ItemWeaponProfile::query()
            ->whereKey($profileId)
            ->exists()
    )->toBeFalse()
        ->and(
            ItemWeaponDamage::query()
                ->whereKey($damageId)
                ->exists()
        )->toBeFalse()
        ->and(
            ItemWeaponProperty::query()
                ->whereKey($assignmentId)
                ->exists()
        )->toBeFalse()
        //La proprietà appartiene al catalogo e deve rimanere
        ->and($property->fresh())
        ->not->toBeNull();
});
