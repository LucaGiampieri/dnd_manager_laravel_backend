<?php

use App\Models\SpellSchool;
use Database\Seeders\SpellSchoolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea le otto scuole di magia senza duplicati', function () {
    $this->seed(SpellSchoolSeeder::class);
    $this->seed(SpellSchoolSeeder::class);

    expect(SpellSchool::count())->toBe(8)
        ->and(
            SpellSchool::query()
                ->orderBy('name')
                ->pluck('name')
                ->all()
        )->toBe([
            'Abiurazione',
            'Ammaliamento',
            'Divinazione',
            'Evocazione',
            'Illusione',
            'Invocazione',
            'Necromanzia',
            'Trasmutazione',
        ])
        ->and(
            SpellSchool::query()
                ->whereNull('description')
                ->count()
        )->toBe(0);
});
