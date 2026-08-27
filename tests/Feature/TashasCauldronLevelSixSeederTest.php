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

it('salva i due incantesimi di sesto livello di Tasha', function () {
    $levelSixSpells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 6);

    expect((clone $levelSixSpells)->count())
        ->toBe(2)
        ->and(
            (clone $levelSixSpells)
                ->distinct('canonical_key')
                ->count()
        )->toBe(2)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->where('level', '<=', 6)
                ->count()
        )->toBe(19)
        ->and(
            SourceReference::query()
                ->where('sourceable_type', Spell::class)
                ->whereIn(
                    'sourceable_id',
                    (clone $levelSixSpells)->pluck('id')
                )
                ->count()
        )->toBe(2);
});

it('salva i benefici di Abito Ultraterreno di Tasha', function () {
    $guise = Spell::query()
        ->where('key', 'tashas_otherworldly_guise')
        ->firstOrFail();
    $material = $guise
        ->materialComponents()
        ->firstOrFail();

    expect($guise->name)
        ->toBe('Abito Ultraterreno di Tasha')
        ->and($guise->spellSchool->key)
        ->toBe('transmutation')
        ->and($guise->casting_time_type)
        ->toBe('bonus_action')
        ->and($guise->range_type)
        ->toBe('self')
        ->and($guise->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($guise->concentration)
        ->toBeTrue()
        ->and($guise->effectDefinitions()->count())
        ->toBe(3)
        ->and(
            $guise
                ->effectDefinitions()
                ->orderBy('sort_order')
                ->pluck('key')
                ->all()
        )->toBe([
            'planar_defense',
            'spectral_wings',
            'empowered_combat',
        ])
        ->and((float) $material->cost_amount)
        ->toBe(500.0)
        ->and($guise->sourceReferences()->firstOrFail()->page_start)
        ->toBe(106);
});

it('salva le tre forme dello Spirito Immondo', function () {
    $fiend = Spell::query()
        ->where('key', 'summon_fiend')
        ->firstOrFail();
    $template = $fiend
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();

    $demonForm = $template
        ->forms()
        ->where('name', 'Demone')
        ->firstOrFail();
    $devilForm = $template
        ->forms()
        ->where('name', 'Diavolo')
        ->firstOrFail();
    $yugolothForm = $template
        ->forms()
        ->where('name', 'Yugoloth')
        ->firstOrFail();

    expect($template->creatureType->key)
        ->toBe('fiend')
        ->and($template->size->name)
        ->toBe('Grande')
        ->and($template->forms()->count())
        ->toBe(3)
        ->and($demonForm->creatureStatBlock->average_hit_points)
        ->toBe(50)
        ->and($devilForm->creatureStatBlock->average_hit_points)
        ->toBe(40)
        ->and($yugolothForm->creatureStatBlock->average_hit_points)
        ->toBe(60);

    $bite = $demonForm
        ->creatureStatBlock
        ->actions()
        ->where('key', 'bite')
        ->firstOrFail()
        ->damages()
        ->firstOrFail();
    $deathThroes = $demonForm
        ->creatureStatBlock
        ->actions()
        ->where('key', 'death_throes')
        ->firstOrFail();
    $flame = $devilForm
        ->creatureStatBlock
        ->actions()
        ->where('key', 'hurl_flame')
        ->firstOrFail();
    $claws = $yugolothForm
        ->creatureStatBlock
        ->actions()
        ->where('key', 'claws')
        ->firstOrFail();

    expect($bite->formula)
        ->toBe('1d12 + 3')
        ->and($bite->damageType->name)
        ->toBe('Necrotico')
        ->and($deathThroes->damages()->firstOrFail()->formula)
        ->toBe('2d10')
        ->and(
            $deathThroes
                ->savingThrows()
                ->firstOrFail()
                ->ability
                ->short_name
        )->toBe('DES')
        ->and($demonForm->scalings()->count())
        ->toBe(6)
        ->and($flame->damages()->firstOrFail()->formula)
        ->toBe('2d6 + 3')
        ->and($flame->attacks()->firstOrFail()->range)
        ->toBe(45.72)
        ->and($claws->damages()->firstOrFail()->formula)
        ->toBe('1d8 + 3')
        ->and($yugolothForm->scalings()->count())
        ->toBe(5);
});

it('collega materiale e riferimento di Evoca Immondo', function () {
    $fiend = Spell::query()
        ->where('key', 'summon_fiend')
        ->firstOrFail();
    $material = $fiend
        ->materialComponents()
        ->firstOrFail();
    $reference = $fiend
        ->sourceReferences()
        ->firstOrFail();

    expect((float) $material->cost_amount)
        ->toBe(600.0)
        ->and($material->currency->code)
        ->toBe('mo')
        ->and($material->cost_is_minimum)
        ->toBeTrue()
        ->and($material->consumed)
        ->toBeFalse()
        ->and($reference->page_start)
        ->toBe(110)
        ->and($reference->sourceBook->slug)
        ->toBe('tcoe-2020');
});
