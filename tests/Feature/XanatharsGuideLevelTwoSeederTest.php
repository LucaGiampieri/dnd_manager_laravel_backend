<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima del test
uses(RefreshDatabase::class);

//Verifica in un solo passaggio tutto il catalogo di 2° livello
it('salva tutti gli incantesimi di secondo livello di Xanathar', function () {
    //La doppia esecuzione controlla anche l'idempotenza
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $levelTwoSpells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 2);

    $mindSpike = Spell::query()
        ->where('key', 'mind_spike')
        ->firstOrFail();

    $dustDevil = Spell::query()
        ->where('key', 'dust_devil')
        ->firstOrFail();

    $pyrotechnics = Spell::query()
        ->where('key', 'pyrotechnics')
        ->firstOrFail();

    $skywrite = Spell::query()
        ->where('key', 'skywrite')
        ->firstOrFail();

    $dragonsBreath = Spell::query()
        ->where('key', 'dragons_breath')
        ->firstOrFail();

    $maximiliansGrasp = Spell::query()
        ->where('key', 'maximilians_earthen_grasp')
        ->firstOrFail();

    $aganazzarsScorcher = Spell::query()
        ->where('key', 'aganazzars_scorcher')
        ->firstOrFail();

    $wardingWind = Spell::query()
        ->where('key', 'warding_wind')
        ->firstOrFail();

    $earthbind = Spell::query()
        ->where('key', 'earthbind')
        ->firstOrFail();

    //Controlla conteggi, identità, scuole e concentrazione
    expect($levelTwoSpells->count())
        ->toBe(12)
        ->and($levelTwoSpells->distinct('canonical_key')->count())
        ->toBe(12)
        ->and($mindSpike->version_key)
        ->toBe('xgte_2017')
        ->and($mindSpike->spellSchool->key)
        ->toBe('divination')
        ->and($pyrotechnics->spellSchool->key)
        ->toBe('transmutation')
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 2)
                ->where('concentration', true)
                ->count()
        )->toBe(9)
        ->and($skywrite->ritual)
        ->toBeTrue();

    //Controlla i componenti materiali normalizzati
    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 2)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query
                ->where('version_key', 'xgte_2017')
                ->where('level', 2);
        })
        ->count();

    $missingDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 2)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 2)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    $dragonBreathMaterial = $dragonsBreath
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(5)
        ->and($materialDetailCount)
        ->toBe(5)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and($dragonBreathMaterial->description)
        ->toBe('Un peperoncino.')
        ->and($dragonBreathMaterial->consumed)
        ->toBeFalse()
        ->and($dragonBreathMaterial->focus_replaceable)
        ->toBeTrue();

    //Controlla tiri salvezza e forme delle aree
    expect($mindSpike->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($mindSpike->save_success_damage)
        ->toBe('half')
        ->and($dustDevil->savingThrowAbility->short_name)
        ->toBe('FOR')
        ->and($dustDevil->targetProfile->area_shape)
        ->toBe('cube')
        ->and($pyrotechnics->targetProfile->area_shape)
        ->toBe('special')
        ->and($maximiliansGrasp->targetProfile->area_shape)
        ->toBe('square')
        ->and($maximiliansGrasp->targetProfile->area_size_meters)
        ->toBe(1.524)
        ->and($aganazzarsScorcher->targetProfile->area_shape)
        ->toBe('line')
        ->and(
            $aganazzarsScorcher
                ->targetProfile
                ->area_size_meters
        )->toBe(9.144)
        ->and(
            $aganazzarsScorcher
                ->targetProfile
                ->area_secondary_size_meters
        )->toBe(1.524)
        ->and($wardingWind->targetProfile->area_shape)
        ->toBe('emanation')
        ->and($earthbind->savingThrowAbility->short_name)
        ->toBe('FOR');

    //Controlla bersagli e tempi di lancio rappresentativi
    expect($dragonsBreath->casting_time_type)
        ->toBe('bonus_action')
        ->and($dragonsBreath->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($dragonsBreath->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($wardingWind->range_type)
        ->toBe('self')
        ->and($wardingWind->duration_value)
        ->toBe(10)
        ->and($earthbind->range)
        ->toBe(91.44)
        ->and($earthbind->targetProfile->requires_sight)
        ->toBeTrue();

    //Controlla descrizioni, profili e riferimenti alle pagine
    $spellIds = $levelTwoSpells->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $reference = $wardingWind
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 2)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 2)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(172)
        ->and($reference->sourceBook->slug)
        ->toBe('xgte-2017')
        ->and($reference->official_text)
        ->toBeNull();
});
