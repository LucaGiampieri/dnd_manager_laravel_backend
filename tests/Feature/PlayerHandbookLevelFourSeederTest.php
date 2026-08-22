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

//Verifica la presenza di tutti gli incantesimi di 4° livello
it('crea tutti gli incantesimi di quarto livello senza duplicati', function () {
    //Recupera soltanto la versione PHB 2014 di 4° livello
    $levelFourSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 4);

    expect($levelFourSpells->count())
        ->toBe(35)
        ->and(
            $levelFourSpells
                ->distinct('canonical_key')
                ->count()
        )->toBe(35);
});

//Verifica identità, versionamento e scuole differenti
it('salva identità e scuole degli incantesimi', function () {
    //Recupera alcuni incantesimi rappresentativi
    $banishment = Spell::query()
        ->where('key', 'banishment')
        ->firstOrFail();

    $polymorph = Spell::query()
        ->where('key', 'polymorph')
        ->firstOrFail();

    $phantasmalKiller = Spell::query()
        ->where('key', 'phantasmal_killer')
        ->firstOrFail();

    expect($banishment->name)
        ->toBe('Esilio')
        ->and($banishment->level)
        ->toBe(4)
        ->and($banishment->canonical_key)
        ->toBe('banishment')
        ->and($banishment->version_key)
        ->toBe('phb_2014')
        ->and($banishment->is_legacy)
        ->toBeFalse()
        ->and($banishment->spellSchool->key)
        ->toBe('abjuration')
        ->and($polymorph->spellSchool->key)
        ->toBe('transmutation')
        ->and($phantasmalKiller->spellSchool->key)
        ->toBe('illusion');
});

//Verifica cubi, cilindri, quadrati, emanazioni e muri
it('salva correttamente le aree degli incantesimi', function () {
    //Recupera incantesimi con aree di forma differente
    $controlWater = Spell::query()
        ->where('key', 'control_water')
        ->firstOrFail();

    $blackTentacles = Spell::query()
        ->where('key', 'evards_black_tentacles')
        ->firstOrFail();

    $iceStorm = Spell::query()
        ->where('key', 'ice_storm')
        ->firstOrFail();

    $auraOfLife = Spell::query()
        ->where('key', 'aura_of_life')
        ->firstOrFail();

    $wallOfFire = Spell::query()
        ->where('key', 'wall_of_fire')
        ->firstOrFail();

    expect($controlWater->targetProfile->area_shape)
        ->toBe('cube')
        ->and($controlWater->targetProfile->area_size_meters)
        ->toBe(30.48)
        ->and($blackTentacles->targetProfile->area_shape)
        ->toBe('square')
        ->and($iceStorm->targetProfile->area_shape)
        ->toBe('cylinder')
        ->and($iceStorm->targetProfile->area_secondary_size_meters)
        ->toBe(12.192)
        ->and($auraOfLife->targetProfile->area_shape)
        ->toBe('emanation')
        ->and($wallOfFire->targetProfile->area_shape)
        ->toBe('wall');
});

//Verifica azioni bonus, lanci lunghi e rituali
it('salva tempi di lancio e rituali', function () {
    //Recupera incantesimi con tempi di lancio differenti
    $graspingVine = Spell::query()
        ->where('key', 'grasping_vine')
        ->firstOrFail();

    $fabricate = Spell::query()
        ->where('key', 'fabricate')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 4)
        ->where('ritual', true)
        ->count();

    expect($graspingVine->casting_time_type)
        ->toBe('bonus_action')
        ->and($fabricate->casting_time_type)
        ->toBe('minute')
        ->and($fabricate->casting_time_value)
        ->toBe(10)
        ->and($ritualCount)
        ->toBe(1);
});

//Verifica materiali costosi e componenti consumati
it('salva i materiali costosi degli incantesimi', function () {
    //Recupera incantesimi con materiali di tipo differente
    $divination = Spell::query()
        ->where('key', 'divination')
        ->firstOrFail();

    $secretChest = Spell::query()
        ->where('key', 'leomunds_secret_chest')
        ->firstOrFail();

    $stoneskin = Spell::query()
        ->where('key', 'stoneskin')
        ->firstOrFail();

    expect((float) $divination->material_cost)
        ->toBe(25.0)
        ->and($divination->material_consumed)
        ->toBeTrue()
        ->and((float) $secretChest->material_cost)
        ->toBe(5050.0)
        ->and($secretChest->material_consumed)
        ->toBeFalse()
        ->and((float) $stoneskin->material_cost)
        ->toBe(100.0)
        ->and($stoneskin->material_consumed)
        ->toBeTrue();
});

//Verifica tiri salvezza, danni parziali e lancio superiore
it('salva attacchi e tiri salvezza', function () {
    //Recupera incantesimi con meccaniche differenti
    $banishment = Spell::query()
        ->where('key', 'banishment')
        ->firstOrFail();

    $blight = Spell::query()
        ->where('key', 'blight')
        ->firstOrFail();

    $guardianOfFaith = Spell::query()
        ->where('key', 'guardian_of_faith')
        ->firstOrFail();

    $wallOfFire = Spell::query()
        ->where('key', 'wall_of_fire')
        ->firstOrFail();

    expect($banishment->savingThrowAbility->short_name)
        ->toBe('CAR')
        ->and($blight->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($blight->save_success_damage)
        ->toBe('half')
        ->and($guardianOfFaith->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($wallOfFire->higher_levels)
        ->not->toBeNull();
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 4° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 4)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $wallOfFire = Spell::query()
        ->where('key', 'wall_of_fire')
        ->firstOrFail();

    $reference = $wallOfFire
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(35)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 4)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(35)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 4)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(285)
        ->and($reference->sourceBook->slug)
        ->toBe('phb-2014')
        ->and($reference->official_text)
        ->toBeNull();
});
