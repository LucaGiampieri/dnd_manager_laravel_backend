<?php

use App\Models\DamageType;
use Database\Seeders\DamageTypeSeeder;

test('il seeder crea i tredici tipi di danno senza duplicati', function () {
    $this->seed(DamageTypeSeeder::class);
    $this->seed(DamageTypeSeeder::class);

    expect(DamageType::count())->toBe(13);

    expect(
        DamageType::whereNull('description')->count()
    )->toBe(0);

    expect(
        DamageType::orderBy('name')->pluck('name')->all()
    )->toBe([
        'Acido',
        'Contundente',
        'Forza',
        'Freddo',
        'Fulmine',
        'Fuoco',
        'Necrotico',
        'Perforante',
        'Psichico',
        'Radioso',
        'Tagliente',
        'Tuono',
        'Veleno',
    ]);
});
