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

//Verifica la presenza di tutti gli incantesimi di 1° livello
it('crea tutti gli incantesimi di primo livello senza duplicati', function () {
    //Recupera soltanto la versione PHB 2014 di 1° livello
    $levelOneSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 1);

    expect($levelOneSpells->count())
      ->toBe(62)
      ->and(
          $levelOneSpells
              ->distinct('canonical_key')
              ->count()
      )->toBe(62);
});

//Verifica identità, scuole e versionamento
it('salva identità e scuole degli incantesimi', function () {
    //Recupera incantesimi appartenenti a scuole differenti
    $shield = Spell::query()
        ->where('key', 'shield')
        ->firstOrFail();

    $findFamiliar = Spell::query()
        ->where('key', 'find_familiar')
        ->firstOrFail();

    $sleep = Spell::query()
        ->where('key', 'sleep')
        ->firstOrFail();

    expect($shield->name)
        ->toBe('Scudo')
        ->and($shield->canonical_key)
        ->toBe('shield')
        ->and($shield->version_key)
        ->toBe('phb_2014')
        ->and($shield->is_legacy)
        ->toBeFalse()
        ->and($shield->spellSchool->key)
        ->toBe('abjuration')
        ->and($findFamiliar->spellSchool->key)
        ->toBe('conjuration')
        ->and($sleep->spellSchool->key)
        ->toBe('enchantment');
});

//Verifica coni, quadrati, sfere e cubi
it('salva correttamente le aree degli incantesimi', function () {
    //Recupera alcuni incantesimi ad area rappresentativi
    $burningHands = Spell::query()
        ->where('key', 'burning_hands')
        ->firstOrFail();

    $entangle = Spell::query()
        ->where('key', 'entangle')
        ->firstOrFail();

    $fogCloud = Spell::query()
        ->where('key', 'fog_cloud')
        ->firstOrFail();

    $thunderwave = Spell::query()
        ->where('key', 'thunderwave')
        ->firstOrFail();

    expect($burningHands->targetProfile->area_shape)
        ->toBe('cone')
        ->and($burningHands->targetProfile->area_size_meters)
        ->toBe(4.572)
        ->and($entangle->targetProfile->area_shape)
        ->toBe('square')
        ->and($entangle->targetProfile->area_size_meters)
        ->toBe(6.096)
        ->and($fogCloud->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($thunderwave->targetProfile->area_shape)
        ->toBe('cube');
});

//Verifica reazioni, azioni bonus e rituali
it('salva tempi di lancio e rituali', function () {
    //Recupera incantesimi con tempi di lancio differenti
    $featherFall = Spell::query()
        ->where('key', 'feather_fall')
        ->firstOrFail();

    $shield = Spell::query()
        ->where('key', 'shield')
        ->firstOrFail();

    $healingWord = Spell::query()
        ->where('key', 'healing_word')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 1)
        ->where('ritual', true)
        ->count();

    expect($featherFall->casting_time_type)
        ->toBe('reaction')
        ->and($featherFall->casting_trigger)
        ->not->toBeNull()
        ->and($shield->casting_time_type)
        ->toBe('reaction')
        ->and($healingWord->casting_time_type)
        ->toBe('bonus_action')
        ->and($ritualCount)
        ->toBe(11);
});

//Verifica materiali costosi e componenti consumati
it('salva i materiali costosi degli incantesimi', function () {
    //Recupera incantesimi con materiali di tipo differente
    $chromaticOrb = Spell::query()
        ->where('key', 'chromatic_orb')
        ->firstOrFail();

    $findFamiliar = Spell::query()
        ->where('key', 'find_familiar')
        ->firstOrFail();

    $identify = Spell::query()
        ->where('key', 'identify')
        ->firstOrFail();

    $illusoryScript = Spell::query()
        ->where('key', 'illusory_script')
        ->firstOrFail();

    expect((float) $chromaticOrb->material_cost)
        ->toBe(50.0)
        ->and($chromaticOrb->material_consumed)
        ->toBeFalse()
        ->and((float) $findFamiliar->material_cost)
        ->toBe(10.0)
        ->and($findFamiliar->material_consumed)
        ->toBeTrue()
        ->and((float) $identify->material_cost)
        ->toBe(100.0)
        ->and($identify->material_consumed)
        ->toBeFalse()
        ->and($illusoryScript->material_consumed)
        ->toBeTrue();
});

//Verifica attacchi e tiri salvezza
it('salva attacchi e tiri salvezza', function () {
    //Recupera incantesimi con meccaniche differenti
    $burningHands = Spell::query()
        ->where('key', 'burning_hands')
        ->firstOrFail();

    $guidingBolt = Spell::query()
        ->where('key', 'guiding_bolt')
        ->firstOrFail();

    $inflictWounds = Spell::query()
        ->where('key', 'inflict_wounds')
        ->firstOrFail();

    $magicMissile = Spell::query()
        ->where('key', 'magic_missile')
        ->firstOrFail();

    expect($burningHands->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($burningHands->save_success_damage)
        ->toBe('half')
        ->and($guidingBolt->attack_type)
        ->toBe('ranged')
        ->and($inflictWounds->attack_type)
        ->toBe('melee')
        ->and($magicMissile->attack_type)
        ->toBeNull()
        ->and($magicMissile->targetProfile->target_count)
        ->toBe(3);
});

//Verifica che ogni incantesimo possieda bersaglio e riferimento al PHB
it('collega bersagli e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 1° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 1)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $witchBolt = Spell::query()
        ->where('key', 'witch_bolt')
        ->firstOrFail();

    $reference = $witchBolt
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(62)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 1)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(62)
        ->and($reference->page_start)
        ->toBe(289)
        ->and($reference->sourceBook->slug)
        ->toBe('phb-2014')
        ->and($reference->official_text)
        ->toBeNull();
});
