<?php

use App\Models\EffectDefinition;
use App\Models\EffectDefinitionDamage;
use App\Models\EffectDefinitionDuration;
use App\Models\EffectDefinitionScaling;
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

it('salva l incantesimo di primo livello di Tasha', function () {
    $levelOneSpells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 1);

    $causticBrew = Spell::query()
        ->where('key', 'tashas_caustic_brew')
        ->firstOrFail();

    expect($levelOneSpells->count())
        ->toBe(1)
        ->and($levelOneSpells->distinct('canonical_key')->count())
        ->toBe(1)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->where('level', '<=', 1)
                ->count()
        )->toBe(6)
        ->and($causticBrew->name)
        ->toBe('Miscela Caustica di Tasha')
        ->and($causticBrew->spellSchool->key)
        ->toBe('evocation')
        ->and($causticBrew->concentration)
        ->toBeTrue()
        ->and($causticBrew->duration_type)
        ->toBe('minute')
        ->and($causticBrew->duration_value)
        ->toBe(1)
        ->and($causticBrew->savingThrowAbility->short_name)
        ->toBe('DES')
        ->and($causticBrew->save_success_damage)
        ->toBe('none');
});

it('salva area materiali e riferimento di Miscela Caustica', function () {
    $causticBrew = Spell::query()
        ->where('key', 'tashas_caustic_brew')
        ->firstOrFail();

    $material = $causticBrew
        ->materialComponents()
        ->firstOrFail();

    $reference = $causticBrew
        ->sourceReferences()
        ->firstOrFail();

    expect($causticBrew->targetProfile->area_shape)
        ->toBe('line')
        ->and($causticBrew->targetProfile->area_size_meters)
        ->toBe(9.144)
        ->and(
            $causticBrew
                ->targetProfile
                ->area_secondary_size_meters
        )->toBe(1.524)
        ->and($material->description)
        ->toBe('Un frammento di cibo avariato.')
        ->and($material->consumed)
        ->toBeFalse()
        ->and($material->focus_replaceable)
        ->toBeTrue()
        ->and($reference->page_start)
        ->toBe(114)
        ->and($reference->sourceBook->slug)
        ->toBe('tcoe-2020')
        ->and($reference->official_text)
        ->toBeNull();
});

it('salva danno ricorrente durata e progressione', function () {
    $causticBrew = Spell::query()
        ->where('key', 'tashas_caustic_brew')
        ->firstOrFail();

    $effect = $causticBrew
        ->effectDefinitions()
        ->where('key', 'acid_coating')
        ->firstOrFail();

    $damage = $effect
        ->damages()
        ->where('key', 'recurring_acid_damage')
        ->firstOrFail();

    $scaling = $damage
        ->scalings()
        ->where('key', 'extra_dice_per_slot_level')
        ->firstOrFail();

    expect($effect->application_type)
        ->toBe('on_start_turn')
        ->and($effect->ends_with_source)
        ->toBeTrue()
        ->and($damage->damageType->name)
        ->toBe('Acido')
        ->and($damage->formula)
        ->toBe('2d4')
        ->and($scaling->target_field)
        ->toBe('dice_count')
        ->and($scaling->source_type)
        ->toBe('spell_slot_level')
        ->and($scaling->operation)
        ->toBe('add')
        ->and($scaling->minimum_source)
        ->toBe(2.0)
        ->and($scaling->source_offset)
        ->toBe(-1.0)
        ->and($scaling->multiplier)
        ->toBe(2.0)
        ->and($effect->durations()->count())
        ->toBe(2)
        ->and(
            $effect->durations()
                ->where('duration_type', 'until_condition')
                ->count()
        )->toBe(1);

    expect(
        EffectDefinition::query()
            ->where('source_type', Spell::class)
            ->where('source_id', $causticBrew->id)
            ->count()
    )->toBe(1)
        ->and(
            EffectDefinitionDamage::query()
                ->where('effect_definition_id', $effect->id)
                ->count()
        )->toBe(1)
        ->and(
            EffectDefinitionScaling::query()
                ->where(
                    'scalable_type',
                    EffectDefinitionDamage::class
                )
                ->where('scalable_id', $damage->id)
                ->count()
        )->toBe(1)
        ->and(
            EffectDefinitionDuration::query()
                ->where('effect_definition_id', $effect->id)
                ->count()
        )->toBe(2)
        ->and(
            SourceReference::query()
                ->where('sourceable_type', Spell::class)
                ->where('sourceable_id', $causticBrew->id)
                ->where('reference_type', 'definition')
                ->count()
        )->toBe(1);
});
