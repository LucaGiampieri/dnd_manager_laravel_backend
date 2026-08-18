<?php

use App\Models\Ruleset;

test('un regolamento può avere molte condizioni', function () {
    //Crea un regolamento utilizzato soltanto dal test
    $ruleset = Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
        'revision' => '2014',
        'is_active' => true,
    ]);

    //Relazione uno-a-molti (HasMany):
    //crea una condizione normale appartenente al regolamento
    $ruleset->conditions()->create([
        'key' => 'blinded',
        'name' => 'Accecato',
        'description' => 'La creatura non è in grado di vedere.',
        'is_level_based' => false,
    ]);

    //Relazione uno-a-molti (HasMany):
    //crea una condizione progressiva appartenente allo stesso regolamento
    $exhaustion = $ruleset->conditions()->create([
        'key' => 'exhaustion',
        'name' => 'Sfinimento',
        'description' => 'Condizione progressiva suddivisa in livelli.',
        'is_level_based' => true,
        'maximum_level' => 6,
    ]);

    //Verifica che il regolamento possieda esattamente due condizioni
    expect($ruleset->conditions)->toHaveCount(2);

    //Verifica che entrambe le condizioni siano presenti nella relazione
    expect($ruleset->conditions->pluck('name')->all())
        ->toContain('Accecato')
        ->toContain('Sfinimento');

    //Relazione molti-a-uno (BelongsTo):
    //verifica che lo Sfinimento appartenga al regolamento creato
    expect($exhaustion->ruleset->is($ruleset))->toBeTrue();

    //Verifica che lo Sfinimento sia una condizione basata su livelli
    expect($exhaustion->is_level_based)->toBeTrue();

    //Verifica che lo Sfinimento possieda un massimo di sei livelli
    expect($exhaustion->maximum_level)->toBe(6);
});
