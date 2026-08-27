<?php

use App\Models\SourceReference;
use App\Models\Spell;
use Database\Seeders\TashasCauldronSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Inserisce due volte il catalogo per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(TashasCauldronSpellSeeder::class);
    $this->seed(TashasCauldronSpellSeeder::class);
});

it('salva l incantesimo di nono livello di Tasha', function () {
    $levelNineSpells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 9);

    $blade = (clone $levelNineSpells)->firstOrFail();

    expect((clone $levelNineSpells)->count())
        ->toBe(1)
        ->and(
            (clone $levelNineSpells)
                ->distinct('canonical_key')
                ->count()
        )->toBe(1)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->count()
        )->toBe(21)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->where('level', 8)
                ->count()
        )->toBe(0)
        ->and($blade->key)
        ->toBe('blade_of_disaster')
        ->and($blade->name)
        ->toBe('Lama del Disastro')
        ->and($blade->spellSchool->key)
        ->toBe('conjuration');
});

it('salva attacchi danni e critico della lama', function () {
    $blade = Spell::query()
        ->where('key', 'blade_of_disaster')
        ->firstOrFail();
    $attackEffect = $blade
        ->effectDefinitions()
        ->where('key', 'planar_blade_attacks')
        ->firstOrFail();
    $normalDamage = $attackEffect
        ->damages()
        ->where('key', 'force_damage')
        ->firstOrFail();
    $criticalDamage = $attackEffect
        ->damages()
        ->where('key', 'critical_force_damage')
        ->firstOrFail();

    expect($blade->casting_time_type)
        ->toBe('bonus_action')
        ->and($blade->range)
        ->toBe(18.288)
        ->and($blade->duration_type)
        ->toBe('minute')
        ->and($blade->duration_value)
        ->toBe(1)
        ->and($blade->concentration)
        ->toBeTrue()
        ->and($blade->attack_type)
        ->toBe('melee')
        ->and($blade->targetProfile->can_target_objects)
        ->toBeTrue()
        ->and($blade->effectDefinitions()->count())
        ->toBe(2)
        ->and($normalDamage->formula)
        ->toBe('4d12')
        ->and($normalDamage->damageType->name)
        ->toBe('Forza')
        ->and($criticalDamage->formula)
        ->toBe('8d12')
        ->and($criticalDamage->damageType->name)
        ->toBe('Forza')
        ->and($criticalDamage->is_primary)
        ->toBeFalse();
});

it('salva movimento e riferimento della lama', function () {
    $blade = Spell::query()
        ->where('key', 'blade_of_disaster')
        ->firstOrFail();
    $movement = $blade
        ->effectDefinitions()
        ->where('key', 'mobile_planar_blade')
        ->firstOrFail();
    $reference = $blade
        ->sourceReferences()
        ->firstOrFail();

    expect($movement->application_type)
        ->toBe('manual')
        ->and($movement->target_scope)
        ->toBe('special')
        ->and($blade->material_component)
        ->toBeFalse()
        ->and($blade->materialComponents()->count())
        ->toBe(0)
        ->and($reference->page_start)
        ->toBe(112)
        ->and($reference->sourceBook->slug)
        ->toBe('tcoe-2020')
        ->and(
            SourceReference::query()
                ->where('sourceable_type', Spell::class)
                ->where('sourceable_id', $blade->id)
                ->count()
        )->toBe(1);
});
