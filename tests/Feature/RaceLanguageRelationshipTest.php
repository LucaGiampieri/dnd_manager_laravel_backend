<?php

use App\Models\CreatureType;
use App\Models\Language;
use App\Models\Race;
use App\Models\Ruleset;
use App\Models\Subrace;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara i cataloghi necessari
beforeEach(function () {
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
        LanguageSeeder::class,
    ]);

    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    $this->humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();
});

//Crea una razza utilizzabile dai test
function createLanguageTestRace(
    Ruleset $ruleset,
    CreatureType $humanoid
): Race {
    return $ruleset->races()->create([
        'key' => 'test_elf',
        'canonical_key' => 'test_elf',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Elfo di Prova',
        'creature_type_id' => $humanoid->id,
        'selectable' => true,
        'sort_order' => 1,
    ]);
}

it('collega lingue a razze e sottorazze', function () {
    $race = createLanguageTestRace(
        $this->ruleset,
        $this->humanoid
    );

    $subrace = $race->subraces()->create([
        'key' => 'test_high_elf',
        'canonical_key' => 'test_high_elf',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Elfo Alto di Prova',
        'sort_order' => 1,
    ]);

    $common = Language::query()
        ->where('key', 'common')
        ->firstOrFail();

    $elvish = Language::query()
        ->where('key', 'elvish')
        ->firstOrFail();

    $raceAssignment = $race
        ->languageAssignments()
        ->create([
            'language_id' => $common->id,
            'notes' => 'Lingua della razza.',
        ]);

    $subraceAssignment = $subrace
        ->languageAssignments()
        ->create([
            'language_id' => $elvish->id,
            'notes' => 'Lingua della sottorazza.',
        ]);

    expect($raceAssignment->race->is($race))->toBeTrue()
        ->and($raceAssignment->language->is($common))
        ->toBeTrue()
        ->and($subraceAssignment->subrace->is($subrace))
        ->toBeTrue()
        ->and($subraceAssignment->language->is($elvish))
        ->toBeTrue()
        ->and($common->raceAssignments)
        ->toHaveCount(1)
        ->and($elvish->subraceAssignments)
        ->toHaveCount(1);
});

it('rifiuta assegnazioni linguistiche duplicate', function () {
    $race = createLanguageTestRace(
        $this->ruleset,
        $this->humanoid
    );

    $common = Language::query()
        ->where('key', 'common')
        ->firstOrFail();

    $race->languageAssignments()->create([
        'language_id' => $common->id,
    ]);

    expect(
        fn () => $race->languageAssignments()->create([
            'language_id' => $common->id,
        ])
    )->toThrow(QueryException::class);
});

it('elimina le lingue assegnate insieme alla razza', function () {
    $race = createLanguageTestRace(
        $this->ruleset,
        $this->humanoid
    );

    $subrace = $race->subraces()->create([
        'key' => 'test_subrace',
        'canonical_key' => 'test_subrace',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Sottorazza di Prova',
        'sort_order' => 1,
    ]);

    $common = Language::query()
        ->where('key', 'common')
        ->firstOrFail();

    $elvish = Language::query()
        ->where('key', 'elvish')
        ->firstOrFail();

    $raceAssignment = $race
        ->languageAssignments()
        ->create([
            'language_id' => $common->id,
        ]);

    $subraceAssignment = $subrace
        ->languageAssignments()
        ->create([
            'language_id' => $elvish->id,
        ]);

    $raceAssignmentId = $raceAssignment->id;
    $subraceAssignmentId = $subraceAssignment->id;

    $race->delete();

    expect(
        DB::table('race_languages')
            ->where('id', $raceAssignmentId)
            ->exists()
    )->toBeFalse()
        ->and(
            DB::table('subrace_languages')
                ->where('id', $subraceAssignmentId)
                ->exists()
        )->toBeFalse()
        ->and(Subrace::query()->whereKey($subrace->id)->exists())
        ->toBeFalse();
});
