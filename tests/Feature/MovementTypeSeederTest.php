<?php

use App\Models\MovementType;
use Database\Seeders\MovementTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea i cinque tipi di movimento senza duplicati', function () {
    $this->seed(MovementTypeSeeder::class);
    $this->seed(MovementTypeSeeder::class);

    expect(MovementType::count())->toBe(5)
        ->and(
            MovementType::query()
                ->orderBy('name')
                ->pluck('name')
                ->all()
        )->toBe([
    'Nuotare',
    'Scalare',
    'Scavare',
    'Terrestre',
    'Volare',
])
        ->and(
            MovementType::query()
                ->whereNull('description')
                ->count()
        )->toBe(0);
});
