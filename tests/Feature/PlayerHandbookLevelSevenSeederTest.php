<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\PlayerHandbookSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce due volte il catalogo per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(PlayerHandbookSpellSeeder::class);
    $this->seed(PlayerHandbookSpellSeeder::class);
});

//Verifica conteggi, identità, versionamento e scuole
it('crea tutti gli incantesimi di settimo livello senza duplicati', function () {
    //Recupera soltanto gli incantesimi PHB 2014 di 7° livello
    $levelSevenSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 7);

    $sequester = Spell::query()
        ->where('key', 'sequester')
        ->firstOrFail();

    $fingerOfDeath = Spell::query()
        ->where('key', 'finger_of_death')
        ->firstOrFail();

    expect($levelSevenSpells->count())
        ->toBe(20)
        ->and($levelSevenSpells->distinct('canonical_key')->count())
        ->toBe(20)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', '<=', 7)
                ->count()
        )->toBe(327)
        ->and($sequester->name)
        ->toBe('Celare')
        ->and($sequester->version_key)
        ->toBe('phb_2014')
        ->and($sequester->is_legacy)
        ->toBeFalse()
        ->and($sequester->spellSchool->key)
        ->toBe('transmutation')
        ->and($fingerOfDeath->spellSchool->key)
        ->toBe('necromancy');
});

//Verifica aree, tempi di lancio, concentrazione e rituali
it('salva aree tempi di lancio e rituali', function () {
    //Recupera incantesimi con strutture differenti
    $forcecage = Spell::query()
        ->where('key', 'forcecage')
        ->firstOrFail();

    $reverseGravity = Spell::query()
        ->where('key', 'reverse_gravity')
        ->firstOrFail();

    $mirageArcane = Spell::query()
        ->where('key', 'mirage_arcane')
        ->firstOrFail();

    $fireStorm = Spell::query()
        ->where('key', 'fire_storm')
        ->firstOrFail();

    $etherealness = Spell::query()
        ->where('key', 'etherealness')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 7)
        ->where('ritual', true)
        ->count();

    expect($forcecage->targetProfile->area_shape)
        ->toBe('special')
        ->and($forcecage->duration_type)
        ->toBe('hour')
        ->and($reverseGravity->targetProfile->area_shape)
        ->toBe('cylinder')
        ->and($reverseGravity->targetProfile->area_size_meters)
        ->toBe(15.24)
        ->and(
            $reverseGravity
                ->targetProfile
                ->area_secondary_size_meters
        )->toBe(30.48)
        ->and($mirageArcane->casting_time_value)
        ->toBe(10)
        ->and($mirageArcane->casting_time_type)
        ->toBe('minute')
        ->and($mirageArcane->targetProfile->area_shape)
        ->toBe('square')
        ->and($fireStorm->targetProfile->area_shape)
        ->toBe('special')
        ->and($fireStorm->targetProfile->area_size_meters)
        ->toBe(3.048)
        ->and($etherealness->duration_value)
        ->toBe(8)
        ->and($ritualCount)
        ->toBe(0);
});

//Verifica i componenti materiali semplici, multipli e consumati
it('normalizza tutti i componenti materiali', function () {
    //Conta gli incantesimi fino al 7° livello con componente M
    $materialSpellCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 7)
        ->where('material_component', true)
        ->count();

    //Cerca eventuali componenti mancanti o inattesi
    $missingDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 7)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 7)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    //Conta soltanto i componenti degli incantesimi fino al 7° livello
    $materialComponentCount = SpellMaterialComponent::query()
        ->whereHas(
            'spell',
            fn ($query) => $query
                ->where('version_key', 'phb_2014')
                ->where('level', '<=', 7)
        )
        ->count();

    //Recupera la mistura costosa e consumata di Celare
    $sequesterMaterial = Spell::query()
        ->where('key', 'sequester')
        ->firstOrFail()
        ->materialComponents()
        ->where('key', 'gem_dust_mixture')
        ->firstOrFail();

    //Recupera i tre componenti distinti della Reggia
    $mansion = Spell::query()
        ->where('key', 'mordenkainens_magnificent_mansion')
        ->firstOrFail();

    $silverSpoon = $mansion->materialComponents()
        ->where('key', 'silver_spoon')
        ->firstOrFail();

    //Recupera il diamante consumato da Resurrezione
    $resurrectionMaterial = Spell::query()
        ->where('key', 'resurrection')
        ->firstOrFail()
        ->materialComponents()
        ->firstOrFail();

    //Recupera i tre componenti del Simulacro
    $simulacrum = Spell::query()
        ->where('key', 'simulacrum')
        ->firstOrFail();

    $rubyDust = $simulacrum->materialComponents()
        ->where('key', 'ruby_dust')
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(182)
        ->and($materialComponentCount)
        ->toBe(193)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and((float) $sequesterMaterial->cost_amount)
        ->toBe(5000.0)
        ->and($sequesterMaterial->consumed)
        ->toBeTrue()
        ->and($mansion->materialComponents()->count())
        ->toBe(3)
        ->and((float) $silverSpoon->cost_amount)
        ->toBe(5.0)
        ->and($silverSpoon->consumed)
        ->toBeFalse()
        ->and((float) $resurrectionMaterial->cost_amount)
        ->toBe(1000.0)
        ->and($resurrectionMaterial->consumed)
        ->toBeTrue()
        ->and($simulacrum->materialComponents()->count())
        ->toBe(3)
        ->and((float) $rubyDust->cost_amount)
        ->toBe(1500.0)
        ->and($rubyDust->consumed)
        ->toBeTrue();
});

//Verifica tiri salvezza, attacchi e bersagli rappresentativi
it('salva tiri salvezza attacchi e bersagli', function () {
    //Recupera incantesimi con meccaniche differenti
    $fingerOfDeath = Spell::query()
        ->where('key', 'finger_of_death')
        ->firstOrFail();

    $divineWord = Spell::query()
        ->where('key', 'divine_word')
        ->firstOrFail();

    $reverseGravity = Spell::query()
        ->where('key', 'reverse_gravity')
        ->firstOrFail();

    $planeShift = Spell::query()
        ->where('key', 'plane_shift')
        ->firstOrFail();

    $teleport = Spell::query()
        ->where('key', 'teleport')
        ->firstOrFail();

    expect($fingerOfDeath->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($fingerOfDeath->save_success_damage)
        ->toBe('half')
        ->and($divineWord->savingThrowAbility->short_name)
        ->toBe('CAR')
        ->and($divineWord->casting_time_type)
        ->toBe('bonus_action')
        ->and($reverseGravity->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($reverseGravity->save_success_damage)
        ->toBe('none')
        ->and($planeShift->attack_type)
        ->toBe('melee')
        ->and($planeShift->savingThrowAbility->short_name)
        ->toBe('CAR')
        ->and($planeShift->targetProfile->target_count)
        ->toBe(9)
        ->and($planeShift->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($teleport->targetProfile->target_count)
        ->toBe(9)
        ->and($teleport->targetProfile->can_target_objects)
        ->toBeTrue();
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 7° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 7)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $teleport = Spell::query()
        ->where('key', 'teleport')
        ->firstOrFail();

    $reference = $teleport
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(20)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 7)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(20)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 7)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(283)
        ->and($reference->sourceBook->slug)
        ->toBe('phb-2014')
        ->and($reference->official_text)
        ->toBeNull();
});
