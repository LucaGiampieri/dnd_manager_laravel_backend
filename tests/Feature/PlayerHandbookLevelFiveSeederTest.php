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
it('crea tutti gli incantesimi di quinto livello senza duplicati', function () {
    //Recupera soltanto gli incantesimi PHB 2014 di 5° livello
    $levelFiveSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 5);

    $animateObjects = Spell::query()
        ->where('key', 'animate_objects')
        ->firstOrFail();

    $hallow = Spell::query()
        ->where('key', 'hallow')
        ->firstOrFail();

    expect($levelFiveSpells->count())
        ->toBe(42)
        ->and($levelFiveSpells->distinct('canonical_key')->count())
        ->toBe(42)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->count()
        )->toBe(275)
        ->and($animateObjects->name)
        ->toBe('Animare Oggetti')
        ->and($animateObjects->version_key)
        ->toBe('phb_2014')
        ->and($animateObjects->is_legacy)
        ->toBeFalse()
        ->and($animateObjects->spellSchool->key)
        ->toBe('transmutation')
        ->and($hallow->spellSchool->key)
        ->toBe('evocation');
});

//Verifica aree, tempi di lancio e rituali
it('salva aree tempi di lancio e rituali', function () {
    //Recupera incantesimi con strutture differenti
    $coneOfCold = Spell::query()
        ->where('key', 'cone_of_cold')
        ->firstOrFail();

    $conjureVolley = Spell::query()
        ->where('key', 'conjure_volley')
        ->firstOrFail();

    $wallOfForce = Spell::query()
        ->where('key', 'wall_of_force')
        ->firstOrFail();

    $awaken = Spell::query()
        ->where('key', 'awaken')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 5)
        ->where('ritual', true)
        ->count();

    expect($coneOfCold->targetProfile->area_shape)
        ->toBe('cone')
        ->and($coneOfCold->targetProfile->area_size_meters)
        ->toBe(18.288)
        ->and($conjureVolley->targetProfile->area_shape)
        ->toBe('cylinder')
        ->and(
            $conjureVolley
                ->targetProfile
                ->area_secondary_size_meters
        )->toBe(6.096)
        ->and($wallOfForce->targetProfile->area_shape)
        ->toBe('wall')
        ->and($awaken->casting_time_type)
        ->toBe('hour')
        ->and($awaken->casting_time_value)
        ->toBe(8)
        ->and($ritualCount)
        ->toBe(4);
});

//Verifica i componenti materiali semplici e misti
it('normalizza tutti i componenti materiali', function () {
    //Conta gli incantesimi che dichiarano la componente M
    $materialSpellCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('material_component', true)
        ->count();

    //Cerca eventuali componenti mancanti o inattesi
    $missingDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    //Recupera il caso con costi e consumi differenti
    $legendLore = Spell::query()
        ->where('key', 'legend_lore')
        ->firstOrFail();

    $incense = $legendLore->materialComponents()
        ->where('key', 'incense')
        ->firstOrFail();

    $ivory = $legendLore->materialComponents()
        ->where('key', 'ivory_strips')
        ->firstOrFail();

    //Recupera un focus costoso ma riutilizzabile
    $scryingFocus = Spell::query()
        ->where('key', 'scrying')
        ->firstOrFail()
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(148)
        ->and(SpellMaterialComponent::query()->count())
        ->toBe(149)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and($legendLore->materialComponents()->count())
        ->toBe(2)
        ->and((float) $incense->cost_amount)
        ->toBe(250.0)
        ->and($incense->consumed)
        ->toBeTrue()
        ->and((float) $ivory->cost_amount)
        ->toBe(200.0)
        ->and($ivory->quantity)
        ->toBe('4.000')
        ->and($ivory->consumed)
        ->toBeFalse()
        ->and((float) $scryingFocus->cost_amount)
        ->toBe(1000.0)
        ->and($scryingFocus->consumed)
        ->toBeFalse();
});

//Verifica attacchi e tiri salvezza rappresentativi
it('salva attacchi e tiri salvezza', function () {
    //Recupera incantesimi con meccaniche differenti
    $contagion = Spell::query()
        ->where('key', 'contagion')
        ->firstOrFail();

    $cloudkill = Spell::query()
        ->where('key', 'cloudkill')
        ->firstOrFail();

    $dominatePerson = Spell::query()
        ->where('key', 'dominate_person')
        ->firstOrFail();

    expect($contagion->attack_type)
        ->toBe('melee')
        ->and($contagion->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($cloudkill->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($cloudkill->save_success_damage)
        ->toBe('half')
        ->and($dominatePerson->savingThrowAbility->short_name)
        ->toBe('SAG');
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 5° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 5)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $wallOfStone = Spell::query()
        ->where('key', 'wall_of_stone')
        ->firstOrFail();

    $reference = $wallOfStone
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(42)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 5)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(42)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 5)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(287)
        ->and($reference->sourceBook->slug)
        ->toBe('phb-2014')
        ->and($reference->official_text)
        ->toBeNull();
});
