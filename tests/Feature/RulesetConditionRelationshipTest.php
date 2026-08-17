<?php

use App\Models\Ruleset;

test('un regolamento può avere molte condizioni', function () {
    $ruleset = Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
        'revision' => '2014',
        'is_active' => true,
    ]);

    $ruleset->conditions()->create([
        'key' => 'blinded',
        'name' => 'Accecato',
        'description' => 'La creatura non è in grado di vedere.',
        'is_level_based' => false,
    ]);

    $exhaustion = $ruleset->conditions()->create([
        'key' => 'exhaustion',
        'name' => 'Sfinimento',
        'description' => 'Condizione progressiva suddivisa in livelli.',
        'is_level_based' => true,
        'maximum_level' => 6,
    ]);

    expect($ruleset->conditions)->toHaveCount(2);

    expect($ruleset->conditions->pluck('name')->all())
        ->toContain('Accecato')
        ->toContain('Sfinimento');

    expect($exhaustion->ruleset->is($ruleset))->toBeTrue();
    expect($exhaustion->is_level_based)->toBeTrue();
    expect($exhaustion->maximum_level)->toBe(6);
});
