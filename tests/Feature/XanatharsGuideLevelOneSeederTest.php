<?php

use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima del test
uses(RefreshDatabase::class);

//Verifica in un solo passaggio tutto il catalogo di 1° livello
it('salva tutti gli incantesimi di primo livello di Xanathar', function () {
    //La doppia esecuzione controlla anche l'idempotenza
    $this->seed(XanatharsGuideSpellSeeder::class);
    $this->seed(XanatharsGuideSpellSeeder::class);

    $levelOneSpells = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 1);

    $absorbElements = Spell::query()
        ->where('key', 'absorb_elements')
        ->firstOrFail();

    $ceremony = Spell::query()
        ->where('key', 'ceremony')
        ->firstOrFail();

    $iceKnife = Spell::query()
        ->where('key', 'ice_knife')
        ->firstOrFail();

    $chaosBolt = Spell::query()
        ->where('key', 'chaos_bolt')
        ->firstOrFail();

    $causeFear = Spell::query()
        ->where('key', 'cause_fear')
        ->firstOrFail();

    $earthTremor = Spell::query()
        ->where('key', 'earth_tremor')
        ->firstOrFail();

    $snare = Spell::query()
        ->where('key', 'snare')
        ->firstOrFail();

    //Controlla conteggi, identità e versionamento
    expect($levelOneSpells->count())
        ->toBe(10)
        ->and($levelOneSpells->distinct('canonical_key')->count())
        ->toBe(10)
        ->and($absorbElements->version_key)
        ->toBe('xgte_2017')
        ->and($absorbElements->is_legacy)
        ->toBeFalse()
        ->and($absorbElements->spellSchool->key)
        ->toBe('abjuration');

    //Controlla tempi di lancio, durata, rituale e concentrazione
    expect($absorbElements->casting_time_type)
        ->toBe('reaction')
        ->and($absorbElements->casting_trigger)
        ->toContain('danni da acido')
        ->and($absorbElements->duration_type)
        ->toBe('round')
        ->and($absorbElements->duration_value)
        ->toBe(1)
        ->and($ceremony->casting_time_type)
        ->toBe('hour')
        ->and($ceremony->ritual)
        ->toBeTrue()
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 1)
                ->where('concentration', true)
                ->count()
        )->toBe(3);

    //Controlla componenti materiali semplici e consumati
    $materialSpellCount = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 1)
        ->where('material_component', true)
        ->count();

    $materialDetailCount = SpellMaterialComponent::query()
        ->whereHas('spell', function ($query) {
            $query
                ->where('version_key', 'xgte_2017')
                ->where('level', 1);
        })
        ->count();

    $missingDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 1)
        ->where('material_component', true)
        ->whereDoesntHave('materialComponents')
        ->count();

    $unexpectedDetails = Spell::query()
        ->where('version_key', 'xgte_2017')
        ->where('level', 1)
        ->where('material_component', false)
        ->whereHas('materialComponents')
        ->count();

    $ceremonyMaterial = $ceremony
        ->materialComponents()
        ->firstOrFail();

    $snareMaterial = $snare
        ->materialComponents()
        ->firstOrFail();

    expect($materialSpellCount)
        ->toBe(4)
        ->and($materialDetailCount)
        ->toBe(4)
        ->and($missingDetails)
        ->toBe(0)
        ->and($unexpectedDetails)
        ->toBe(0)
        ->and((float) $ceremonyMaterial->cost_amount)
        ->toBe(25.0)
        ->and($ceremonyMaterial->consumed)
        ->toBeTrue()
        ->and($ceremonyMaterial->focus_replaceable)
        ->toBeFalse()
        ->and($snareMaterial->consumed)
        ->toBeTrue();

    //Controlla attacchi, tiri salvezza, bersagli e aree
    expect($iceKnife->attack_type)
        ->toBe('ranged')
        ->and($iceKnife->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($iceKnife->targetProfile->area_shape)
        ->toBe('sphere')
        ->and($iceKnife->targetProfile->area_size_meters)
        ->toBe(1.524)
        ->and($chaosBolt->attack_type)
        ->toBe('ranged')
        ->and($causeFear->savingThrowAbility->short_name)
        ->toBe('SAG')
        ->and($causeFear->targetProfile->requires_sight)
        ->toBeTrue()
        ->and($earthTremor->targetProfile->area_shape)
        ->toBe('emanation')
        ->and($earthTremor->targetProfile->area_size_meters)
        ->toBe(3.048)
        ->and($snare->targetProfile->area_shape)
        ->toBe('circle');

    //Controlla descrizioni, profili e riferimenti alle pagine
    $spellIds = $levelOneSpells->pluck('id');

    $referenceCount = SourceReference::query()
        ->where('sourceable_type', Spell::class)
        ->whereIn('sourceable_id', $spellIds)
        ->where('reference_type', 'definition')
        ->count();

    $reference = $snare->sourceReferences()->firstOrFail();

    expect($referenceCount)
        ->toBe(10)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 1)
                ->whereHas('targetProfile')
                ->count()
        )->toBe(10)
        ->and(
            Spell::query()
                ->where('version_key', 'xgte_2017')
                ->where('level', 1)
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
