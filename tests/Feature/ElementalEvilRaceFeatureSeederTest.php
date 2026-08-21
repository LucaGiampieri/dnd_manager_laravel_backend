<?php

use App\Models\Feature;
use App\Models\Race;
use App\Models\RaceFeature;
use App\Models\Subrace;
use App\Models\SubraceFeature;
use Database\Seeders\ElementalEvilRaceFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Verifica la creazione completa e idempotente del catalogo EEPC
it('crea le capacità razziali eepc senza duplicati', function () {
    //Esegue due volte il seeder
    $this->seed(ElementalEvilRaceFeatureSeeder::class);
    $this->seed(ElementalEvilRaceFeatureSeeder::class);

    //Le quantità comprendono anche le capacità PHB
    expect(Feature::query()->count())->toBe(64)
        ->and(RaceFeature::query()->count())->toBe(33)
        ->and(SubraceFeature::query()->count())->toBe(31)
        ->and(
            Feature::query()
                ->whereNull('description')
                ->count()
        )->toBe(0);
});

//Verifica le capacità delle razze principali EEPC
it('assegna le capacità alle razze eepc', function () {
    $this->seed(ElementalEvilRaceFeatureSeeder::class);

    $expectedCounts = [
        'aarakocra_eepc_2015' => 2,
        'genasi_eepc_2015' => 0,
        'goliath_eepc_2015' => 4,
    ];

    foreach ($expectedCounts as $raceKey => $expectedCount) {
        $race = Race::query()
            ->where('key', $raceKey)
            ->firstOrFail();

        expect($race->features()->count())
            ->toBe($expectedCount);
    }

    $goliath = Race::query()
        ->where('key', 'goliath_eepc_2015')
        ->firstOrFail();

    expect(
        $goliath->features()
            ->orderByPivot('sort_order')
            ->pluck('name')
            ->all()
    )->toBe([
        'Atleta Nato',
        'Resistenza della Pietra',
        'Corporatura Possente',
        'Nato sulle Montagne',
    ]);
});

//Verifica le capacità delle sottorazze EEPC
it('assegna le capacità alle sottorazze eepc', function () {
    $this->seed(ElementalEvilRaceFeatureSeeder::class);

    $expectedCounts = [
        'water_genasi_eepc_2015' => 4,
        'air_genasi_eepc_2015' => 2,
        'fire_genasi_eepc_2015' => 3,
        'earth_genasi_eepc_2015' => 2,
        'deep_gnome_eepc_2015' => 2,
    ];

    foreach (
        $expectedCounts as $subraceKey => $expectedCount
    ) {
        $subrace = Subrace::query()
            ->where('key', $subraceKey)
            ->firstOrFail();

        expect($subrace->features()->count())
            ->toBe($expectedCount);
    }

    $waterGenasi = Subrace::query()
        ->where('key', 'water_genasi_eepc_2015')
        ->firstOrFail();

    expect(
        $waterGenasi->features()
            ->orderByPivot('sort_order')
            ->pluck('name')
            ->all()
    )->toBe([
        'Resistenza all’Acido',
        'Anfibio',
        'Nuotare',
        'Richiamare l’Onda',
    ]);
});

//Verifica l'ereditarietà delle capacità degli Gnomi
it('mantiene le capacità della razza nello gnomo delle profondità', function () {
    $this->seed(ElementalEvilRaceFeatureSeeder::class);

    $deepGnome = Subrace::query()
        ->where('key', 'deep_gnome_eepc_2015')
        ->with([
            'race.features',
            'features',
        ])
        ->firstOrFail();

    expect($deepGnome->race->key)->toBe('gnome')
        ->and(
            $deepGnome->race->features
                ->pluck('key')
                ->all()
        )->toContain(
            'gnome_darkvision_phb_2014',
            'gnome_cunning_phb_2014'
        )
        ->and(
            $deepGnome->features
                ->pluck('key')
                ->all()
        )->toContain(
            'deep_gnome_superior_darkvision_eepc_2015',
            'deep_gnome_stone_camouflage_eepc_2015'
        );
});

//Verifica gli utilizzi della Resistenza della Pietra
it('salva il recupero della resistenza della pietra', function () {
    $this->seed(ElementalEvilRaceFeatureSeeder::class);

    $stoneEndurance = Feature::query()
        ->where(
            'key',
            'goliath_stones_endurance_eepc_2015'
        )
        ->firstOrFail();

    expect($stoneEndurance->max_uses)->toBe(1)
        ->and($stoneEndurance->recharge)
        ->toBe('short_rest')
        ->and($stoneEndurance->races->first()->key)
        ->toBe('goliath_eepc_2015');
});
