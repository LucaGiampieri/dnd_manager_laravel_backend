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

//Verifica la presenza di tutti gli incantesimi di 3° livello
it('crea tutti gli incantesimi di terzo livello senza duplicati', function () {
    //Recupera soltanto la versione PHB 2014 di 3° livello
    $levelThreeSpells = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 3);

    expect($levelThreeSpells->count())
        ->toBe(50)
        ->and(
            $levelThreeSpells
                ->distinct('canonical_key')
                ->count()
        )->toBe(50);
});

//Verifica identità, versionamento e scuole differenti
it('salva identità e scuole degli incantesimi', function () {
    //Recupera alcuni incantesimi rappresentativi
    $counterspell = Spell::query()
        ->where('key', 'counterspell')
        ->firstOrFail();

    $fireball = Spell::query()
        ->where('key', 'fireball')
        ->firstOrFail();

    $animateDead = Spell::query()
        ->where('key', 'animate_dead')
        ->firstOrFail();

    expect($counterspell->name)
        ->toBe('Controincantesimo')
        ->and($counterspell->level)
        ->toBe(3)
        ->and($counterspell->canonical_key)
        ->toBe('counterspell')
        ->and($counterspell->version_key)
        ->toBe('phb_2014')
        ->and($counterspell->is_legacy)
        ->toBeFalse()
        ->and($counterspell->spellSchool->key)
        ->toBe('abjuration')
        ->and($fireball->spellSchool->key)
        ->toBe('evocation')
        ->and($animateDead->spellSchool->key)
        ->toBe('necromancy');
});

//Verifica coni, linee, cilindri, cupole e muri
it('salva correttamente le aree degli incantesimi', function () {
    //Recupera incantesimi con aree di forma differente
    $fear = Spell::query()
        ->where('key', 'fear')
        ->firstOrFail();

    $lightningBolt = Spell::query()
        ->where('key', 'lightning_bolt')
        ->firstOrFail();

    $sleetStorm = Spell::query()
        ->where('key', 'sleet_storm')
        ->firstOrFail();

    $tinyHut = Spell::query()
        ->where('key', 'leomunds_tiny_hut')
        ->firstOrFail();

    $windWall = Spell::query()
        ->where('key', 'wind_wall')
        ->firstOrFail();

    expect($fear->targetProfile->area_shape)
        ->toBe('cone')
        ->and($fear->targetProfile->area_size_meters)
        ->toBe(9.144)
        ->and($lightningBolt->targetProfile->area_shape)
        ->toBe('line')
        ->and($lightningBolt->targetProfile->area_secondary_size_meters)
        ->toBe(1.524)
        ->and($sleetStorm->targetProfile->area_shape)
        ->toBe('cylinder')
        ->and($sleetStorm->targetProfile->area_secondary_size_meters)
        ->toBe(6.096)
        ->and($tinyHut->targetProfile->area_shape)
        ->toBe('hemisphere')
        ->and($windWall->targetProfile->area_shape)
        ->toBe('wall');
});

//Verifica reazioni, azioni bonus, lanci lunghi e rituali
it('salva tempi di lancio e rituali', function () {
    //Recupera incantesimi con tempi di lancio differenti
    $counterspell = Spell::query()
        ->where('key', 'counterspell')
        ->firstOrFail();

    $massHealingWord = Spell::query()
        ->where('key', 'mass_healing_word')
        ->firstOrFail();

    $glyphOfWarding = Spell::query()
        ->where('key', 'glyph_of_warding')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 3)
        ->where('ritual', true)
        ->count();

    expect($counterspell->casting_time_type)
        ->toBe('reaction')
        ->and($counterspell->casting_trigger)
        ->not->toBeNull()
        ->and($massHealingWord->casting_time_type)
        ->toBe('bonus_action')
        ->and($glyphOfWarding->casting_time_type)
        ->toBe('hour')
        ->and($ritualCount)
        ->toBe(6);
});

//Verifica materiali costosi e componenti consumati
it('salva i materiali costosi degli incantesimi', function () {
    //Recupera incantesimi con materiali di tipo differente
    $clairvoyance = Spell::query()
        ->where('key', 'clairvoyance')
        ->firstOrFail();

    $glyphOfWarding = Spell::query()
        ->where('key', 'glyph_of_warding')
        ->firstOrFail();

    $nondetection = Spell::query()
        ->where('key', 'nondetection')
        ->firstOrFail();

    $revivify = Spell::query()
        ->where('key', 'revivify')
        ->firstOrFail();

    expect((float) $clairvoyance->material_cost)
        ->toBe(100.0)
        ->and($clairvoyance->material_consumed)
        ->toBeFalse()
        ->and((float) $glyphOfWarding->material_cost)
        ->toBe(200.0)
        ->and($glyphOfWarding->material_consumed)
        ->toBeTrue()
        ->and((float) $nondetection->material_cost)
        ->toBe(25.0)
        ->and($nondetection->material_consumed)
        ->toBeTrue()
        ->and((float) $revivify->material_cost)
        ->toBe(300.0)
        ->and($revivify->material_consumed)
        ->toBeTrue();
});

//Verifica attacchi, tiri salvezza e bersagli multipli
it('salva attacchi e tiri salvezza', function () {
    //Recupera incantesimi con meccaniche differenti
    $fireball = Spell::query()
        ->where('key', 'fireball')
        ->firstOrFail();

    $spiritGuardians = Spell::query()
        ->where('key', 'spirit_guardians')
        ->firstOrFail();

    $vampiricTouch = Spell::query()
        ->where('key', 'vampiric_touch')
        ->firstOrFail();

    $massHealingWord = Spell::query()
        ->where('key', 'mass_healing_word')
        ->firstOrFail();

    expect($fireball->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($fireball->save_success_damage)
        ->toBe('half')
        ->and($spiritGuardians->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($vampiricTouch->attack_type)
        ->toBe('melee')
        ->and($massHealingWord->targetProfile->target_count)
        ->toBe(6);
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e riferimenti alle pagine del phb', function () {
    //Recupera gli identificativi degli incantesimi di 3° livello
    $spellIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 3)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $windWall = Spell::query()
        ->where('key', 'wind_wall')
        ->firstOrFail();

    $reference = $windWall
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(50)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 3)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(50)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->where('level', 3)
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
