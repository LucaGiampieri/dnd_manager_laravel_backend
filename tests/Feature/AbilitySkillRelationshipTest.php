<?php

use App\Models\Ability;

test('una caratteristica può avere molte abilità', function () {
    //Crea una caratteristica utilizzata soltanto dal test
    $dexterity = Ability::create([
        'name' => 'Destrezza',
        'short_name' => 'DES',
        'description' => 'Misura agilità, riflessi ed equilibrio.',
    ]);

    //Relazione uno-a-molti (HasMany):
    //crea la prima abilità associandola alla Destrezza
    $acrobatics = $dexterity->skills()->create([
        'name' => 'Acrobazia',
        'description' => 'Rappresenta equilibrio e movimenti acrobatici.',
    ]);

    //Relazione uno-a-molti (HasMany):
    //crea una seconda abilità associata alla stessa caratteristica
    $dexterity->skills()->create([
        'name' => 'Furtività',
        'description' => 'Rappresenta la capacità di muoversi senza essere notati.',
    ]);

    //Verifica che la Destrezza possieda esattamente due abilità
    expect($dexterity->skills)->toHaveCount(2);

    //Verifica che entrambe le abilità siano presenti nella relazione
    expect($dexterity->skills->pluck('name')->all())
        ->toContain('Acrobazia')
        ->toContain('Furtività');

    //Relazione molti-a-uno (BelongsTo):
    //verifica che Acrobazia appartenga alla Destrezza
    expect($acrobatics->ability->is($dexterity))->toBeTrue();
});
