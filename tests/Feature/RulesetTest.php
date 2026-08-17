<?php

use App\Models\Ruleset;
use Illuminate\Database\QueryException;

test('può creare un regolamento', function () {
    $ruleset = Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
        'revision' => '2014',
        'description' => 'Regolamento D&D quinta edizione del 2014.',
        'is_active' => true,
    ]);

    expect($ruleset->exists)->toBeTrue();
    expect($ruleset->is_active)->toBeTrue();

    $this->assertDatabaseHas('rulesets', [
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
        'revision' => '2014',
        'is_active' => 1,
    ]);
});

test('non permette due regolamenti con la stessa chiave', function () {
    Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
    ]);

    expect(fn () => Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'Altro regolamento',
        'edition' => '5e',
    ]))->toThrow(QueryException::class);
});
