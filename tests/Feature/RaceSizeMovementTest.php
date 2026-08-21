<?php

use App\Models\CreatureType;
use App\Models\MovementType;
use App\Models\Race;
use App\Models\RaceMovement;
use App\Models\RaceSize;
use App\Models\Ruleset;
use App\Models\Size;
use App\Models\SubraceMovement;
use App\Models\SubraceSize;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\MovementTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SizeSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara i cataloghi richiesti prima di ogni test
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea regolamento, tipi di creatura, taglie e movimenti
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
        SizeSeeder::class,
        MovementTypeSeeder::class,
    ]);
});

//Crea una razza utilizzabile dai test di questo file
function createRaceForSizeMovementTest(
    string $key,
    string $name
): Race {
    //Recupera il regolamento utilizzato dal test
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera il tipo di creatura Umanoide
    $humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();

    //Crea e restituisce la razza richiesta
    return $ruleset->races()->create([
        'key' => $key,
        'name' => $name,
        'creature_type_id' => $humanoid->id,
        'selectable' => true,
        'sort_order' => 1,
    ]);
}

//Verifica taglia e movimento della razza
it('gestisce la taglia e i movimenti di una razza', function () {
    //Crea una razza di riferimento
    $elf = createRaceForSizeMovementTest(
        'elf',
        'Elfo'
    );

    //Recupera la taglia Media
    $medium = Size::query()
        ->where('name', 'Media')
        ->firstOrFail();

    //Recupera il movimento Terrestre
    $walking = MovementType::query()
        ->where('name', 'Terrestre')
        ->firstOrFail();

    //Assegna la taglia alla razza
    $sizeAssignment = $elf->sizeAssignment()->create([
        'size_id' => $medium->id,
        'notes' => 'Assegnazione utilizzata soltanto nel test.',
    ]);

    //Assegna la velocità terrestre alla razza
    $movement = $elf->movements()->create([
        'movement_type_id' => $walking->id,
        'speed_meters' => '9.000',
        'condition' => null,
    ]);

    //Ricarica le relazioni complete
    $elf->load([
        'sizeAssignment.size',
        'movements.movementType',
    ]);

    //Verifica la taglia assegnata
    expect($elf->sizeAssignment->size->name)
        ->toBe('Media');

    //Verifica il movimento e la velocità
    expect($elf->movements)->toHaveCount(1)
        ->and($elf->movements->first()->movementType->name)
        ->toBe('Terrestre')
        ->and($elf->movements->first()->speed_meters)
        ->toBe('9.000');

    //Verifica le relazioni inverse verso la razza
    expect($sizeAssignment->race->is($elf))->toBeTrue()
        ->and($movement->race->is($elf))->toBeTrue();

    //Verifica le relazioni verso i cataloghi
    expect($sizeAssignment->size->is($medium))->toBeTrue()
        ->and($movement->movementType->is($walking))->toBeTrue();
});

//Verifica le modifiche introdotte da una sottorazza
it('gestisce taglia e movimenti specifici di una sottorazza', function () {
    //Crea la razza principale
    $elf = createRaceForSizeMovementTest(
        'elf',
        'Elfo'
    );

    //Recupera le taglie necessarie
    $medium = Size::query()
        ->where('name', 'Media')
        ->firstOrFail();

    $small = Size::query()
        ->where('name', 'Piccola')
        ->firstOrFail();

    //Recupera i tipi di movimento necessari
    $walking = MovementType::query()
        ->where('name', 'Terrestre')
        ->firstOrFail();

    $flying = MovementType::query()
        ->where('name', 'Volare')
        ->firstOrFail();

    //Assegna i valori base della razza
    $elf->sizeAssignment()->create([
        'size_id' => $medium->id,
    ]);

    $elf->movements()->create([
        'movement_type_id' => $walking->id,
        'speed_meters' => '9.000',
    ]);

    //Crea una sottorazza di prova
    $wingedElf = $elf->subraces()->create([
        'key' => 'winged_elf',
        'name' => 'Elfo Alato di Prova',
        'sort_order' => 1,
    ]);

    //Assegna una taglia specifica alla sottorazza
    $subraceSize = $wingedElf->sizeAssignment()->create([
        'size_id' => $small->id,
        'notes' => 'Sostituzione utilizzata soltanto nel test.',
    ]);

    //Aggiunge un movimento specifico alla sottorazza
    $subraceMovement = $wingedElf->movements()->create([
        'movement_type_id' => $flying->id,
        'speed_meters' => '12.000',
        'condition' => 'Disponibile soltanto quando può usare le ali.',
    ]);

    //Ricarica tutte le relazioni utilizzate
    $wingedElf->load([
        'race.sizeAssignment.size',
        'race.movements.movementType',
        'sizeAssignment.size',
        'movements.movementType',
    ]);

    //Verifica che la razza conservi i propri valori base
    expect($wingedElf->race->sizeAssignment->size->name)
        ->toBe('Media')
        ->and(
            $wingedElf->race
                ->movements
                ->first()
                ->speed_meters
        )->toBe('9.000');

    //Verifica i valori specifici della sottorazza
    expect($wingedElf->sizeAssignment->size->name)
        ->toBe('Piccola')
        ->and($wingedElf->movements->first()->movementType->name)
        ->toBe('Volare')
        ->and($wingedElf->movements->first()->speed_meters)
        ->toBe('12.000');

    //Verifica le relazioni inverse
    expect($subraceSize->subrace->is($wingedElf))->toBeTrue()
        ->and(
            $subraceMovement->subrace->is($wingedElf)
        )->toBeTrue();
});

//Verifica velocità e assegnazioni duplicate
it('rifiuta velocità non valide e assegnazioni duplicate', function () {
    //Crea una razza di riferimento
    $elf = createRaceForSizeMovementTest(
        'elf',
        'Elfo'
    );

    //Recupera taglia e movimento
    $medium = Size::query()
        ->where('name', 'Media')
        ->firstOrFail();

    $small = Size::query()
        ->where('name', 'Piccola')
        ->firstOrFail();

    $walking = MovementType::query()
        ->where('name', 'Terrestre')
        ->firstOrFail();

    //Rifiuta una velocità uguale a zero
    expect(
        fn () => $elf->movements()->create([
            'movement_type_id' => $walking->id,
            'speed_meters' => '0.000',
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Crea una taglia valida
    $elf->sizeAssignment()->create([
        'size_id' => $medium->id,
    ]);

    //Rifiuta una seconda taglia per la stessa razza
    expect(
        fn () => $elf->sizeAssignment()->create([
            'size_id' => $small->id,
        ])
    )->toThrow(QueryException::class);

    //Crea un movimento valido
    $elf->movements()->create([
        'movement_type_id' => $walking->id,
        'speed_meters' => '9.000',
    ]);

    //Rifiuta due volte lo stesso tipo di movimento
    expect(
        fn () => $elf->movements()->create([
            'movement_type_id' => $walking->id,
            'speed_meters' => '12.000',
        ])
    )->toThrow(QueryException::class);
});

//Verifica la cancellazione a cascata
it('elimina taglie e movimenti insieme alla razza', function () {
    //Crea una razza e una sottorazza
    $elf = createRaceForSizeMovementTest(
        'elf',
        'Elfo'
    );

    $highElf = $elf->subraces()->create([
        'key' => 'high_elf',
        'name' => 'Elfo Alto',
        'sort_order' => 1,
    ]);

    //Recupera i cataloghi necessari
    $medium = Size::query()
        ->where('name', 'Media')
        ->firstOrFail();

    $walking = MovementType::query()
        ->where('name', 'Terrestre')
        ->firstOrFail();

    //Crea taglie e movimenti per entrambi
    $elf->sizeAssignment()->create([
        'size_id' => $medium->id,
    ]);

    $elf->movements()->create([
        'movement_type_id' => $walking->id,
        'speed_meters' => '9.000',
    ]);

    $highElf->sizeAssignment()->create([
        'size_id' => $medium->id,
    ]);

    $highElf->movements()->create([
        'movement_type_id' => $walking->id,
        'speed_meters' => '9.000',
    ]);

    //Cancella la razza principale
    $elf->delete();

    //Verifica che tutte le righe collegate siano state eliminate
    expect(RaceSize::query()->count())->toBe(0)
        ->and(RaceMovement::query()->count())->toBe(0)
        ->and(SubraceSize::query()->count())->toBe(0)
        ->and(SubraceMovement::query()->count())->toBe(0);
});
