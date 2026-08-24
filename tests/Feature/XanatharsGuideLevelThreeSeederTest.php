<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima del test
uses(RefreshDatabase::class);

//Verifica in un solo passaggio tutto il catalogo di 3° livello
it('salva tutti gli incantesimi di terzo livello di Xanathar', function () {
    //La doppia esecuzione controlla anche l'idempotenza
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $levelThreeSpells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 3);

    $eruptingEarth = Spell::query()
        ->where('key', 'erupting_earth')
        ->firstOrFail();

    $summonLesserDemons = Spell::query()
        ->where('key', 'summon_lesser_demons')
        ->firstOrFail();

    $flameArrows = Spell::query()
        ->where('key', 'flame_arrows')
        ->firstOrFail();

    $melfsMeteors = Spell::query()
        ->where('key', 'melfs_minute_meteors')
        ->firstOrFail();

    $wallOfWater = Spell::query()
        ->where('key', 'wall_of_water')
        ->firstOrFail();

    $wallOfSand = Spell::query()
        ->where('key', 'wall_of_sand')
        ->firstOrFail();

    $enemiesAbound = Spell::query()
        ->where('key', 'enemies_abound')
        ->firstOrFail();

    $tidalWave = Spell::query()
        ->where('key', 'tidal_wave')
        ->firstOrFail();

    $thunderStep = Spell::query()
        ->where('key', 'thunder_step')
        ->firstOrFail();

    $tinyServant = Spell::query()
        ->where('key', 'tiny_servant')
        ->firstOrFail();

    $catnap = Spell::query()
        ->where('key', 'catnap')
        ->firstOrFail();

    $lifeTransference = Spell::query()
        ->where('key', 'life_transference')
        ->firstOrFail();

    //Controlla conteggi, identità e concentrazione
    expect($levelThreeSpells->count())
        ->toBe(12)
        ->and($levelThreeSpells->distinct('canonical_key')->count())
        ->toBe(12)
        ->and($eruptingEarth->version_key)
        ->toBe('xgte_2017')
        ->and($eruptingEarth->spellSchool->key)
        ->toBe('transmutation')
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 3)
                ->where('concentration', true)
                ->count()
        )->toBe(6)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 3)
                ->where('ritual', true)
                ->count()
        )->toBe(0);

    //Controlla tutti i componenti materiali normalizzati
    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 3)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query
                ->where('version_key', 'xgte_2017')
                ->where('level', 3);
        })
        ->count();

    $missingDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 3)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 3)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    $demonMaterial = $summonLesserDemons
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(7)
        ->and($materialDetailCount)
        ->toBe(7)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and($demonMaterial->description)
        ->toContain('cerchio protettivo')
        ->and($demonMaterial->consumed)
        ->toBeFalse();

    //Controlla tiri salvezza, bersagli e aree
    expect($eruptingEarth->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($eruptingEarth->save_success_damage)
        ->toBe('half')
        ->and($eruptingEarth->targetProfile->area_shape)
        ->toBe('cube')
        ->and($eruptingEarth->targetProfile->area_size_meters)
        ->toBe(6.096)
        ->and($flameArrows->targetProfile->target_type)
        ->toBe('object')
        ->and($melfsMeteors->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($wallOfWater->targetProfile->area_shape)
        ->toBe('wall')
        ->and($wallOfWater->targetProfile->area_size_meters)
        ->toBe(9.144)
        ->and($wallOfSand->targetProfile->area_shape)
        ->toBe('wall')
        ->and($enemiesAbound->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($tidalWave->targetProfile->area_shape)
        ->toBe('rectangle')
        ->and($thunderStep->savingThrowAbility->short_name)
        ->toBe('COS')
        ->and($thunderStep->targetProfile->area_shape)
        ->toBe('emanation');

    //Controlla tempi, durate e bersagli rappresentativi
    expect($summonLesserDemons->duration_type)
        ->toBe('hour')
        ->and($summonLesserDemons->concentration)
        ->toBeTrue()
        ->and($tinyServant->casting_time_type)
        ->toBe('minute')
        ->and($tinyServant->duration_value)
        ->toBe(8)
        ->and($tinyServant->targetProfile->can_target_objects)
        ->toBeTrue()
        ->and($catnap->targetProfile->target_type)
        ->toBe('creatures')
        ->and($catnap->targetProfile->target_count)
        ->toBe(3)
        ->and($lifeTransference->targetProfile->requires_sight)
        ->toBeTrue();

    //Controlla descrizioni, profili e riferimenti alle pagine
    $spellIds = $levelThreeSpells->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $reference = $lifeTransference
        ->sourceReferences()
        ->firstOrFail();

    expect($referenceCount)
        ->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 3)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(12)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 3)
                ->where(function ($query) {
                    $query
                        ->whereNull('description')
                        ->orWhere('description', '');
                })
                ->count()
        )->toBe(0)
        ->and($reference->page_start)
        ->toBe(170)
        ->and($reference->sourceBook->slug)
        ->toBe('xgte-2017')
        ->and($reference->official_text)
        ->toBeNull();
});
