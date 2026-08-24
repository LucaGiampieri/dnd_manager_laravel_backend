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
it('crea tutti gli incantesimi di sesto livello senza duplicati', function () {
    //Recupera soltanto gli incantesimi PHB 2014 di 6° livello
    $levelSixSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 6);

    $arcaneGate = Spell::query()
        ->where('key', 'arcane_gate')
        ->firstOrFail();

    $eyebite = Spell::query()
        ->where('key', 'eyebite')
        ->firstOrFail();

    expect($levelSixSpells->count())
        ->toBe(32)
        ->and($levelSixSpells->distinct('canonical_key')->count())
        ->toBe(32)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', '<=', 6)
                ->count()
        )->toBe(307)
        ->and($arcaneGate->name)
        ->toBe('Portale Arcano')
        ->and($arcaneGate->version_key)
        ->toBe('phb_2014')
        ->and($arcaneGate->is_legacy)
        ->toBeFalse()
        ->and($arcaneGate->spellSchool->key)
        ->toBe('conjuration')
        ->and($eyebite->spellSchool->key)
        ->toBe('necromancy');
});

//Verifica aree, tempi di lancio, concentrazione e rituali
it('salva aree tempi di lancio e rituali', function () {
    //Recupera incantesimi con strutture differenti
    $arcaneGate = Spell::query()
        ->where('key', 'arcane_gate')
        ->firstOrFail();

    $bladeBarrier = Spell::query()
        ->where('key', 'blade_barrier')
        ->firstOrFail();

    $circleOfDeath = Spell::query()
        ->where('key', 'circle_of_death')
        ->firstOrFail();

    $forbiddance = Spell::query()
        ->where('key', 'forbiddance')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 6)
        ->where('ritual', true)
        ->count();

    expect((float) $arcaneGate->range)
        ->toBe(152.4)
        ->and($arcaneGate->concentration)
        ->toBeTrue()
        ->and($arcaneGate->targetProfile->target_count)
        ->toBe(2)
        ->and($bladeBarrier->targetProfile->area_shape)
        ->toBe('wall')
        ->and($bladeBarrier->targetProfile->area_size_meters)
        ->toBe(30.48)
        ->and($circleOfDeath->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($circleOfDeath->targetProfile->area_size_meters)
        ->toBe(18.288)
        ->and($forbiddance->casting_time_type)
        ->toBe('minute')
        ->and($forbiddance->casting_time_value)
        ->toBe(10)
        ->and($forbiddance->targetProfile->area_shape)
        ->toBe('special')
        ->and($ritualCount)
        ->toBe(2);
});

//Verifica i componenti materiali semplici, multipli e condizionali
it('normalizza tutti i componenti materiali', function () {
    //Conta gli incantesimi PHB che dichiarano la componente M
    $materialSpellCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 6)
        ->where('material_component', true)
        ->count();

    //Cerca eventuali componenti mancanti o inattesi
    $missingDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 6)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 6)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    //Conta soltanto i componenti degli incantesimi fino al 6° livello
    $materialComponentCount = SpellMaterialComponent::query()
        ->whereHas(
            'spell',
            fn ($query) => $query
                ->where('version_key', 'phb_2014')
                ->where('level', '<=', 6)
        )
        ->count();

    //Recupera il costo variabile di Creare Non Morti
    $createUndead = Spell::query()
        ->where('key', 'create_undead')
        ->firstOrFail();

    $blackOnyx = $createUndead->materialComponents()
        ->where('key', 'black_onyx')
        ->firstOrFail();

    //Recupera i due requisiti distinti di Scopri il Percorso
    $findThePath = Spell::query()
        ->where('key', 'find_the_path')
        ->firstOrFail();

    $divinationTools = $findThePath->materialComponents()
        ->where('key', 'divination_tools')
        ->firstOrFail();

    //Recupera il componente costoso e condizionale di Proibizione
    $forbiddance = Spell::query()
        ->where('key', 'forbiddance')
        ->firstOrFail();

    $powderedRuby = $forbiddance->materialComponents()
        ->where('key', 'powdered_ruby')
        ->firstOrFail();

    //Recupera due componenti che vengono sempre consumati
    $heroesFeastMaterial = Spell::query()
        ->where('key', 'heroes_feast')
        ->firstOrFail()
        ->materialComponents()
        ->firstOrFail();

    $globeMaterial = Spell::query()
        ->where('key', 'globe_of_invulnerability')
        ->firstOrFail()
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(170)
        ->and($materialComponentCount)
        ->toBe(176)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and($createUndead->materialComponents()->count())
        ->toBe(3)
        ->and((float) $blackOnyx->cost_amount)
        ->toBe(150.0)
        ->and($blackOnyx->unit)
        ->toBe('per cadavere')
        ->and($blackOnyx->consumed)
        ->toBeFalse()
        ->and($findThePath->materialComponents()->count())
        ->toBe(2)
        ->and((float) $divinationTools->cost_amount)
        ->toBe(100.0)
        ->and($forbiddance->materialComponents()->count())
        ->toBe(3)
        ->and((float) $powderedRuby->cost_amount)
        ->toBe(1000.0)
        ->and($powderedRuby->cost_is_minimum)
        ->toBeTrue()
        ->and($powderedRuby->consumed)
        ->toBeFalse()
        ->and($heroesFeastMaterial->consumed)
        ->toBeTrue()
        ->and($globeMaterial->consumed)
        ->toBeTrue();
});

//Verifica tiri salvezza e bersagli rappresentativi
it('salva tiri salvezza e bersagli', function () {
    //Recupera incantesimi con meccaniche differenti
    $chainLightning = Spell::query()
        ->where('key', 'chain_lightning')
        ->firstOrFail();

    $disintegrate = Spell::query()
        ->where('key', 'disintegrate')
        ->firstOrFail();

    $harm = Spell::query()
        ->where('key', 'harm')
        ->firstOrFail();

    $magicJar = Spell::query()
        ->where('key', 'magic_jar')
        ->firstOrFail();

    $wordOfRecall = Spell::query()
        ->where('key', 'word_of_recall')
        ->firstOrFail();

    expect($chainLightning->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($chainLightning->save_success_damage)
        ->toBe('half')
        ->and($chainLightning->targetProfile->target_count)
        ->toBe(4)
        ->and($chainLightning->targetProfile->can_target_objects)
        ->toBeTrue()
        ->and($disintegrate->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($disintegrate->save_success_damage)
        ->toBe('none')
        ->and($harm->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($harm->save_success_damage)
        ->toBe('half')
        ->and($magicJar->savingThrowAbility->short_name)
        ->toBe('CAR')
        ->and($wordOfRecall->targetProfile->target_count)
        ->toBe(6)
        ->and($wordOfRecall->targetProfile->can_target_self)
        ->toBeTrue();
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 6° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 6)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $wordOfRecall = Spell::query()
        ->where('key', 'word_of_recall')
        ->firstOrFail();

    $reference = $wordOfRecall
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(32)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 6)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(32)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 6)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(289)
        ->and($reference->sourceBook->slug)
        ->toBe('phb-2014')
        ->and($reference->official_text)
        ->toBeNull();
});
