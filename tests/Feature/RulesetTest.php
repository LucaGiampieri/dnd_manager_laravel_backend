<?php

use App\Models\Ruleset;
use Illuminate\Database\QueryException;

test('può creare un regolamento', function () {
    //Crea un regolamento tramite il modello Eloquent
    $ruleset = Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
        'revision' => '2014',
        'description' => 'Regolamento D&D quinta edizione del 2014.',
        'is_active' => true,
    ]);

    //Verifica che il modello sia stato salvato nel database
    expect($ruleset->exists)->toBeTrue();

    //Verifica che Laravel converta is_active in un booleano
    expect($ruleset->is_active)->toBeTrue();

    //Verifica direttamente che il record e i suoi valori
    //siano presenti nella tabella rulesets
    $this->assertDatabaseHas('rulesets', [
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
        'revision' => '2014',
        'is_active' => 1,
    ]);
});

test('non permette due regolamenti con la stessa chiave', function () {
    //Crea il primo regolamento con una chiave tecnica univoca
    Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
    ]);

    //Prova a creare un secondo regolamento con la stessa chiave
    //e verifica che il vincolo UNIQUE del database lo impedisca
    expect(
        fn () => Ruleset::create([
            'key' => 'dnd5e_2014',
            'name' => 'Altro regolamento',
            'edition' => '5e',
        ])
    )->toThrow(QueryException::class);
});
