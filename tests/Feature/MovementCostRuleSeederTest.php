<?php

use App\Models\EffectDefinitionMovementCostModifier;
use App\Models\Ruleset;
use Database\Seeders\MovementCostRuleSeeder;
use Database\Seeders\MovementTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea le regole base dei costi di movimento senza duplicati', function () {
    $this->seed(RulesetSeeder::class);
    $this->seed(MovementTypeSeeder::class);

    $this->seed(MovementCostRuleSeeder::class);
    $this->seed(MovementCostRuleSeeder::class);

    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    $effect = $ruleset->effectDefinitions()
        ->where('key', 'core_movement_cost_rules')
        ->firstOrFail();

    $modifiers = $effect->movementCostModifiers()
        ->orderBy('context_key')
        ->get();

    $crawling = $modifiers->firstWhere(
        'context_key',
        'crawling'
    );

    $difficultTerrain = $modifiers->firstWhere(
        'context_key',
        'difficult_terrain'
    );

    $climbing = $modifiers->firstWhere(
        'context_key',
        'climbing'
    );

    $swimming = $modifiers->firstWhere(
        'context_key',
        'swimming'
    );

    $standing = $modifiers->firstWhere(
        'context_key',
        'standing_from_prone'
    );

    expect(EffectDefinitionMovementCostModifier::count())->toBe(6)
        ->and($modifiers->pluck('context_key')->all())->toBe([
            'climbing',
            'crawling',
            'difficult_terrain',
            'squeezing',
            'standing_from_prone',
            'swimming',
        ])
        ->and($crawling->cost_basis)->toBe('per_distance')
        ->and($crawling->operation)->toBe('add')
        ->and($crawling->value)->toBe('1.000')
        ->and($difficultTerrain->value)->toBe('1.000')
        ->and($climbing->waivedByMovementType->name)->toBe('Scalare')
        ->and($swimming->waivedByMovementType->name)->toBe('Nuotare')
        ->and($standing->cost_basis)->toBe('total_speed_fraction')
        ->and($standing->value)->toBe('0.500')
        ->and(
            1 + (float) $crawling->value
        )->toBe(2.0)
        ->and(
            1
            + (float) $crawling->value
            + (float) $difficultTerrain->value
        )->toBe(3.0);
});
