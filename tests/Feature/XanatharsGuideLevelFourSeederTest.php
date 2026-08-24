<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima del test
uses(RefreshDatabase::class);

//Verifica in un solo passaggio tutto il catalogo di 4° livello
it('salva tutti gli incantesimi di quarto livello di Xanathar', function () {
    //La doppia esecuzione controlla anche l'idempotenza
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $levelFourSpells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 4);

    $elementalBane = Spell::query()
        ->where('key', 'elemental_bane')
        ->firstOrFail();

    $charmMonster = Spell::query()
        ->where('key', 'charm_monster')
        ->firstOrFail();

    $summonGreaterDemon = Spell::query()
        ->where('key', 'summon_greater_demon')
        ->firstOrFail();

    $sickeningRadiance = Spell::query()
        ->where('key', 'sickening_radiance')
        ->firstOrFail();

    $guardianOfNature = Spell::query()
        ->where('key', 'guardian_of_nature')
        ->firstOrFail();

    $shadowOfMoil = Spell::query()
        ->where('key', 'shadow_of_moil')
        ->firstOrFail();

    $waterySphere = Spell::query()
        ->where('key', 'watery_sphere')
        ->firstOrFail();

    $vitriolicSphere = Spell::query()
        ->where('key', 'vitriolic_sphere')
        ->firstOrFail();

    $stormSphere = Spell::query()
        ->where('key', 'storm_sphere')
        ->firstOrFail();

    $findGreaterSteed = Spell::query()
        ->where('key', 'find_greater_steed')
        ->firstOrFail();

    //Controlla conteggi, identità e concentrazione
    expect($levelFourSpells->count())
        ->toBe(10)
        ->and($levelFourSpells->distinct('canonical_key')->count())
        ->toBe(10)
        ->and($elementalBane->version_key)
        ->toBe('xgte_2017')
        ->and($elementalBane->spellSchool->key)
        ->toBe('transmutation')
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 4)
                ->where('concentration', true)
                ->count()
        )->toBe(7)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 4)
                ->where('ritual', true)
                ->count()
        )->toBe(0);

    //Controlla tutti i componenti materiali normalizzati
    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 4)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query
                ->where('version_key', 'xgte_2017')
                ->where('level', 4);
        })
        ->count();

    $missingDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 4)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 4)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    $shadowMaterial = $shadowOfMoil
        ->materialComponents()
        ->firstOrFail();

    $demonMaterial = $summonGreaterDemon
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(4)
        ->and($materialDetailCount)
        ->toBe(4)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and((float) $shadowMaterial->cost_amount)
        ->toBe(150.0)
        ->and($shadowMaterial->focus_replaceable)
        ->toBeFalse()
        ->and($shadowMaterial->consumed)
        ->toBeFalse()
        ->and($demonMaterial->description)
        ->toContain('cerchio protettivo')
        ->and($demonMaterial->consumed)
        ->toBeFalse();

    //Controlla tiri salvezza, bersagli e aree
    expect($elementalBane->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($charmMonster->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($sickeningRadiance->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($sickeningRadiance->targetProfile->area_size_meters)
        ->toBe(9.144)
        ->and($guardianOfNature->casting_time_type)
        ->toBe('bonus_action')
        ->and($guardianOfNature->targetProfile->target_type)
        ->toBe('self')
        ->and($waterySphere->savingThrowAbility->short_name)
        ->toBe('FOR')
        ->and($waterySphere->targetProfile->area_size_meters)
        ->toBe(1.524)
        ->and($vitriolicSphere->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($vitriolicSphere->save_success_damage)
        ->toBe('half')
        ->and($vitriolicSphere->targetProfile->area_size_meters)
        ->toBe(6.096)
        ->and($stormSphere->attack_type)
        ->toBe('ranged')
        ->and($stormSphere->savingThrowAbility->short_name)
        ->toBe('FOR');

    //Controlla evocazioni, tempi di lancio e bersagli speciali
    expect($summonGreaterDemon->duration_type)
        ->toBe('hour')
        ->and($summonGreaterDemon->concentration)
        ->toBeTrue()
        ->and($findGreaterSteed->casting_time_type)
        ->toBe('minute')
        ->and($findGreaterSteed->casting_time_value)
        ->toBe(10)
        ->and($findGreaterSteed->targetProfile->target_type)
        ->toBe('special');

    //Controlla descrizioni, profili e riferimenti alle pagine
    $spellIds = $levelFourSpells->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $reference = $findGreaterSteed
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(10)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 4)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(10)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 4)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(171)
        ->and($reference->sourceBook->slug)
        ->toBe('xgte-2017')
        ->and($reference->official_text)
        ->toBeNull();
});
