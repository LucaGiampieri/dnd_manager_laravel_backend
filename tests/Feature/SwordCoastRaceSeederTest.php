<?php

use App\Models\Subrace;
use Database\Seeders\SwordCoastRaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione idempotente delle sottorazze SCAG
it('crea le sottorazze dello SCAG senza duplicati', function () {
    //Esegue due volte il seeder
    $this->seed(SwordCoastRaceSeeder::class);
    $this->seed(SwordCoastRaceSeeder::class);

    //Recupera soltanto le sottorazze dello SCAG
    $subraces = Subrace::query()
        ->where('version_key', 'scag_2015')
        ->orderBy('key')
        ->get();

    //Verifica quantità e chiavi tecniche
    expect($subraces)->toHaveCount(2)
        ->and($subraces->pluck('key')->all())
        ->toBe([
            'duergar_scag_2015',
            'ghostwise_halfling_scag_2015',
        ])
        ->and($subraces->pluck('canonical_key')->all())
        ->toBe([
            'duergar',
            'ghostwise_halfling',
        ]);
});

//Verifica il collegamento con le razze principali
it('collega le sottorazze SCAG alle razze corrette', function () {
    $this->seed(SwordCoastRaceSeeder::class);

    //Recupera il Duergar
    $duergar = Subrace::query()
        ->where('key', 'duergar_scag_2015')
        ->with('race')
        ->firstOrFail();

    //Recupera l'Halfling degli Spiriti
    $ghostwise = Subrace::query()
        ->where('key', 'ghostwise_halfling_scag_2015')
        ->with('race')
        ->firstOrFail();

    //Verifica le razze principali
    expect($duergar->race->key)->toBe('dwarf')
        ->and($ghostwise->race->key)->toBe('halfling')
        ->and($duergar->version_key)->toBe('scag_2015')
        ->and($ghostwise->version_key)->toBe('scag_2015')
        ->and($duergar->is_legacy)->toBeFalse()
        ->and($ghostwise->is_legacy)->toBeFalse();
});

//Verifica l'ereditarietà di taglia e movimento
it('eredita taglia e velocità dalle razze principali', function () {
    $this->seed(SwordCoastRaceSeeder::class);

    //Carica il Duergar e i dati del Nano
    $duergar = Subrace::query()
        ->where('key', 'duergar_scag_2015')
        ->with([
            'sizeAssignment',
            'movements',
            'race.sizeAssignment.size',
            'race.movements.movementType',
        ])
        ->firstOrFail();

    //Carica l'Halfling degli Spiriti e i dati dell'Halfling
    $ghostwise = Subrace::query()
        ->where('key', 'ghostwise_halfling_scag_2015')
        ->with([
            'sizeAssignment',
            'movements',
            'race.sizeAssignment.size',
            'race.movements.movementType',
        ])
        ->firstOrFail();

    //Recupera i movimenti terrestri ereditati
    $dwarfWalking = $duergar->race->movements->first(
        fn ($movement) =>
            $movement->movementType->name === 'Terrestre'
    );

    $halflingWalking = $ghostwise->race->movements->first(
        fn ($movement) =>
            $movement->movementType->name === 'Terrestre'
    );

    //Le sottorazze non devono duplicare i dati della razza
    expect($duergar->sizeAssignment)->toBeNull()
        ->and($duergar->movements)->toHaveCount(0)
        ->and($ghostwise->sizeAssignment)->toBeNull()
        ->and($ghostwise->movements)->toHaveCount(0);

    //Verifica i dati ereditati
    expect($duergar->race->sizeAssignment->size->name)
        ->toBe('Media')
        ->and((float) $dwarfWalking->speed_meters)
        ->toBe(7.5)
        ->and($ghostwise->race->sizeAssignment->size->name)
        ->toBe('Piccola')
        ->and((float) $halflingWalking->speed_meters)
        ->toBe(7.5);
});

//Verifica che la scelta richieda l'approvazione del DM
it('richiede il permesso del DM per le sottorazze SCAG', function () {
    $this->seed(SwordCoastRaceSeeder::class);

    $subraces = Subrace::query()
        ->where('version_key', 'scag_2015')
        ->get();

    expect($subraces->every(
        fn (Subrace $subrace) =>
            $subrace->requires_dm_permission === true
    ))->toBeTrue()
        ->and($subraces->every(
            fn (Subrace $subrace) =>
                $subrace->selectable === true
        ))->toBeTrue();
});
