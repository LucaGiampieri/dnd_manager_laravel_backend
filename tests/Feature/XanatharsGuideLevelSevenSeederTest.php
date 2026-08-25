<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('salva tutti gli incantesimi di settimo livello di Xanathar', function () {
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $spells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 7);

    $crown = Spell::query()->where('key', 'crown_of_stars')->firstOrFail();
    $pain = Spell::query()->where('key', 'power_word_pain')->firstOrFail();
    $temple = Spell::query()->where('key', 'temple_of_the_gods')->firstOrFail();
    $whirlwind = Spell::query()->where('key', 'whirlwind')->firstOrFail();

    expect($spells->count())
        ->toBe(4)
        ->and($spells->distinct('canonical_key')->count())
        ->toBe(4)
        ->and($crown->spellSchool->key)
        ->toBe('evocation')
        ->and($crown->attack_type)
        ->toBe('ranged')
        ->and($crown->concentration)
        ->toBeFalse()
        ->and($pain->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($temple->casting_time_type)
        ->toBe('hour')
        ->and($temple->duration_value)
        ->toBe(24)
        ->and($temple->targetProfile->area_shape)
        ->toBe('cube')
        ->and($temple->targetProfile->area_size_meters)
        ->toBe(36.576)
        ->and($whirlwind->concentration)
        ->toBeTrue()
        ->and($whirlwind->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($whirlwind->targetProfile->area_shape)
        ->toBe('cylinder')
        ->and($whirlwind->targetProfile->area_size_meters)
        ->toBe(3.048)
        ->and($whirlwind->targetProfile->area_secondary_size_meters)
        ->toBe(9.144);

    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 7)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query->where('version_key', 'xgte_2017')->where('level', 7);
        })
        ->count();

    $templeMaterial = $temple->materialComponents()->firstOrFail();

    expect($materialSpellCount)
        ->toBe(2)
        ->and($materialDetailCount)
        ->toBe(2)
        ->and((float) $templeMaterial->cost_amount)
        ->toBe(5.0)
        ->and($templeMaterial->focus_replaceable)
        ->toBeFalse();

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
                ->where('level', 7)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(4)
        ->and($whirlwind->sourceReferences()->firstOrFail()->page_start)
        ->toBe(171)
        ->and($whirlwind->sourceReferences()->firstOrFail()->sourceBook->slug)
        ->toBe('xgte-2017');
});
