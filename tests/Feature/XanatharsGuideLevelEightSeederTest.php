<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('salva tutti gli incantesimi di ottavo livello di Xanathar', function () {
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $spells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 8);

    $dragon = Spell::query()->where('key', 'illusory_dragon')->firstOrFail();
    $fortress = Spell::query()->where('key', 'mighty_fortress')->firstOrFail();
    $wilting = Spell::query()
        ->where('key', 'abi_dalzims_horrid_wilting')
        ->firstOrFail();
    $darkness = Spell::query()->where('key', 'maddening_darkness')->firstOrFail();

    expect($spells->count())
        ->toBe(4)
        ->and($spells->distinct('canonical_key')->count())
        ->toBe(4)
        ->and($dragon->spellSchool->key)
        ->toBe('illusion')
        ->and($dragon->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($dragon->concentration)
        ->toBeTrue()
        ->and($fortress->casting_time_type)
        ->toBe('minute')
        ->and($fortress->range)
        ->toBe(1609.344)
        ->and($fortress->targetProfile->area_shape)
        ->toBe('square')
        ->and($fortress->targetProfile->area_size_meters)
        ->toBe(36.576)
        ->and($wilting->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($wilting->targetProfile->area_shape)
        ->toBe('cube')
        ->and($wilting->targetProfile->area_size_meters)
        ->toBe(9.144)
        ->and($darkness->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($darkness->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($darkness->targetProfile->area_size_meters)
        ->toBe(18.288);

    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 8)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query->where('version_key', 'xgte_2017')->where('level', 8);
        })
        ->count();

    $fortressMaterial = $fortress->materialComponents()->firstOrFail();

    expect($materialSpellCount)
        ->toBe(3)
        ->and($materialDetailCount)
        ->toBe(3)
        ->and((float) $fortressMaterial->cost_amount)
        ->toBe(500.0)
        ->and($fortressMaterial->consumed)
        ->toBeTrue();

    $spellIds = $spells->pluck('id');

    expect(
        SourceReference::query()
            ->where('sourceable_type', Spell::class)
            ->whereIn('sourceable_id', $spellIds)
            ->where('reference_type', 'definition')
            ->count()
    )->toBe(4)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 8)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(4)
        ->and($darkness->sourceReferences()->firstOrFail()->page_start)
        ->toBe(163)
        ->and($darkness->sourceReferences()->firstOrFail()->sourceBook->slug)
        ->toBe('xgte-2017');
});
