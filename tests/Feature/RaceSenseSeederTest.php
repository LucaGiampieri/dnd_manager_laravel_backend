<?php

use App\Models\RaceSense;
use App\Models\SubraceSense;
use Database\Seeders\RaceSenseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione idempotente dei sensi
it('crea i sensi razziali senza duplicati', function () {
    $this->seed(RaceSenseSeeder::class);
    $this->seed(RaceSenseSeeder::class);

    expect(RaceSense::query()->count())->toBe(6)
        ->and(SubraceSense::query()->count())->toBe(4);
});

//Verifica le razze dotate di Scurovisione
it('assegna la Scurovisione alle razze corrette', function () {
    $this->seed(RaceSenseSeeder::class);

    $assignments = RaceSense::query()
        ->with([
            'race',
            'sense',
        ])
        ->get();

    expect(
        $assignments
            ->pluck('race.key')
            ->sort()
            ->values()
            ->all()
    )->toBe([
        'dwarf',
        'elf',
        'gnome',
        'half_elf',
        'half_orc',
        'tiefling',
    ])
        ->and($assignments->pluck('sense.key')->unique()->all())
        ->toBe(['darkvision'])
        ->and($assignments->every(
            fn (RaceSense $assignment) =>
                $assignment->range_meters === '18.000'
        ))->toBeTrue();
});

//Verifica le portate specifiche delle sottorazze
it('assegna le portate corrette alle sottorazze', function () {
    $this->seed(RaceSenseSeeder::class);

    $ranges = SubraceSense::query()
        ->with('subrace')
        ->get()
        ->mapWithKeys(
            fn (SubraceSense $assignment) => [
                $assignment->subrace->key =>
                    $assignment->range_meters,
            ]
        )
        ->all();

    expect($ranges)->toBe([
        'drow' => '36.000',
        'fire_genasi_eepc_2015' => '18.000',
        'deep_gnome_eepc_2015' => '36.000',
        'duergar_scag_2015' => '36.000',
    ]);
});

//Verifica le proprietà aggiuntive dei sensi
it('registra correttamente condizioni e cecità oltre portata', function () {
    $this->seed(RaceSenseSeeder::class);

    $assignments = RaceSense::query()
        ->get()
        ->concat(SubraceSense::query()->get());

    expect($assignments)->toHaveCount(10)
        ->and($assignments->every(
            fn ($assignment) =>
                $assignment->is_blind_beyond_range === false
        ))->toBeTrue()
        ->and($assignments->every(
            fn ($assignment) =>
                $assignment->condition !== null
        ))->toBeTrue();
});
