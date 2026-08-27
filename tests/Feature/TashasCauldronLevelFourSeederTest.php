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

it('salva i tre incantesimi di quarto livello di Tasha', function () {
    $levelFourSpells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 4);

    expect((clone $levelFourSpells)->count())
        ->toBe(3)
        ->and(
            (clone $levelFourSpells)
                ->distinct('canonical_key')
                ->count()
        )->toBe(3)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->where('level', '<=', 4)
                ->count()
        )->toBe(16)
        ->and(
            (clone $levelFourSpells)
                ->whereHas('summons')
                ->count()
        )->toBe(3)
        ->and(
            SourceReference::query()
                ->where('sourceable_type', Spell::class)
                ->whereIn(
                    'sourceable_id',
                    (clone $levelFourSpells)->pluck('id')
                )
                ->count()
        )->toBe(3);
});

it('salva le forme di aberrazione costrutto ed elementale', function () {
    $aberration = Spell::query()
        ->where('key', 'summon_aberration')
        ->firstOrFail();
    $construct = Spell::query()
        ->where('key', 'summon_construct')
        ->firstOrFail();
    $elemental = Spell::query()
        ->where('key', 'summon_elemental')
        ->firstOrFail();

    $aberrationTemplate = $aberration
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();
    $constructTemplate = $construct
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();
    $elementalTemplate = $elemental
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();

    expect($aberrationTemplate->forms()->count())
        ->toBe(3)
        ->and($constructTemplate->forms()->count())
        ->toBe(3)
        ->and($elementalTemplate->forms()->count())
        ->toBe(4)
        ->and($aberrationTemplate->creatureType->key)
        ->toBe('aberration')
        ->and($constructTemplate->creatureType->key)
        ->toBe('construct')
        ->and($elementalTemplate->creatureType->key)
        ->toBe('elemental');

    $slaadForm = $aberrationTemplate
        ->forms()
        ->where('name', 'Slaad')
        ->firstOrFail();
    $slaadDamage = $slaadForm
        ->creatureStatBlock
        ->actions()
        ->where('key', 'claws')
        ->firstOrFail()
        ->damages()
        ->firstOrFail();

    $starAura = $aberrationTemplate
        ->forms()
        ->where('name', 'Progenie Stellare')
        ->firstOrFail()
        ->creatureStatBlock
        ->actions()
        ->where('key', 'whispering_aura')
        ->firstOrFail();

    expect($slaadDamage->formula)
        ->toBe('1d10 + 3')
        ->and($slaadDamage->damageType->name)
        ->toBe('Tagliente')
        ->and($slaadForm->scalings()->count())
        ->toBe(5)
        ->and($starAura->damages()->firstOrFail()->formula)
        ->toBe('2d6')
        ->and(
            $starAura
                ->savingThrows()
                ->firstOrFail()
                ->ability
                ->short_name
        )->toBe('SAG');
});

it('salva capacità movimenti e danni delle forme', function () {
    $constructTemplate = Spell::query()
        ->where('key', 'summon_construct')
        ->firstOrFail()
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();

    $heatedBody = $constructTemplate
        ->forms()
        ->where('name', 'Metallo')
        ->firstOrFail()
        ->creatureStatBlock
        ->actions()
        ->where('key', 'heated_body')
        ->firstOrFail()
        ->damages()
        ->firstOrFail();

    $elementalTemplate = Spell::query()
        ->where('key', 'summon_elemental')
        ->firstOrFail()
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();
    $air = $elementalTemplate
        ->forms()
        ->where('name', 'Aria')
        ->firstOrFail()
        ->creatureStatBlock;
    $fire = $elementalTemplate
        ->forms()
        ->where('name', 'Fuoco')
        ->firstOrFail()
        ->creatureStatBlock;

    $airFlight = $air
        ->movements()
        ->whereHas(
            'movementType',
            fn ($query) => $query->where('name', 'Volare')
        )
        ->firstOrFail();
    $fireDamage = $fire
        ->actions()
        ->where('key', 'slam')
        ->firstOrFail()
        ->damages()
        ->firstOrFail();

    expect($heatedBody->formula)
        ->toBe('1d10')
        ->and($heatedBody->damageType->name)
        ->toBe('Fuoco')
        ->and($airFlight->speed)
        ->toBe(12.192)
        ->and($airFlight->can_hover)
        ->toBeTrue()
        ->and($fireDamage->formula)
        ->toBe('1d10 + 4')
        ->and($fireDamage->damageType->name)
        ->toBe('Fuoco');
});

it('collega materiali e pagine del manuale', function () {
    $spells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 4)
        ->get();

    $aberration = $spells
        ->firstWhere('key', 'summon_aberration');
    $construct = $spells
        ->firstWhere('key', 'summon_construct');
    $elemental = $spells
        ->firstWhere('key', 'summon_elemental');

    expect($spells->count())
        ->toBe(3)
        ->and(
            $spells->every(
                fn (Spell $spell) =>
                    (float) $spell
                        ->materialComponents()
                        ->firstOrFail()
                        ->cost_amount === 400.0
            )
        )->toBeTrue()
        ->and($aberration->sourceReferences()->firstOrFail()->page_start)
        ->toBe(106)
        ->and($construct->sourceReferences()->firstOrFail()->page_start)
        ->toBe(109)
        ->and($elemental->sourceReferences()->firstOrFail()->page_start)
        ->toBe(109)
        ->and(
            $elemental
                ->sourceReferences()
                ->firstOrFail()
                ->sourceBook
                ->slug
        )->toBe('tcoe-2020');
});
