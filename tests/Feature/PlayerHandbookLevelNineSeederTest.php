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
it('crea tutti gli incantesimi di nono livello senza duplicati', function () {
    //Recupera soltanto gli incantesimi PHB 2014 di 9° livello
    $levelNineSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 9);

    $wish = Spell::query()
        ->where('key', 'wish')
        ->firstOrFail();

    $weird = Spell::query()
        ->where('key', 'weird')
        ->firstOrFail();

    expect($levelNineSpells->count())
        ->toBe(16)
        ->and($levelNineSpells->distinct('canonical_key')->count())
        ->toBe(16)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', '<=', 9)
                ->count()
        )->toBe(361)
        ->and($wish->name)
        ->toBe('Desiderio')
        ->and($wish->version_key)
        ->toBe('phb_2014')
        ->and($wish->is_legacy)
        ->toBeFalse()
        ->and($wish->spellSchool->key)
        ->toBe('conjuration')
        ->and($weird->name)
        ->toBe('Fatale')
        ->and($weird->spellSchool->key)
        ->toBe('illusion');
});

//Verifica aree, tempi di lancio, concentrazione e rituali
it('salva aree tempi di lancio e rituali', function () {
    //Recupera incantesimi con strutture differenti
    $weird = Spell::query()
        ->where('key', 'weird')
        ->firstOrFail();

    $prismaticWall = Spell::query()
        ->where('key', 'prismatic_wall')
        ->firstOrFail();

    $meteorSwarm = Spell::query()
        ->where('key', 'meteor_swarm')
        ->firstOrFail();

    $imprisonment = Spell::query()
        ->where('key', 'imprisonment')
        ->firstOrFail();

    $astralProjection = Spell::query()
        ->where('key', 'astral_projection')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 9)
        ->where('ritual', true)
        ->count();

    expect($weird->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($weird->targetProfile->area_size_meters)
        ->toBe(9.144)
        ->and($prismaticWall->targetProfile->area_shape)
        ->toBe('special')
        ->and($prismaticWall->targetProfile->notes)
        ->not->toBeEmpty()
        ->and($meteorSwarm->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($meteorSwarm->targetProfile->target_count)
        ->toBe(4)
        ->and($imprisonment->casting_time_type)
        ->toBe('minute')
        ->and($imprisonment->duration_type)
        ->toBe('until_dispelled')
        ->and($astralProjection->casting_time_type)
        ->toBe('hour')
        ->and($astralProjection->duration_type)
        ->toBe('special')
        ->and($ritualCount)
        ->toBe(0);
});

//Verifica i componenti materiali semplici, multipli e consumati
it('normalizza tutti i componenti materiali', function () {
    //Conta gli incantesimi fino al 9° livello con componente M
    $materialSpellCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 9)
        ->where('material_component', true)
        ->count();

    //Cerca eventuali componenti mancanti o inattesi
    $missingDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 9)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 9)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    //Conta soltanto i componenti degli incantesimi fino al 9° livello
    $materialComponentCount = SpellMaterialComponent::query()
        ->whereHas(
            'spell',
            fn ($query) => $query
                ->where('version_key', 'phb_2014')
                ->where('level', '<=', 9)
        )
        ->count();

    //Recupera i componenti consumati della Proiezione Astrale
    $astralProjection = Spell::query()
        ->where('key', 'astral_projection')
        ->firstOrFail();

    $jacinth = $astralProjection->materialComponents()
        ->where('key', 'jacinth')
        ->firstOrFail();

    //Recupera la componente variabile di Imprigionare
    $imprisonmentReagent = Spell::query()
        ->where('key', 'imprisonment')
        ->firstOrFail()
        ->materialComponents()
        ->where('key', 'imprisonment_reagent')
        ->firstOrFail();

    //Recupera i diamanti consumati da Resurrezione Pura
    $trueResurrection = Spell::query()
        ->where('key', 'true_resurrection')
        ->firstOrFail();

    $diamonds = $trueResurrection->materialComponents()
        ->where('key', 'diamonds')
        ->firstOrFail();

    //Recupera il diadema riutilizzabile di Trasformazione
    $jadeCirclet = Spell::query()
        ->where('key', 'shapechange')
        ->firstOrFail()
        ->materialComponents()
        ->where('key', 'jade_circlet')
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(198)
        ->and($materialComponentCount)
        ->toBe(214)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and($astralProjection->materialComponents()->count())
        ->toBe(2)
        ->and((float) $jacinth->cost_amount)
        ->toBe(1000.0)
        ->and($jacinth->consumed)
        ->toBeTrue()
        ->and((float) $imprisonmentReagent->cost_amount)
        ->toBe(500.0)
        ->and($imprisonmentReagent->consumed)
        ->toBeFalse()
        ->and($trueResurrection->materialComponents()->count())
        ->toBe(2)
        ->and((float) $diamonds->cost_amount)
        ->toBe(25000.0)
        ->and($diamonds->consumed)
        ->toBeTrue()
        ->and((float) $jadeCirclet->cost_amount)
        ->toBe(1500.0)
        ->and($jadeCirclet->consumed)
        ->toBeFalse();
});

//Verifica tiri salvezza e bersagli rappresentativi
it('salva tiri salvezza e bersagli', function () {
    //Recupera incantesimi con meccaniche differenti
    $weird = Spell::query()
        ->where('key', 'weird')
        ->firstOrFail();

    $imprisonment = Spell::query()
        ->where('key', 'imprisonment')
        ->firstOrFail();

    $truePolymorph = Spell::query()
        ->where('key', 'true_polymorph')
        ->firstOrFail();

    $meteorSwarm = Spell::query()
        ->where('key', 'meteor_swarm')
        ->firstOrFail();

    $astralProjection = Spell::query()
        ->where('key', 'astral_projection')
        ->firstOrFail();

    $shapechange = Spell::query()
        ->where('key', 'shapechange')
        ->firstOrFail();

    expect($weird->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($imprisonment->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($truePolymorph->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($truePolymorph->targetProfile->can_target_objects)
        ->toBeTrue()
        ->and($meteorSwarm->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($meteorSwarm->save_success_damage)
        ->toBe('half')
        ->and($astralProjection->targetProfile->target_count)
        ->toBe(9)
        ->and($astralProjection->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($shapechange->targetProfile->can_target_self)
        ->toBeTrue();
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 9° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 9)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $trueResurrection = Spell::query()
        ->where('key', 'true_resurrection')
        ->firstOrFail();

    $reference = $trueResurrection
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(16)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 9)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(16)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 9)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(270)
        ->and($reference->sourceBook->slug)
        ->toBe('phb-2014')
        ->and($reference->official_text)
        ->toBeNull();
});
