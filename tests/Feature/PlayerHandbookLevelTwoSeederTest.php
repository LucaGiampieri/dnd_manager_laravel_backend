<?php

use App\Models\SourceReference;
use App\Models\Spell;
use Database\Seeders\PlayerHandbookSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce due volte il catalogo per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(PlayerHandbookSpellSeeder::class);
    $this->seed(PlayerHandbookSpellSeeder::class);
});

//Verifica la presenza di tutti gli incantesimi di 2° livello
it('crea tutti gli incantesimi di secondo livello senza duplicati', function () {
    //Recupera soltanto la versione PHB 2014 di 2° livello
    $levelTwoSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 2);

    expect($levelTwoSpells->count())
    ->toBe(59)
    ->and(
        $levelTwoSpells
            ->distinct('canonical_key')
            ->count()
    )->toBe(59);
});

//Verifica identità, versionamento e scuole differenti
it('salva identità e scuole degli incantesimi', function () {
    //Recupera alcuni incantesimi rappresentativi
    $aid = Spell::query()
        ->where('key', 'aid')
        ->firstOrFail();

    $mistyStep = Spell::query()
        ->where('key', 'misty_step')
        ->firstOrFail();

    $phantasmalForce = Spell::query()
        ->where('key', 'phantasmal_force')
        ->firstOrFail();

    expect($aid->name)
        ->toBe('Aiuto')
        ->and($aid->level)
        ->toBe(2)
        ->and($aid->canonical_key)
        ->toBe('aid')
        ->and($aid->version_key)
        ->toBe('phb_2014')
        ->and($aid->is_legacy)
        ->toBeFalse()
        ->and($aid->spellSchool->key)
        ->toBe('abjuration')
        ->and($mistyStep->spellSchool->key)
        ->toBe('conjuration')
        ->and($phantasmalForce->spellSchool->key)
        ->toBe('illusion');
});

//Verifica sfere, linee, cilindri e cubi
it('salva correttamente le aree degli incantesimi', function () {
    //Recupera incantesimi con aree di forma differente
    $calmEmotions = Spell::query()
        ->where('key', 'calm_emotions')
        ->firstOrFail();

    $gustOfWind = Spell::query()
        ->where('key', 'gust_of_wind')
        ->firstOrFail();

    $moonbeam = Spell::query()
        ->where('key', 'moonbeam')
        ->firstOrFail();

    $web = Spell::query()
        ->where('key', 'web')
        ->firstOrFail();

    expect($calmEmotions->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($calmEmotions->targetProfile->area_size_meters)
        ->toBe(6.096)
        ->and($gustOfWind->targetProfile->area_shape)
        ->toBe('line')
        ->and($gustOfWind->targetProfile->area_secondary_size_meters)
        ->toBe(3.048)
        ->and($moonbeam->targetProfile->area_shape)
        ->toBe('cylinder')
        ->and($moonbeam->targetProfile->area_secondary_size_meters)
        ->toBe(12.192)
        ->and($web->targetProfile->area_shape)
        ->toBe('cube')
        ->and($web->targetProfile->area_size_meters)
        ->toBe(6.096);
});

//Verifica azioni bonus, lanci lunghi e rituali
it('salva tempi di lancio e rituali', function () {
    //Recupera incantesimi con tempi di lancio differenti
    $mistyStep = Spell::query()
        ->where('key', 'misty_step')
        ->firstOrFail();

    $findSteed = Spell::query()
        ->where('key', 'find_steed')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 2)
        ->where('ritual', true)
        ->count();

    expect($mistyStep->casting_time_type)
        ->toBe('bonus_action')
        ->and($findSteed->casting_time_type)
        ->toBe('minute')
        ->and($findSteed->casting_time_value)
        ->toBe(10)
        ->and($ritualCount)
        ->toBe(7);
});

//Verifica materiali costosi e componenti consumati
it('salva i materiali costosi degli incantesimi', function () {
    //Recupera incantesimi con materiali di tipo differente
    $arcaneLock = Spell::query()
        ->where('key', 'arcane_lock')
        ->firstOrFail();

    $continualFlame = Spell::query()
        ->where('key', 'continual_flame')
        ->firstOrFail();

    $wardingBond = Spell::query()
        ->where('key', 'warding_bond')
        ->firstOrFail();

    expect((float) $arcaneLock->material_cost)
        ->toBe(25.0)
        ->and($arcaneLock->material_consumed)
        ->toBeTrue()
        ->and((float) $continualFlame->material_cost)
        ->toBe(50.0)
        ->and($continualFlame->material_consumed)
        ->toBeTrue()
        ->and((float) $wardingBond->material_cost)
        ->toBe(100.0)
        ->and($wardingBond->material_consumed)
        ->toBeFalse();
});

//Verifica attacchi, tiri salvezza e lancio a livelli superiori
it('salva attacchi e tiri salvezza', function () {
    //Recupera incantesimi con meccaniche differenti
    $melfsAcidArrow = Spell::query()
        ->where('key', 'melfs_acid_arrow')
        ->firstOrFail();

    $holdPerson = Spell::query()
        ->where('key', 'hold_person')
        ->firstOrFail();

    $moonbeam = Spell::query()
        ->where('key', 'moonbeam')
        ->firstOrFail();

    $scorchingRay = Spell::query()
        ->where('key', 'scorching_ray')
        ->firstOrFail();

    expect($melfsAcidArrow->attack_type)
        ->toBe('ranged')
        ->and($holdPerson->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($moonbeam->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($moonbeam->save_success_damage)
        ->toBe('half')
        ->and($scorchingRay->targetProfile->target_count)
        ->toBe(3)
        ->and($scorchingRay->higher_levels)
        ->not->toBeNull();
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 2° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 2)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $zoneOfTruth = Spell::query()
        ->where('key', 'zone_of_truth')
        ->firstOrFail();

    $reference = $zoneOfTruth
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(59)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 2)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(59)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 2)
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
