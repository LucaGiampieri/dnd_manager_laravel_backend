<?php

use App\Models\CreatureType;
use App\Models\Feature;
use App\Models\Race;
use App\Models\RaceFeature;
use App\Models\Ruleset;
use App\Models\Subrace;
use App\Models\SubraceFeature;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Verifica le relazioni tra capacità, razze e sottorazze
it('gestisce le capacità di razze e sottorazze', function () {
    //Crea i cataloghi necessari
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
    ]);

    //Recupera il regolamento e il tipo Umanoide
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    $humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();

    //Crea una razza di prova
    $race = $ruleset->races()->create([
        'key' => 'test_race',
        'canonical_key' => 'test_race',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Razza di Prova',
        'creature_type_id' => $humanoid->id,
        'sort_order' => 1,
    ]);

    //Crea una sottorazza di prova
    $subrace = $race->subraces()->create([
        'key' => 'test_subrace',
        'canonical_key' => 'test_subrace',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Sottorazza di Prova',
        'sort_order' => 1,
    ]);

    //Crea una capacità razziale
    $raceFeature = $ruleset->features()->create([
        'key' => 'test_race_feature',
        'name' => 'Capacità Razziale di Prova',
        'type' => 'race',
        'level' => 1,
        'description' => 'Capacità utilizzata soltanto nel test.',
    ]);

    //Crea un effetto meccanico prodotto dalla capacità
    $effectDefinition = $raceFeature
        ->effectDefinitions()
        ->create([
            'key' => 'test_effect',
            'name' => 'Effetto di Prova',
            'application_type' => 'automatic',
            'target_scope' => 'source',
            'ends_with_source' => true,
            'description' => 'Effetto utilizzato soltanto nel test.',
            'sort_order' => 1,
        ]);

    //Crea una capacità della sottorazza
    $subraceFeature = $ruleset->features()->create([
        'key' => 'test_subrace_feature',
        'name' => 'Capacità della Sottorazza di Prova',
        'type' => 'subrace',
        'level' => 1,
        'description' => 'Capacità utilizzata soltanto nel test.',
    ]);

    //Assegna la capacità alla razza
    $raceAssignment = $race
        ->featureAssignments()
        ->create([
            'feature_id' => $raceFeature->id,
            'level' => 1,
            'sort_order' => 10,
        ]);

    //Assegna la capacità alla sottorazza
    $subraceAssignment = $subrace
        ->featureAssignments()
        ->create([
            'feature_id' => $subraceFeature->id,
            'level' => 3,
            'sort_order' => 20,
        ]);

    //Ricarica le relazioni
    $race->load('features');
    $subrace->load('features');

    //Verifica il regolamento della capacità
    expect($raceFeature->ruleset->is($ruleset))
        ->toBeTrue();

    //Verifica la capacità assegnata alla razza
    expect($race->features)->toHaveCount(1)
        ->and($race->features->first()->is($raceFeature))
        ->toBeTrue()
        ->and($race->features->first()->pivot->level)
        ->toBe(1)
        ->and($raceAssignment->feature->is($raceFeature))
        ->toBeTrue();

    //Verifica la capacità assegnata alla sottorazza
    expect($subrace->features)->toHaveCount(1)
        ->and($subrace->features->first()->is($subraceFeature))
        ->toBeTrue()
        ->and($subrace->features->first()->pivot->level)
        ->toBe(3)
        ->and($subraceAssignment->feature->is($subraceFeature))
        ->toBeTrue();

    //Verifica le relazioni inverse
    expect(
        $raceFeature->races()
            ->whereKey($race->id)
            ->exists()
    )->toBeTrue()
        ->and(
            $subraceFeature->subraces()
                ->whereKey($subrace->id)
                ->exists()
        )->toBeTrue();

    //Verifica il collegamento tra capacità ed effetto
    expect(
        $effectDefinition->source->is($raceFeature)
    )->toBeTrue()
        ->and(
            $raceFeature->effectDefinitions()
                ->whereKey($effectDefinition->id)
                ->exists()
        )->toBeTrue();
});

//Verifica i vincoli sulle assegnazioni
it('rifiuta livelli non validi e capacità duplicate', function () {
    //Crea i dati necessari
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
    ]);

    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    $humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();

    $race = $ruleset->races()->create([
        'key' => 'test_race',
        'canonical_key' => 'test_race',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Razza di Prova',
        'creature_type_id' => $humanoid->id,
        'sort_order' => 1,
    ]);

    $subrace = $race->subraces()->create([
        'key' => 'test_subrace',
        'canonical_key' => 'test_subrace',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Sottorazza di Prova',
        'sort_order' => 1,
    ]);

    $feature = $ruleset->features()->create([
        'key' => 'test_feature',
        'name' => 'Capacità di Prova',
        'type' => 'race',
        'level' => 1,
        'description' => 'Capacità utilizzata soltanto nel test.',
    ]);

    //Crea la prima assegnazione valida
    $race->featureAssignments()->create([
        'feature_id' => $feature->id,
        'level' => 1,
    ]);

    //Rifiuta la stessa capacità allo stesso livello
    expect(
        fn () => $race->featureAssignments()->create([
            'feature_id' => $feature->id,
            'level' => 1,
        ])
    )->toThrow(QueryException::class);

    //Rifiuta un livello inferiore a 1
    expect(
        fn () => $subrace->featureAssignments()->create([
            'feature_id' => $feature->id,
            'level' => 0,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Rifiuta un livello superiore a 20
    expect(
        fn () => $subrace->featureAssignments()->create([
            'feature_id' => $feature->id,
            'level' => 21,
        ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica le eliminazioni a cascata
it('elimina le assegnazioni quando viene cancellata la capacità', function () {
    //Crea i dati necessari
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
    ]);

    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    $humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();

    $race = $ruleset->races()->create([
        'key' => 'test_race',
        'canonical_key' => 'test_race',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Razza di Prova',
        'creature_type_id' => $humanoid->id,
        'sort_order' => 1,
    ]);

    $subrace = $race->subraces()->create([
        'key' => 'test_subrace',
        'canonical_key' => 'test_subrace',
        'version_key' => 'test_2014',
        'is_legacy' => false,
        'name' => 'Sottorazza di Prova',
        'sort_order' => 1,
    ]);

    $feature = $ruleset->features()->create([
        'key' => 'test_feature',
        'name' => 'Capacità di Prova',
        'type' => 'race',
        'level' => 1,
        'description' => 'Capacità utilizzata soltanto nel test.',
    ]);

    $race->featureAssignments()->create([
        'feature_id' => $feature->id,
        'level' => 1,
    ]);

    $subrace->featureAssignments()->create([
        'feature_id' => $feature->id,
        'level' => 1,
    ]);

    //Elimina la capacità principale
    $feature->delete();

    //Verifica la cancellazione delle assegnazioni
    expect(RaceFeature::query()->count())->toBe(0)
        ->and(SubraceFeature::query()->count())->toBe(0)
        ->and(Race::query()->count())->toBe(1)
        ->and(Subrace::query()->count())->toBe(1);
});
