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
it('crea tutti gli incantesimi di ottavo livello senza duplicati', function () {
    //Recupera soltanto gli incantesimi PHB 2014 di 8° livello
    $levelEightSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 8);

    $holyAura = Spell::query()
        ->where('key', 'holy_aura')
        ->firstOrFail();

    $clone = Spell::query()
        ->where('key', 'clone')
        ->firstOrFail();

    expect($levelEightSpells->count())
        ->toBe(18)
        ->and($levelEightSpells->distinct('canonical_key')->count())
        ->toBe(18)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', '<=', 8)
                ->count()
        )->toBe(345)
        ->and($holyAura->name)
        ->toBe('Aura Sacra')
        ->and($holyAura->version_key)
        ->toBe('phb_2014')
        ->and($holyAura->is_legacy)
        ->toBeFalse()
        ->and($holyAura->spellSchool->key)
        ->toBe('abjuration')
        ->and($clone->spellSchool->key)
        ->toBe('necromancy');
});

//Verifica aree, tempi di lancio, concentrazione e rituali
it('salva aree tempi di lancio e rituali', function () {
    //Recupera incantesimi con strutture differenti
    $holyAura = Spell::query()
        ->where('key', 'holy_aura')
        ->firstOrFail();

    $antimagicField = Spell::query()
        ->where('key', 'antimagic_field')
        ->firstOrFail();

    $controlWeather = Spell::query()
        ->where('key', 'control_weather')
        ->firstOrFail();

    $tsunami = Spell::query()
        ->where('key', 'tsunami')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 8)
        ->where('ritual', true)
        ->count();

    expect($holyAura->targetProfile->area_shape)
        ->toBe('emanation')
        ->and($holyAura->targetProfile->area_size_meters)
        ->toBe(9.144)
        ->and($antimagicField->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($antimagicField->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($controlWeather->casting_time_value)
        ->toBe(10)
        ->and($controlWeather->casting_time_type)
        ->toBe('minute')
        ->and($controlWeather->duration_value)
        ->toBe(8)
        ->and($controlWeather->targetProfile->area_shape)
        ->toBe('circle')
        ->and($tsunami->targetProfile->area_shape)
        ->toBe('wall')
        ->and($tsunami->targetProfile->area_size_meters)
        ->toBe(91.44)
        ->and($tsunami->targetProfile->area_secondary_size_meters)
        ->toBe(15.24)
        ->and($ritualCount)
        ->toBe(0);
});

//Verifica i componenti materiali semplici, multipli e consumati
it('normalizza tutti i componenti materiali', function () {
    //Conta gli incantesimi fino all'8° livello con componente M
    $materialSpellCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 8)
        ->where('material_component', true)
        ->count();

    //Cerca eventuali componenti mancanti o inattesi
    $missingDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 8)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', '<=', 8)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    //Conta soltanto i componenti degli incantesimi fino all'8° livello
    $materialComponentCount = SpellMaterialComponent::query()
        ->whereHas(
            'spell',
            fn ($query) => $query
                ->where('version_key', 'phb_2014')
                ->where('level', '<=', 8)
        )
        ->count();

    //Recupera i componenti distinti di Clone
    $clone = Spell::query()
        ->where('key', 'clone')
        ->firstOrFail();

    $cloneDiamond = $clone->materialComponents()
        ->where('key', 'diamond')
        ->firstOrFail();

    $cloneVessel = $clone->materialComponents()
        ->where('key', 'sealed_vessel')
        ->firstOrFail();

    //Recupera il reliquiario costoso ma riutilizzabile di Aura Sacra
    $holyReliquary = Spell::query()
        ->where('key', 'holy_aura')
        ->firstOrFail()
        ->materialComponents()
        ->where('key', 'holy_reliquary')
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(191)
        ->and($materialComponentCount)
        ->toBe(204)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and($clone->materialComponents()->count())
        ->toBe(3)
        ->and((float) $cloneDiamond->cost_amount)
        ->toBe(1000.0)
        ->and($cloneDiamond->consumed)
        ->toBeTrue()
        ->and((float) $cloneVessel->cost_amount)
        ->toBe(2000.0)
        ->and($cloneVessel->consumed)
        ->toBeFalse()
        ->and((float) $holyReliquary->cost_amount)
        ->toBe(1000.0)
        ->and($holyReliquary->consumed)
        ->toBeFalse();
});

//Verifica tiri salvezza e bersagli rappresentativi
it('salva tiri salvezza e bersagli', function () {
    //Recupera incantesimi con meccaniche differenti
    $sunburst = Spell::query()
        ->where('key', 'sunburst')
        ->firstOrFail();

    $dominateMonster = Spell::query()
        ->where('key', 'dominate_monster')
        ->firstOrFail();

    $feeblemind = Spell::query()
        ->where('key', 'feeblemind')
        ->firstOrFail();

    $powerWordStun = Spell::query()
        ->where('key', 'power_word_stun')
        ->firstOrFail();

    $telepathy = Spell::query()
        ->where('key', 'telepathy')
        ->firstOrFail();

    expect($sunburst->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($sunburst->save_success_damage)
        ->toBe('half')
        ->and($dominateMonster->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($dominateMonster->targetProfile->target_count)
        ->toBe(1)
        ->and($feeblemind->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($powerWordStun->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($powerWordStun->targetProfile->requires_sight)
        ->toBeTrue()
        ->and($telepathy->range_type)
        ->toBe('unlimited')
        ->and($telepathy->targetProfile->target_count)
        ->toBe(1);
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 8° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 8)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $tsunami = Spell::query()
        ->where('key', 'tsunami')
        ->firstOrFail();

    $reference = $tsunami
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(18)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 8)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(18)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 8)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(288)
        ->and($reference->sourceBook->slug)
        ->toBe('phb-2014')
        ->and($reference->official_text)
        ->toBeNull();
});
