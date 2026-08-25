<?php

use App\Models\EffectDefinition;
use App\Models\EffectDefinitionDamage;
use App\Models\EffectDefinitionDuration;
use App\Models\EffectDefinitionForcedMovement;
use App\Models\EffectDefinitionRollModifier;
use App\Models\EffectDefinitionScaling;
use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use Database\Seeders\TashasCauldronSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Inserisce due volte il catalogo per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(TashasCauldronSpellSeeder::class);
    $this->seed(TashasCauldronSpellSeeder::class);
});

it('salva tutti i trucchetti del Calderone di Tasha', function () {
    $spells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 0);

    $boomingBlade = Spell::query()
        ->where('key', 'booming_blade')
        ->firstOrFail();

    $mindSliver = Spell::query()
        ->where('key', 'mind_sliver')
        ->firstOrFail();

    $swordBurst = Spell::query()
        ->where('key', 'sword_burst')
        ->firstOrFail();

    expect($spells->count())
        ->toBe(5)
        ->and($spells->distinct('canonical_key')->count())
        ->toBe(5)
        ->and($boomingBlade->name)
        ->toBe('Lama Roboante')
        ->and($boomingBlade->attack_type)
        ->toBe('melee')
        ->and((float) $boomingBlade->material_cost)
        ->toBe(0.1)
        ->and($mindSliver->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($swordBurst->spellSchool->key)
        ->toBe('conjuration')
        ->and($swordBurst->targetProfile->area_shape)
        ->toBe('emanation')
        ->and($swordBurst->targetProfile->area_size_meters)
        ->toBe(1.524)
        ->and(SpellMaterialComponent::query()->count())
        ->toBe(2);

    $weapon = $boomingBlade->materialComponents()->firstOrFail();

    expect((float) $weapon->cost_amount)
        ->toBe(1.0)
        ->and($weapon->currency->code)
        ->toBe('ma')
        ->and($weapon->focus_replaceable)
        ->toBeFalse()
        ->and($boomingBlade->sourceReferences()->firstOrFail()->page_start)
        ->toBe(113)
        ->and($swordBurst->sourceReferences()->firstOrFail()->page_start)
        ->toBe(116)
        ->and($swordBurst->sourceReferences()->firstOrFail()->sourceBook->slug)
        ->toBe('tcoe-2020');

    $spellIds = $spells->pluck('id');

    expect(
        SourceReference::query()
            ->where('sourceable_type', Spell::class)
            ->whereIn('sourceable_id', $spellIds)
            ->where('reference_type', 'definition')
            ->count()
    )->toBe(5);
});

//Verifica danni, progressioni e movimenti dei trucchetti
it('salva gli effetti strutturati dei trucchetti di Tasha', function () {
    $boomingBlade = Spell::query()
        ->where('key', 'booming_blade')
        ->firstOrFail();

    $movementDamage = $boomingBlade
        ->effectDefinitions()
        ->where('key', 'booming_energy')
        ->firstOrFail()
        ->damages()
        ->where('key', 'thunder_on_move')
        ->firstOrFail();

    expect($boomingBlade->effectDefinitions()->count())
        ->toBe(2)
        ->and($movementDamage->damageType->name)
        ->toBe('Tuono')
        ->and($movementDamage->formula)
        ->toBe('1d8')
        ->and(
            $movementDamage->scalings()
                ->pluck('flat_value')
                ->map(fn ($value): float => (float) $value)
                ->all()
        )->toBe([2.0, 3.0, 4.0]);

    $greenFlameBlade = Spell::query()
        ->where('key', 'green_flame_blade')
        ->firstOrFail();

    $secondaryModifier = $greenFlameBlade
        ->effectDefinitions()
        ->where('key', 'green_flame_jump')
        ->firstOrFail()
        ->damages()
        ->where('key', 'secondary_spellcasting_modifier')
        ->firstOrFail();

    expect($secondaryModifier->modifier_source_type)
        ->toBe('caster_ability_modifier')
        ->and($secondaryModifier->formula)
        ->toBe('+modificatore');

    $lightningLure = Spell::query()
        ->where('key', 'lightning_lure')
        ->firstOrFail();

    $forcedMovement = $lightningLure
        ->effectDefinitions()
        ->firstOrFail()
        ->forcedMovements()
        ->firstOrFail();

    expect($forcedMovement->movement_type)
        ->toBe('pull')
        ->and($forcedMovement->direction_type)
        ->toBe('toward_origin')
        ->and($forcedMovement->distance)
        ->toBe(3.048)
        ->and($forcedMovement->up_to_distance)
        ->toBeTrue()
        ->and($forcedMovement->opportunity_attack_rule)
        ->toBe('does_not_provoke');

    $mindSliver = Spell::query()
        ->where('key', 'mind_sliver')
        ->firstOrFail();

    $penaltyEffect = $mindSliver
        ->effectDefinitions()
        ->where('key', 'saving_throw_penalty')
        ->firstOrFail();

    $penalty = $penaltyEffect
        ->rollModifiers()
        ->firstOrFail();

    $duration = $penaltyEffect
        ->durations()
        ->firstOrFail();

    expect($penalty->roll_type)
        ->toBe('saving_throw')
        ->and($penalty->modifier_type)
        ->toBe('penalty')
        ->and($penalty->dice_count)
        ->toBe(1)
        ->and($penalty->die_size)
        ->toBe(4)
        ->and($duration->duration_type)
        ->toBe('until_end_turn')
        ->and($duration->turn_reference)
        ->toBe('source');

    $swordBurst = Spell::query()
        ->where('key', 'sword_burst')
        ->firstOrFail();

    $forceDamage = $swordBurst
        ->effectDefinitions()
        ->firstOrFail()
        ->damages()
        ->firstOrFail();

    expect($forceDamage->damageType->name)
        ->toBe('Forza')
        ->and(
            $forceDamage->damageType
                ->effectDefinitionDamages()
                ->whereKey($forceDamage->id)
                ->exists()
        )->toBeTrue()
        ->and($forceDamage->dice_count)
        ->toBe(1)
        ->and($forceDamage->die_size)
        ->toBe(6)
        ->and($forceDamage->scalings()->count())
        ->toBe(3)
        ->and(EffectDefinition::query()->count())
        ->toBe(8)
        ->and(EffectDefinitionDamage::query()->count())
        ->toBe(8)
        ->and(EffectDefinitionScaling::query()->count())
        ->toBe(18)
        ->and(EffectDefinitionRollModifier::query()->count())
        ->toBe(1)
        ->and(EffectDefinitionForcedMovement::query()->count())
        ->toBe(1)
        ->and(EffectDefinitionDuration::query()->count())
        ->toBe(2);
});

//Verifica le regole di coerenza delle nuove formule
it('rifiuta formule di effetto incomplete', function () {
    $mindSliver = Spell::query()
        ->where('key', 'mind_sliver')
        ->firstOrFail();

    $effect = $mindSliver
        ->effectDefinitions()
        ->where('key', 'psychic_damage')
        ->firstOrFail();

    $damageTypeId = $effect
        ->damages()
        ->firstOrFail()
        ->damage_type_id;

    expect(
        fn () => $effect->damages()->create([
            'key' => 'incomplete_damage',
            'damage_type_id' => $damageTypeId,
            'dice_count' => 1,
        ])
    )->toThrow(InvalidArgumentException::class);

    $damage = $effect->damages()->firstOrFail();

    expect(
        fn () => $damage->scalings()->create([
            'key' => 'invalid_divisor',
            'target_field' => 'dice_count',
            'source_type' => 'character_level',
            'divisor' => 0,
        ])
    )->toThrow(InvalidArgumentException::class);
});

//Verifica che l'eliminazione non lasci progressioni polimorfiche orfane
it('elimina gli effetti insieme all incantesimo', function () {
    $mindSliver = Spell::query()
        ->where('key', 'mind_sliver')
        ->firstOrFail();

    $effectIds = $mindSliver->effectDefinitions()->pluck('id');
    $damageIds = EffectDefinitionDamage::query()
        ->whereIn('effect_definition_id', $effectIds)
        ->pluck('id');

    $mindSliver->delete();

    expect(
        EffectDefinition::query()
            ->whereIn('id', $effectIds)
            ->count()
    )->toBe(0)
        ->and(
            EffectDefinitionDamage::query()
                ->whereIn('effect_definition_id', $effectIds)
                ->count()
        )->toBe(0)
        ->and(
            EffectDefinitionScaling::query()
                ->where('scalable_type', EffectDefinitionDamage::class)
                ->whereIn('scalable_id', $damageIds)
                ->count()
        )->toBe(0);
});
