<?php

use App\Models\SourceReference;
use App\Models\Spell;
use Database\Seeders\PlayerHandbookSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce due volte i trucchetti per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(PlayerHandbookSpellSeeder::class);
    $this->seed(PlayerHandbookSpellSeeder::class);
});

//Verifica che siano presenti tutti i trucchetti del PHB
it('crea tutti i trucchetti del phb senza duplicati', function () {
    //Recupera esclusivamente i trucchetti della versione PHB 2014
    $cantrips = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 0);

    expect($cantrips->count())
        ->toBe(27)
        ->and($cantrips->distinct('canonical_key')->count())
        ->toBe(27);
});

//Verifica nomi, scuole e versionamento
it('salva identità e scuole dei trucchetti', function () {
    //Recupera alcuni incantesimi rappresentativi
    $fireBolt = Spell::query()
        ->where('key', 'fire_bolt')
        ->firstOrFail();

    $minorIllusion = Spell::query()
        ->where('key', 'minor_illusion')
        ->firstOrFail();

    $chillTouch = Spell::query()
        ->where('key', 'chill_touch')
        ->firstOrFail();

    expect($fireBolt->name)
        ->toBe('Dardo di Fuoco')
        ->and($fireBolt->canonical_key)
        ->toBe('fire_bolt')
        ->and($fireBolt->version_key)
        ->toBe('phb_2014')
        ->and($fireBolt->is_legacy)
        ->toBeFalse()
        ->and($fireBolt->spellSchool->key)
        ->toBe('evocation')
        ->and($minorIllusion->spellSchool->key)
        ->toBe('illusion')
        ->and($chillTouch->spellSchool->key)
        ->toBe('necromancy');
});

//Verifica gittate, tempi e componenti
it('salva i dati di lancio dei trucchetti', function () {
    //Recupera trucchetti con configurazioni differenti
    $eldritchBlast = Spell::query()
        ->where('key', 'eldritch_blast')
        ->firstOrFail();

    $shillelagh = Spell::query()
        ->where('key', 'shillelagh')
        ->firstOrFail();

    $mending = Spell::query()
        ->where('key', 'mending')
        ->firstOrFail();

    expect((float) $eldritchBlast->range)
        ->toBe(36.576)
        ->and($eldritchBlast->attack_type)
        ->toBe('ranged')
        ->and($shillelagh->casting_time_type)
        ->toBe('bonus_action')
        ->and($shillelagh->material_component)
        ->toBeTrue()
        ->and($mending->casting_time_type)
        ->toBe('minute')
        ->and($mending->range_type)
        ->toBe('touch');
});

//Verifica i tiri salvezza
it('assegna correttamente i tiri salvezza', function () {
    //Recupera i trucchetti basati sui tiri salvezza
    $acidSplash = Spell::query()
        ->where('key', 'acid_splash')
        ->firstOrFail();

    $poisonSpray = Spell::query()
        ->where('key', 'poison_spray')
        ->firstOrFail();

    $viciousMockery = Spell::query()
        ->where('key', 'vicious_mockery')
        ->firstOrFail();

    expect($acidSplash->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($poisonSpray->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($viciousMockery->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($acidSplash->save_success_damage)
        ->toBe('none');
});

//Verifica bersagli e possibilità di colpire oggetti
it('crea i profili dei bersagli', function () {
    //Recupera diversi tipi di bersaglio
    $acidSplash = Spell::query()
        ->where('key', 'acid_splash')
        ->firstOrFail();

    $light = Spell::query()
        ->where('key', 'light')
        ->firstOrFail();

    $guidance = Spell::query()
        ->where('key', 'guidance')
        ->firstOrFail();

    expect($acidSplash->targetProfile->target_type)
        ->toBe('creatures')
        ->and($acidSplash->targetProfile->target_count)
        ->toBe(2)
        ->and($light->targetProfile->target_type)
        ->toBe('object')
        ->and($light->targetProfile->can_target_objects)
        ->toBeTrue()
        ->and($guidance->targetProfile->can_target_self)
        ->toBeTrue();
});

//Verifica i riferimenti bibliografici
it('collega ogni trucchetto alla pagina del phb', function () {
    //Recupera soltanto gli identificativi dei trucchetti PHB
    $cantripIds = Spell::query()
        ->where('version_key', 'phb_2014')
        ->where('level', 0)
        ->pluck('id');

    //Conta soltanto i riferimenti appartenenti ai trucchetti
    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $cantripIds)
        ->where('reference_type', 'definition')
        ->count();

    //Recupera un riferimento con pagina conosciuta
    $fireBolt = Spell::query()
        ->where('key', 'fire_bolt')
        ->firstOrFail();

    $reference = $fireBolt
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(27)
        ->and($reference->sourceBook->slug)
        ->toBe('phb-2014')
        ->and($reference->page_start)
        ->toBe(242)
        ->and($reference->official_text)
        ->toBeNull()
        ->and(
            array_key_exists(
                'official_text',
                $reference->toArray()
            )
        )->toBeFalse();
});
