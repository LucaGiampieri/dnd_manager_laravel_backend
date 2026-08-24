<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima del test
uses(RefreshDatabase::class);

//Verifica in un solo passaggio tutto il catalogo di 6° livello
it('salva tutti gli incantesimi di sesto livello di Xanathar', function () {
    //La doppia esecuzione controlla anche l'idempotenza
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $levelSixSpells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 6);

    $druidGrove = Spell::query()
        ->where('key', 'druid_grove')
        ->firstOrFail();

    $createHomunculus = Spell::query()
        ->where('key', 'create_homunculus')
        ->firstOrFail();

    $scatter = Spell::query()
        ->where('key', 'scatter')
        ->firstOrFail();

    $soulCage = Spell::query()
        ->where('key', 'soul_cage')
        ->firstOrFail();

    $primordialWard = Spell::query()
        ->where('key', 'primordial_ward')
        ->firstOrFail();

    $investitureOfIce = Spell::query()
        ->where('key', 'investiture_of_ice')
        ->firstOrFail();

    $investitureOfWind = Spell::query()
        ->where('key', 'investiture_of_wind')
        ->firstOrFail();

    $investitureOfFlame = Spell::query()
        ->where('key', 'investiture_of_flame')
        ->firstOrFail();

    $investitureOfStone = Spell::query()
        ->where('key', 'investiture_of_stone')
        ->firstOrFail();

    $bonesOfTheEarth = Spell::query()
        ->where('key', 'bones_of_the_earth')
        ->firstOrFail();

    $mentalPrison = Spell::query()
        ->where('key', 'mental_prison')
        ->firstOrFail();

    $tensersTransformation = Spell::query()
        ->where('key', 'tensers_transformation')
        ->firstOrFail();

    //Controlla conteggi, identità, scuole e concentrazione
    expect($levelSixSpells->count())
        ->toBe(12)
        ->and($levelSixSpells->distinct('canonical_key')->count())
        ->toBe(12)
        ->and($druidGrove->version_key)
        ->toBe('xgte_2017')
        ->and($druidGrove->spellSchool->key)
        ->toBe('abjuration')
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 6)
                ->where('concentration', true)
                ->count()
        )->toBe(7)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 6)
                ->where('ritual', true)
                ->count()
        )->toBe(0);

    //Controlla tutti i componenti materiali normalizzati
    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 6)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query
                ->where('version_key', 'xgte_2017')
                ->where('level', 6);
        })
        ->count();

    $missingDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 6)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 6)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    $druidMaterial = $druidGrove
        ->materialComponents()
        ->firstOrFail();

    $consumedMixture = $createHomunculus
        ->materialComponents()
        ->where('key', 'consumed_mixture')
        ->firstOrFail();

    $jeweledDagger = $createHomunculus
        ->materialComponents()
        ->where('key', 'jeweled_dagger')
        ->firstOrFail();

    $soulCageMaterial = $soulCage
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(4)
        ->and($materialDetailCount)
        ->toBe(5)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and($druidMaterial->consumed)
        ->toBeTrue()
        ->and($createHomunculus->materialComponents()->count())
        ->toBe(2)
        ->and($consumedMixture->consumed)
        ->toBeTrue()
        ->and((float) $jeweledDagger->cost_amount)
        ->toBe(1000.0)
        ->and($jeweledDagger->cost_is_minimum)
        ->toBeTrue()
        ->and($jeweledDagger->consumed)
        ->toBeFalse()
        ->and((float) $soulCageMaterial->cost_amount)
        ->toBe(100.0);

    //Controlla tempi di lancio, bersagli e tiri salvezza
    expect($druidGrove->casting_time_type)
        ->toBe('minute')
        ->and($druidGrove->casting_time_value)
        ->toBe(10)
        ->and($druidGrove->targetProfile->area_shape)
        ->toBe('cube')
        ->and($druidGrove->targetProfile->area_size_meters)
        ->toBe(27.432)
        ->and($createHomunculus->casting_time_type)
        ->toBe('hour')
        ->and($scatter->targetProfile->target_count)
        ->toBe(5)
        ->and($scatter->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($soulCage->casting_time_type)
        ->toBe('reaction')
        ->and($soulCage->casting_trigger)
        ->toContain('vede morire un umanoide')
        ->and($soulCage->duration_value)
        ->toBe(8)
        ->and($primordialWard->targetProfile->can_target_self)
        ->toBeTrue();

    //Controlla investiture e strutture speciali
    expect($investitureOfIce->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($investitureOfWind->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($investitureOfFlame->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($investitureOfStone->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($investitureOfStone->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($bonesOfTheEarth->targetProfile->area_shape)
        ->toBe('special')
        ->and($bonesOfTheEarth->targetProfile->notes)
        ->toContain('sei colonne')
        ->and($mentalPrison->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($mentalPrison->save_success_damage)
        ->toBe('full')
        ->and($tensersTransformation->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($tensersTransformation->duration_value)
        ->toBe(10);

    //Controlla descrizioni, profili e riferimenti alle pagine
    $spellIds = $levelSixSpells->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $reference = $tensersTransformation
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 6)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 6)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(169)
        ->and($reference->sourceBook->slug)
        ->toBe('xgte-2017')
        ->and($reference->official_text)
        ->toBeNull();
});
