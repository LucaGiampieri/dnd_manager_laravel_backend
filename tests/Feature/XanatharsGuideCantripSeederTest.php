<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\PlayerHandbookSpellSeeder;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima di ogni test
uses(RefreshDatabase::class);

//Mantiene il PHB e verifica due volte l'idempotenza di Xanathar
beforeEach(function () {
    $this->seed(PlayerHandbookSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);
});

//Verifica conteggi, identità, versionamento e scuole
it('crea tutti i trucchetti di Xanathar senza duplicati', function () {
    $cantrips = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 0);

    $controlFlames = Spell::query()
        ->where('key', 'control_flames')
        ->firstOrFail();

    $tollTheDead = Spell::query()
        ->where('key', 'toll_the_dead')
        ->firstOrFail();

    expect($cantrips->count())
        ->toBe(12)
        ->and($cantrips->distinct('canonical_key')->count())
        ->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'phb_2014')
                ->count()
        )->toBe(361)
        ->and($controlFlames->name)
        ->toBe('Controllare Fiamme')
        ->and($controlFlames->canonical_key)
        ->toBe('control_flames')
        ->and($controlFlames->version_key)
        ->toBe('xgte_2017')
        ->and($controlFlames->is_legacy)
        ->toBeFalse()
        ->and($controlFlames->spellSchool->key)
        ->toBe('transmutation')
        ->and($tollTheDead->spellSchool->key)
        ->toBe('necromancy');
});

//Verifica aree, tempi di lancio e concentrazione
it('salva aree tempi di lancio e concentrazione', function () {
    $createBonfire = Spell::query()
        ->where('key', 'create_bonfire')
        ->firstOrFail();

    $thunderclap = Spell::query()
        ->where('key', 'thunderclap')
        ->firstOrFail();

    $magicStone = Spell::query()
        ->where('key', 'magic_stone')
        ->firstOrFail();

    $ritualCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 0)
        ->where('ritual', true)
        ->count();

    expect($createBonfire->targetProfile->area_shape)
        ->toBe('cube')
        ->and($createBonfire->targetProfile->area_size_meters)
        ->toBe(1.524)
        ->and($createBonfire->duration_type)
        ->toBe('minute')
        ->and($createBonfire->duration_value)
        ->toBe(1)
        ->and($createBonfire->concentration)
        ->toBeTrue()
        ->and($thunderclap->targetProfile->area_shape)
        ->toBe('emanation')
        ->and($thunderclap->targetProfile->area_size_meters)
        ->toBe(1.524)
        ->and($magicStone->casting_time_type)
        ->toBe('bonus_action')
        ->and($magicStone->targetProfile->target_type)
        ->toBe('objects')
        ->and($magicStone->targetProfile->target_count)
        ->toBe(3)
        ->and($ritualCount)
        ->toBe(0);
});

//Verifica la normalizzazione dei componenti materiali
it('normalizza tutti i componenti materiali', function () {
    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 0)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query
                ->where('version_key', 'xgte_2017')
                ->where('level', 0);
        })
        ->count();

    $missingDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 0)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 0)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    $infestationMaterial = Spell::query()
        ->where('key', 'infestation')
        ->firstOrFail()
        ->materialComponents()
        ->firstOrFail();

    $radianceMaterial = Spell::query()
        ->where('key', 'word_of_radiance')
        ->firstOrFail()
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(2)
        ->and($materialDetailCount)
        ->toBe(2)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and($infestationMaterial->key)
        ->toBe('material_requirement')
        ->and($infestationMaterial->description)
        ->toBe('Una pulce viva.')
        ->and($infestationMaterial->consumed)
        ->toBeFalse()
        ->and($radianceMaterial->description)
        ->toBe('Un simbolo sacro.')
        ->and($radianceMaterial->focus_replaceable)
        ->toBeTrue();
});

//Verifica tiri salvezza, attacchi e bersagli rappresentativi
it('salva tiri salvezza attacchi e bersagli', function () {
    $frostbite = Spell::query()
        ->where('key', 'frostbite')
        ->firstOrFail();

    $tollTheDead = Spell::query()
        ->where('key', 'toll_the_dead')
        ->firstOrFail();

    $gust = Spell::query()
        ->where('key', 'gust')
        ->firstOrFail();

    $primalSavagery = Spell::query()
        ->where('key', 'primal_savagery')
        ->firstOrFail();

    $createBonfire = Spell::query()
        ->where('key', 'create_bonfire')
        ->firstOrFail();

    expect($frostbite->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($tollTheDead->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($gust->savingThrowAbility->short_name)
        ->toBe('FOR')
        ->and($gust->targetProfile->can_target_objects)
        ->toBeTrue()
        ->and($primalSavagery->attack_type)
        ->toBe('melee')
        ->and($primalSavagery->targetProfile->target_count)
        ->toBe(1)
        ->and($createBonfire->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($createBonfire->save_success_damage)
        ->toBe('none');
});

//Verifica descrizioni, bersagli e riferimenti al manuale
it('collega bersagli descrizioni e pagine di Xanathar', function () {
    $spellIds = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 0)
        ->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $wordOfRadiance = Spell::query()
        ->where('key', 'word_of_radiance')
        ->firstOrFail();

    $reference = $wordOfRadiance
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 0)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 0)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(164)
        ->and($reference->sourceBook->slug)
        ->toBe('xgte-2017')
        ->and($reference->key)
        ->toBe('xgte_2017_it_spell_definition')
        ->and($reference->official_text)
        ->toBeNull();
});
