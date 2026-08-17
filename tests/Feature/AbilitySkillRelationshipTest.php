<?php

use App\Models\Ability;

test('una caratteristica può avere molte abilità', function () {
    $dexterity = Ability::create([
        'name' => 'Destrezza',
        'short_name' => 'DES',
        'description' => 'Misura agilità, riflessi ed equilibrio.',
    ]);

    $acrobatics = $dexterity->skills()->create([
        'name' => 'Acrobazia',
        'description' => 'Rappresenta equilibrio e movimenti acrobatici.',
    ]);

    $dexterity->skills()->create([
        'name' => 'Furtività',
        'description' => 'Rappresenta la capacità di muoversi senza essere notati.',
    ]);

    expect($dexterity->skills)->toHaveCount(2);

    expect($dexterity->skills->pluck('name')->all())
        ->toContain('Acrobazia')
        ->toContain('Furtività');

    expect($acrobatics->ability->is($dexterity))->toBeTrue();
});
