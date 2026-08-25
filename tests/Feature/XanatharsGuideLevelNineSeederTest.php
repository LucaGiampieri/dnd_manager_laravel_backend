<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('salva tutti gli incantesimi di nono livello di Xanathar', function () {
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $spells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 9);

    $invulnerability = Spell::query()
        ->where('key', 'invulnerability')
        ->firstOrFail();
    $massPolymorph = Spell::query()->where('key', 'mass_polymorph')->firstOrFail();
    $psychicScream = Spell::query()->where('key', 'psychic_scream')->firstOrFail();

    expect($spells->count())
        ->toBe(3)
        ->and($spells->distinct('canonical_key')->count())
        ->toBe(3)
        ->and($invulnerability->spellSchool->key)
        ->toBe('abjuration')
        ->and($invulnerability->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($invulnerability->concentration)
        ->toBeTrue()
        ->and($massPolymorph->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($massPolymorph->targetProfile->target_count)
        ->toBe(10)
        ->and($massPolymorph->concentration)
        ->toBeTrue()
        ->and($psychicScream->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($psychicScream->save_success_damage)
        ->toBe('half')
        ->and($psychicScream->targetProfile->target_count)
        ->toBe(10);

    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 9)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query->where('version_key', 'xgte_2017')->where('level', 9);
        })
        ->count();

    $invulnerabilityMaterial = $invulnerability
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(2)
        ->and($materialDetailCount)
        ->toBe(2)
        ->and((float) $invulnerabilityMaterial->cost_amount)
        ->toBe(500.0)
        ->and($invulnerabilityMaterial->consumed)
        ->toBeTrue();

    $spellIds = $spells->pluck('id');

    expect(
        SourceReference::query()
            ->where('sourceable_type', Spell::class)
            ->whereIn('sourceable_id', $spellIds)
            ->where('reference_type', 'definition')
            ->count()
    )->toBe(3)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 9)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(3)
        ->and($psychicScream->sourceReferences()->firstOrFail()->page_start)
        ->toBe(171)
        ->and($psychicScream->sourceReferences()->firstOrFail()->sourceBook->slug)
        ->toBe('xgte-2017');
});
