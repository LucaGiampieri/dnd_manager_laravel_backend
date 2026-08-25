<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\TashasCauldronSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('salva tutti i trucchetti del Calderone di Tasha', function () {
    $this->seed(TashasCauldronSpellSeeder::class);
    $this->seed(TashasCauldronSpellSeeder::class);

    $spells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 0);

    $boomingBlade = Spell::query()
        ->where('key', 'booming_blade')
        ->firstOrFail();

    $mindSliver = Spell::query()
        ->where('key', 'mind_sliver')
        ->firstOrFail();

    $swordBurst = Spell::query()
        ->where('key', 'sword_burst')
        ->firstOrFail();

    expect($spells->count())
        ->toBe(5)
        ->and($spells->distinct('canonical_key')->count())
        ->toBe(5)
        ->and($boomingBlade->name)
        ->toBe('Lama Roboante')
        ->and($boomingBlade->attack_type)
        ->toBe('melee')
        ->and((float) $boomingBlade->material_cost)
        ->toBe(0.1)
        ->and($mindSliver->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($swordBurst->spellSchool->key)
        ->toBe('conjuration')
        ->and($swordBurst->targetProfile->area_shape)
        ->toBe('emanation')
        ->and($swordBurst->targetProfile->area_size_meters)
        ->toBe(1.524)
        ->and(SpellMaterialComponent::query()->count())
        ->toBe(2);

    $weapon = $boomingBlade->materialComponents()->firstOrFail();

    expect((float) $weapon->cost_amount)
        ->toBe(1.0)
        ->and($weapon->currency->code)
        ->toBe('ma')
        ->and($weapon->focus_replaceable)
        ->toBeFalse()
        ->and($boomingBlade->sourceReferences()->firstOrFail()->page_start)
        ->toBe(113)
        ->and($swordBurst->sourceReferences()->firstOrFail()->page_start)
        ->toBe(116)
        ->and($swordBurst->sourceReferences()->firstOrFail()->sourceBook->slug)
        ->toBe('tcoe-2020');

    $spellIds = $spells->pluck('id');

    expect(
        SourceReference::query()
            ->where('sourceable_type', Spell::class)
            ->whereIn('sourceable_id', $spellIds)
            ->where('reference_type', 'definition')
            ->count()
    )->toBe(5);
});
