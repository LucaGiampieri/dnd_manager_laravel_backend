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

it('salva l incantesimo di settimo livello di Tasha', function () {
    $levelSevenSpells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 7);

    $dream = (clone $levelSevenSpells)->firstOrFail();

    expect((clone $levelSevenSpells)->count())
        ->toBe(1)
        ->and(
            (clone $levelSevenSpells)
                ->distinct('canonical_key')
                ->count()
        )->toBe(1)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->where('level', '<=', 7)
                ->count()
        )->toBe(20)
        ->and($dream->key)
        ->toBe('dream_of_the_blue_veil')
        ->and($dream->name)
        ->toBe('Sogno del Velo Celeste')
        ->and($dream->spellSchool->key)
        ->toBe('conjuration');
});

it('salva durata bersagli e interruzioni del sogno', function () {
    $dream = Spell::query()
        ->where('key', 'dream_of_the_blue_veil')
        ->firstOrFail();
    $dreamEffect = $dream
        ->effectDefinitions()
        ->where('key', 'shared_world_dream')
        ->firstOrFail();
    $duration = $dreamEffect
        ->durations()
        ->firstOrFail();
    $interruption = $dream
        ->effectDefinitions()
        ->where('key', 'damage_interruption')
        ->firstOrFail();

    expect($dream->casting_time_value)
        ->toBe(10)
        ->and($dream->casting_time_type)
        ->toBe('minute')
        ->and($dream->range)
        ->toBe(6.096)
        ->and($dream->duration_type)
        ->toBe('hour')
        ->and($dream->duration_value)
        ->toBe(6)
        ->and($dream->targetProfile->target_type)
        ->toBe('creatures')
        ->and($dream->targetProfile->target_count)
        ->toBe(9)
        ->and($dream->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($dream->effectDefinitions()->count())
        ->toBe(3)
        ->and($duration->duration_type)
        ->toBe('fixed')
        ->and($duration->duration_value)
        ->toBe(6)
        ->and($duration->duration_unit)
        ->toBe('hour')
        ->and($interruption->application_type)
        ->toBe('on_damage')
        ->and($interruption->target_scope)
        ->toBe('special');
});

it('collega requisito materiale e pagina del manuale', function () {
    $dream = Spell::query()
        ->where('key', 'dream_of_the_blue_veil')
        ->firstOrFail();
    $material = $dream
        ->materialComponents()
        ->firstOrFail();
    $reference = $dream
        ->sourceReferences()
        ->firstOrFail();

    expect($dream->material_component)
        ->toBeTrue()
        ->and($material->cost_amount)
        ->toBeNull()
        ->and($material->consumed)
        ->toBeFalse()
        ->and($reference->page_start)
        ->toBe(115)
        ->and($reference->sourceBook->slug)
        ->toBe('tcoe-2020')
        ->and(
            SourceReference::query()
                ->where('sourceable_type', Spell::class)
                ->where('sourceable_id', $dream->id)
                ->count()
        )->toBe(1);
});
