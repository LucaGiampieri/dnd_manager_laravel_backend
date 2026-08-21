<?php

use App\Models\CreatureType;
use App\Models\Race;
use App\Models\RacePhysicalTrait;
use App\Models\Ruleset;
use App\Models\SubracePhysicalTrait;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara i cataloghi richiesti prima di ogni test
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea il regolamento e i tipi di creatura
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
    ]);
});

//Crea una razza utilizzabile dai test di questo file
function createRaceForPhysicalTraitTest(
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

//Verifica le formule di altezza e peso
it('calcola altezza e peso usando le formule della razza', function () {
    //Crea una razza di riferimento
    $human = createRaceForPhysicalTraitTest(
        'human',
        'Umano'
    );

    //Registra una formula di esempio basata sui valori umani
    $physicalTraits = $human->physicalTraits()->create([
        'maturity_age_years' => 18,
        'lifespan_years' => 100,
        'min_height_cm' => 147,
        'max_height_cm' => 193,
        'min_weight_kg' => '51.000',
        'max_weight_kg' => '145.000',
        'base_height_cm' => '142.240',
        'height_modifier_dice_count' => 2,
        'height_modifier_die_size' => 10,
        'height_modifier_unit_cm' => '2.540',
        'base_weight_kg' => '49.895',
        'weight_modifier_dice_count' => 2,
        'weight_modifier_die_size' => 4,
        'weight_modifier_unit_kg' => '0.453592',
        'weight_uses_height_modifier' => true,
        'appearance' => 'Descrizione utilizzata soltanto nel test.',
    ]);

    //Simula un risultato totale di 12 ottenuto con 2d10
    $heightCm = $physicalTraits->calculateHeightCm(12);

    //Simula un risultato totale di 5 ottenuto con 2d4
    $weightKg = $physicalTraits->calculateWeightKg(
        5,
        12
    );

    //Verifica i risultati dei calcoli
    expect($heightCm)->toBe(172.72)
        ->and($weightKg)->toBe(77.111);

    //Verifica le formule leggibili prodotte dal modello
    expect($physicalTraits->height_formula)
        ->toBe('142.24 cm + 2d10 × 2.54 cm')
        ->and($physicalTraits->weight_formula)
        ->toBe(
            '49.895 kg + modificatore altezza × '
            . '2d4 × 0.453592 kg'
        );

    //Verifica che le formule siano incluse anche nell'array pubblico
    $physicalTraitsArray = $physicalTraits->toArray();

    expect($physicalTraitsArray['height_formula'])
        ->toBe($physicalTraits->height_formula)
        ->and($physicalTraitsArray['weight_formula'])
        ->toBe($physicalTraits->weight_formula);

    //Verifica la relazione molti-a-uno verso la razza
    expect($physicalTraits->race->is($human))->toBeTrue();

    //Verifica la relazione inversa uno-a-uno dalla razza
    expect(
        $human
            ->fresh()
            ->physicalTraits
            ->is($physicalTraits)
    )->toBeTrue();
});

//Verifica che altezza e peso possano essere fissi
it('gestisce valori fisici senza utilizzare formule con dadi', function () {
    //Crea una razza personalizzata
    $lineage = createRaceForPhysicalTraitTest(
        'custom_lineage',
        'Stirpe Personalizzata'
    );

    //Registra valori fissi senza configurare alcun dado
    $physicalTraits = $lineage->physicalTraits()->create([
        'base_height_cm' => '180.000',
        'base_weight_kg' => '80.000',
        'weight_uses_height_modifier' => false,
        'appearance' => 'Aspetto deciso liberamente.',
    ]);

    //Senza dadi vengono restituiti direttamente i valori base
    expect(
        $physicalTraits->calculateHeightCm(0)
    )->toBe(180.0)
        ->and(
            $physicalTraits->calculateWeightKg(0)
        )->toBe(80.0);

    //Le formule leggibili mostrano soltanto i valori fissi
    expect($physicalTraits->height_formula)
        ->toBe('180 cm')
        ->and($physicalTraits->weight_formula)
        ->toBe('80 kg');
});

//Verifica che il calcolo condiviso funzioni anche per le sottorazze
it('gestisce i tratti fisici specifici di una sottorazza', function () {
    //Crea la razza principale
    $elf = createRaceForPhysicalTraitTest(
        'elf',
        'Elfo'
    );

    //Crea una sottorazza
    $highElf = $elf->subraces()->create([
        'key' => 'high_elf',
        'name' => 'Elfo Alto',
        'sort_order' => 1,
    ]);

    //Registra i tratti fisici specifici della sottorazza
    $physicalTraits = $highElf
        ->physicalTraits()
        ->create([
            'maturity_age_years' => 100,
            'lifespan_years' => 750,
            'base_height_cm' => '137.160',
            'height_modifier_dice_count' => 2,
            'height_modifier_die_size' => 10,
            'height_modifier_unit_cm' => '2.540',
            'base_weight_kg' => '40.823',
            'weight_modifier_dice_count' => 1,
            'weight_modifier_die_size' => 4,
            'weight_modifier_unit_kg' => '0.453592',
            'weight_uses_height_modifier' => true,
        ]);

    //Verifica che la sottorazza utilizzi lo stesso calcolatore
    expect(
        $physicalTraits->calculateHeightCm(10)
    )->toBe(162.56)
        ->and(
            $physicalTraits->calculateWeightKg(2, 10)
        )->toBe(49.895);

    //Verifica la relazione molti-a-uno verso la sottorazza
    expect(
        $physicalTraits->subrace->is($highElf)
    )->toBeTrue();

    //Verifica la relazione inversa uno-a-uno
    expect(
        $highElf
            ->fresh()
            ->physicalTraits
            ->is($physicalTraits)
    )->toBeTrue();
});

//Verifica che le configurazioni e i risultati non validi siano rifiutati
it('rifiuta formule fisiche incomplete o risultati impossibili', function () {
    //Crea una razza di riferimento
    $human = createRaceForPhysicalTraitTest(
        'human',
        'Umano'
    );

    //Rifiuta una formula priva della dimensione del dado
    expect(
        fn () => $human->physicalTraits()->create([
            'base_height_cm' => '142.240',
            'height_modifier_dice_count' => 2,
            'height_modifier_unit_cm' => '2.540',
        ])
    )->toThrow(InvalidArgumentException::class);

    //Rifiuta un intervallo nel quale il minimo supera il massimo
    expect(
        fn () => $human->physicalTraits()->create([
            'min_height_cm' => 200,
            'max_height_cm' => 150,
        ])
    )->toThrow(InvalidArgumentException::class);

    //Crea una configurazione valida
    $physicalTraits = $human->physicalTraits()->create([
        'base_height_cm' => '142.240',
        'height_modifier_dice_count' => 2,
        'height_modifier_die_size' => 10,
        'height_modifier_unit_cm' => '2.540',
        'base_weight_kg' => '49.895',
        'weight_modifier_dice_count' => 2,
        'weight_modifier_die_size' => 4,
        'weight_modifier_unit_kg' => '0.453592',
        'weight_uses_height_modifier' => true,
    ]);

    //Con 2d10 non è possibile ottenere un risultato di uno
    expect(
        fn () => $physicalTraits->calculateHeightCm(1)
    )->toThrow(InvalidArgumentException::class);

    //Il peso richiede il modificatore di altezza
    expect(
        fn () => $physicalTraits->calculateWeightKg(5)
    )->toThrow(InvalidArgumentException::class);
});

//Verifica unicità e cancellazione a cascata
it('mantiene una sola configurazione fisica per razza', function () {
    //Crea una razza di riferimento
    $human = createRaceForPhysicalTraitTest(
        'human',
        'Umano'
    );

    //Crea la configurazione fisica
    $physicalTraits = $human->physicalTraits()->create([
        'base_height_cm' => '180.000',
        'base_weight_kg' => '80.000',
    ]);

    //Rifiuta una seconda configurazione per la stessa razza
    expect(
        fn () => $human->physicalTraits()->create([
            'base_height_cm' => '170.000',
            'base_weight_kg' => '70.000',
        ])
    )->toThrow(QueryException::class);

    //Memorizza l'identificativo prima della cancellazione
    $physicalTraitsId = $physicalTraits->id;

    //Cancella la razza principale
    $human->delete();

    //Verifica la cancellazione a cascata dei tratti fisici
    expect(
        RacePhysicalTrait::query()
            ->whereKey($physicalTraitsId)
            ->exists()
    )->toBeFalse();

    //Verifica che il modello delle sottorazze sia disponibile
    expect(
        new SubracePhysicalTrait()
    )->toBeInstanceOf(SubracePhysicalTrait::class);
});

//Verifica una formula del peso basata su una quantità fissa
it('calcola il peso usando un modificatore fisso', function () {
    //Crea una razza di riferimento
    $halfling = createRaceForPhysicalTraitTest(
        'halfling',
        'Halfling'
    );

    //Registra la formula con moltiplicatore fisso di una libbra
    $physicalTraits = $halfling->physicalTraits()->create([
        'base_height_cm' => '78.740',
        'height_modifier_dice_count' => 2,
        'height_modifier_die_size' => 4,
        'height_modifier_unit_cm' => '2.540',
        'base_weight_kg' => '15.876',
        'weight_modifier_fixed_kg' => '0.453592',
        'weight_uses_height_modifier' => true,
    ]);

    //Simula un risultato di cinque ottenuto con i dadi dell'altezza
    expect(
        $physicalTraits->calculateHeightCm(5)
    )->toBe(91.44);

    //Il peso non richiede un secondo tiro di dadi
    expect(
        $physicalTraits->calculateWeightKg(
            null,
            5
        )
    )->toBe(18.144);

    //Verifica la formula leggibile
    expect($physicalTraits->weight_formula)
        ->toBe(
            '15.876 kg + modificatore altezza × '
            . '0.453592 kg'
        );

    //Verifica che il modificatore venga convertito correttamente
    expect($physicalTraits->weight_modifier_fixed_kg)
        ->toBe('0.453592');

    //Il modificatore di altezza rimane obbligatorio
    expect(
        fn () => $physicalTraits->calculateWeightKg()
    )->toThrow(\InvalidArgumentException::class);

    //Dadi e modificatore fisso non possono essere usati insieme
    expect(
        fn () => $halfling->physicalTraits()->create([
            'base_weight_kg' => '15.876',
            'weight_modifier_dice_count' => 1,
            'weight_modifier_die_size' => 4,
            'weight_modifier_unit_kg' => '0.453592',
            'weight_modifier_fixed_kg' => '0.453592',
        ])
    )->toThrow(\InvalidArgumentException::class);
});
