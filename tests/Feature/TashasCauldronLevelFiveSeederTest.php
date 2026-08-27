<?php

use App\Models\SourceReference;
use App\Models\Spell;
use Database\Seeders\TashasCauldronSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Inserisce due volte il catalogo per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(TashasCauldronSpellSeeder::class);
    $this->seed(TashasCauldronSpellSeeder::class);
});

it('salva l incantesimo di quinto livello di Tasha', function () {
    $levelFiveSpells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 5);

    $celestial = (clone $levelFiveSpells)->firstOrFail();

    expect((clone $levelFiveSpells)->count())
        ->toBe(1)
        ->and(
            (clone $levelFiveSpells)
                ->distinct('canonical_key')
                ->count()
        )->toBe(1)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->where('level', '<=', 5)
                ->count()
        )->toBe(17)
        ->and($celestial->key)
        ->toBe('summon_celestial')
        ->and($celestial->name)
        ->toBe('Evoca Celestiale')
        ->and($celestial->spellSchool->key)
        ->toBe('conjuration')
        ->and($celestial->concentration)
        ->toBeTrue()
        ->and($celestial->targetProfile->target_type)
        ->toBe('point');
});

it('salva le forme Vendicatore e Difensore', function () {
    $celestial = Spell::query()
        ->where('key', 'summon_celestial')
        ->firstOrFail();
    $template = $celestial
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();

    $avengerForm = $template
        ->forms()
        ->where('name', 'Vendicatore')
        ->firstOrFail();
    $defenderForm = $template
        ->forms()
        ->where('name', 'Difensore')
        ->firstOrFail();

    $avenger = $avengerForm->creatureStatBlock;
    $defender = $defenderForm->creatureStatBlock;

    $bow = $avenger
        ->actions()
        ->where('key', 'radiant_bow')
        ->firstOrFail();
    $bowAttack = $bow->attacks()->firstOrFail();
    $mace = $defender
        ->actions()
        ->where('key', 'radiant_mace')
        ->firstOrFail();
    $healingTouch = $defender
        ->actions()
        ->where('key', 'healing_touch')
        ->firstOrFail();

    expect($template->creatureType->key)
        ->toBe('celestial')
        ->and($template->size->name)
        ->toBe('Grande')
        ->and($template->forms()->count())
        ->toBe(2)
        ->and($avenger->armor_class)
        ->toBe(11)
        ->and($defender->armor_class)
        ->toBe(13)
        ->and($bow->damages()->firstOrFail()->formula)
        ->toBe('2d6 + 2')
        ->and($bowAttack->range)
        ->toBe(45.72)
        ->and($bowAttack->long_range)
        ->toBe(182.88)
        ->and($mace->damages()->firstOrFail()->formula)
        ->toBe('1d10 + 3')
        ->and($healingTouch->max_uses)
        ->toBe(1)
        ->and($healingTouch->recharge_type)
        ->toBe('per_day')
        ->and($defenderForm->scalings()->count())
        ->toBe(6);
});

it('collega materiale e riferimento di Evoca Celestiale', function () {
    $celestial = Spell::query()
        ->where('key', 'summon_celestial')
        ->firstOrFail();
    $material = $celestial
        ->materialComponents()
        ->firstOrFail();
    $reference = $celestial
        ->sourceReferences()
        ->firstOrFail();

    expect((float) $material->cost_amount)
        ->toBe(500.0)
        ->and($material->currency->code)
        ->toBe('mo')
        ->and($material->cost_is_minimum)
        ->toBeTrue()
        ->and($material->consumed)
        ->toBeFalse()
        ->and($reference->page_start)
        ->toBe(108)
        ->and($reference->sourceBook->slug)
        ->toBe('tcoe-2020')
        ->and(
            SourceReference::query()
                ->where('sourceable_type', Spell::class)
                ->where('sourceable_id', $celestial->id)
                ->count()
        )->toBe(1);
});
