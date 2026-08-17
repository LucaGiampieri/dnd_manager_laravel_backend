<?php

use App\Models\Condition;
use App\Models\ConditionLevel;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\RulesetSeeder;

test('il seeder crea le condizioni e i livelli dello sfinimento senza duplicati', function () {
    $this->seed(RulesetSeeder::class);
    $this->seed(ConditionSeeder::class);

    $this->seed(RulesetSeeder::class);
    $this->seed(ConditionSeeder::class);

    expect(Condition::count())->toBe(15);
    expect(ConditionLevel::count())->toBe(6);

    $exhaustion = Condition::where('key', 'exhaustion')
        ->firstOrFail();

    expect($exhaustion->ruleset->key)->toBe('dnd5e_2014');
    expect($exhaustion->is_level_based)->toBeTrue();
    expect($exhaustion->maximum_level)->toBe(6);

    expect($exhaustion->levels->pluck('level')->all())
        ->toBe([1, 2, 3, 4, 5, 6]);

    expect(
        $exhaustion->levels
            ->where('is_terminal', true)
            ->pluck('level')
            ->all()
    )->toBe([6]);
});
