<?php

use App\Models\Race;
use App\Models\RaceSense;
use App\Models\Sense;
use App\Models\Subrace;
use App\Models\SubraceSense;
use Database\Seeders\RaceSeeder;
use Database\Seeders\SenseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara razze e catalogo dei sensi
beforeEach(function () {
    $this->seed([
        RaceSeeder::class,
        SenseSeeder::class,
    ]);

    $this->darkvision = Sense::query()
        ->where('key', 'darkvision')
        ->firstOrFail();
});

//Verifica l'assegnazione di un senso a una razza
it('gestisce i sensi automatici di una razza', function () {
    $dwarf = Race::query()
        ->where('key', 'dwarf')
        ->firstOrFail();

    $assignment = $dwarf->senseAssignments()->create([
        'sense_id' => $this->darkvision->id,
        'range_meters' => '18.000',
        'is_blind_beyond_range' => false,
        'condition' => 'Condizione creata per il test.',
        'notes' => null,
    ]);

    expect($assignment->race->is($dwarf))->toBeTrue()
        ->and($assignment->sense->is($this->darkvision))
        ->toBeTrue()
        ->and($assignment->range_meters)->toBe('18.000')
        ->and($assignment->is_blind_beyond_range)
        ->toBeFalse()
        ->and($this->darkvision->raceAssignments)
        ->toHaveCount(1);
});

//Verifica l'assegnazione specifica di una sottorazza
it('gestisce la sostituzione del senso in una sottorazza', function () {
    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    $drow = Subrace::query()
        ->where('key', 'drow')
        ->firstOrFail();

    $elf->senseAssignments()->create([
        'sense_id' => $this->darkvision->id,
        'range_meters' => '18.000',
        'is_blind_beyond_range' => false,
    ]);

    $drowAssignment = $drow->senseAssignments()->create([
        'sense_id' => $this->darkvision->id,
        'range_meters' => '36.000',
        'is_blind_beyond_range' => false,
        'condition' =>
            'Sostituisce la portata della razza principale.',
    ]);

    expect($drowAssignment->subrace->is($drow))->toBeTrue()
        ->and($drowAssignment->range_meters)->toBe('36.000')
        ->and($elf->senseAssignments()->first()->range_meters)
        ->toBe('18.000')
        ->and($this->darkvision->subraceAssignments)
        ->toHaveCount(1);
});

//Verifica la validazione e il vincolo univoco
it('rifiuta portate non valide e sensi duplicati', function () {
    $dwarf = Race::query()
        ->where('key', 'dwarf')
        ->firstOrFail();

    expect(
        fn () => $dwarf->senseAssignments()->create([
            'sense_id' => $this->darkvision->id,
            'range_meters' => '0.000',
        ])
    )->toThrow(InvalidArgumentException::class);

    $dwarf->senseAssignments()->create([
        'sense_id' => $this->darkvision->id,
        'range_meters' => '18.000',
    ]);

    expect(
        fn () => $dwarf->senseAssignments()->create([
            'sense_id' => $this->darkvision->id,
            'range_meters' => '36.000',
        ])
    )->toThrow(QueryException::class);
});

//Verifica la cancellazione a cascata
it('elimina i sensi quando viene cancellata la razza', function () {
    $dwarf = Race::query()
        ->where('key', 'dwarf')
        ->firstOrFail();

    $assignment = $dwarf->senseAssignments()->create([
        'sense_id' => $this->darkvision->id,
        'range_meters' => '18.000',
    ]);

    $assignmentId = $assignment->id;

    $dwarf->delete();

    expect(
        RaceSense::query()
            ->whereKey($assignmentId)
            ->exists()
    )->toBeFalse()
        ->and(SubraceSense::query()->count())->toBe(0);
});
