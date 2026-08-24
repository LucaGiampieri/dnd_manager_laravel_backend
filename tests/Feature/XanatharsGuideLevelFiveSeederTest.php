<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima del test
uses(RefreshDatabase::class);

//Verifica in un solo passaggio tutto il catalogo di 5° livello
it('salva tutti gli incantesimi di quinto livello di Xanathar', function () {
    //La doppia esecuzione controlla anche l'idempotenza
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $levelFiveSpells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 5);

    $skillEmpowerment = Spell::query()
        ->where('key', 'skill_empowerment')
        ->firstOrFail();

    $dawn = Spell::query()
        ->where('key', 'dawn')
        ->firstOrFail();

    $holyWeapon = Spell::query()
        ->where('key', 'holy_weapon')
        ->firstOrFail();

    $steelWindStrike = Spell::query()
        ->where('key', 'steel_wind_strike')
        ->firstOrFail();

    $controlWinds = Spell::query()
        ->where('key', 'control_winds')
        ->firstOrFail();

    $danseMacabre = Spell::query()
        ->where('key', 'danse_macabre')
        ->firstOrFail();

    $enervation = Spell::query()
        ->where('key', 'enervation')
        ->firstOrFail();

    $negativeEnergyFlood = Spell::query()
        ->where('key', 'negative_energy_flood')
        ->firstOrFail();

    $maelstrom = Spell::query()
        ->where('key', 'maelstrom')
        ->firstOrFail();

    $wallOfLight = Spell::query()
        ->where('key', 'wall_of_light')
        ->firstOrFail();

    $farStep = Spell::query()
        ->where('key', 'far_step')
        ->firstOrFail();

    $infernalCalling = Spell::query()
        ->where('key', 'infernal_calling')
        ->firstOrFail();

    $synapticStatic = Spell::query()
        ->where('key', 'synaptic_static')
        ->firstOrFail();

    $transmuteRock = Spell::query()
        ->where('key', 'transmute_rock')
        ->firstOrFail();

    //Controlla conteggi, identità, scuole e concentrazione
    expect($levelFiveSpells->count())
        ->toBe(16)
        ->and($levelFiveSpells->distinct('canonical_key')->count())
        ->toBe(16)
        ->and($skillEmpowerment->version_key)
        ->toBe('xgte_2017')
        ->and($skillEmpowerment->spellSchool->key)
        ->toBe('transmutation')
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 5)
                ->where('concentration', true)
                ->count()
        )->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 5)
                ->where('ritual', true)
                ->count()
        )->toBe(0);

    //Controlla tutti i componenti materiali normalizzati
    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 5)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query
                ->where('version_key', 'xgte_2017')
                ->where('level', 5);
        })
        ->count();

    $missingDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 5)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 5)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    $dawnMaterial = $dawn
        ->materialComponents()
        ->firstOrFail();

    $steelMaterial = $steelWindStrike
        ->materialComponents()
        ->firstOrFail();

    $infernalMaterial = $infernalCalling
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(7)
        ->and($materialDetailCount)
        ->toBe(7)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and((float) $dawnMaterial->cost_amount)
        ->toBe(100.0)
        ->and($dawnMaterial->focus_replaceable)
        ->toBeFalse()
        ->and((float) $steelMaterial->cost_amount)
        ->toBe(1.0)
        ->and($steelMaterial->currency->code)
        ->toBe('ma')
        ->and((float) $infernalMaterial->cost_amount)
        ->toBe(999.0)
        ->and($infernalMaterial->consumed)
        ->toBeFalse();

    //Controlla tempi di lancio, tiri salvezza e bersagli
    expect($holyWeapon->casting_time_type)
        ->toBe('bonus_action')
        ->and($holyWeapon->targetProfile->can_target_objects)
        ->toBeTrue()
        ->and($steelWindStrike->attack_type)
        ->toBe('melee')
        ->and($steelWindStrike->targetProfile->target_count)
        ->toBe(5)
        ->and($danseMacabre->targetProfile->can_target_objects)
        ->toBeTrue()
        ->and($danseMacabre->targetProfile->target_count)
        ->toBe(5)
        ->and($enervation->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($negativeEnergyFlood->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($farStep->casting_time_type)
        ->toBe('bonus_action')
        ->and($farStep->targetProfile->can_target_self)
        ->toBeTrue();

    //Controlla le forme e le dimensioni delle aree
    expect($dawn->targetProfile->area_shape)
        ->toBe('cylinder')
        ->and($dawn->targetProfile->area_size_meters)
        ->toBe(9.144)
        ->and($dawn->targetProfile->area_secondary_size_meters)
        ->toBe(12.192)
        ->and($controlWinds->range)
        ->toBe(91.44)
        ->and($controlWinds->targetProfile->area_shape)
        ->toBe('cube')
        ->and($controlWinds->targetProfile->area_size_meters)
        ->toBe(30.48)
        ->and($maelstrom->targetProfile->area_shape)
        ->toBe('cylinder')
        ->and($maelstrom->targetProfile->area_secondary_size_meters)
        ->toBe(1.524)
        ->and($wallOfLight->targetProfile->area_shape)
        ->toBe('wall')
        ->and($wallOfLight->attack_type)
        ->toBe('ranged')
        ->and($synapticStatic->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($synapticStatic->targetProfile->area_size_meters)
        ->toBe(6.096)
        ->and($transmuteRock->duration_type)
        ->toBe('until_dispelled')
        ->and($transmuteRock->targetProfile->area_shape)
        ->toBe('cube');

    //Controlla descrizioni, profili e riferimenti alle pagine
    $spellIds = $levelFiveSpells->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $reference = $transmuteRock
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(16)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 5)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(16)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 5)
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
