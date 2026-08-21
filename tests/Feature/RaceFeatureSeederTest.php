<?php

use App\Models\Feature;
use App\Models\Race;
use App\Models\RaceFeature;
use App\Models\Subrace;
use App\Models\SubraceFeature;
use Database\Seeders\RaceFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Verifica la creazione idempotente del catalogo
it('crea le capacità razziali del phb senza duplicati', function () {
    //Esegue due volte il seeder
    $this->seed(RaceFeatureSeeder::class);
    $this->seed(RaceFeatureSeeder::class);

    //Verifica le quantità complessive
    expect(Feature::query()->count())->toBe(45)
        ->and(RaceFeature::query()->count())->toBe(27)
        ->and(SubraceFeature::query()->count())->toBe(18)
        ->and(
            Feature::query()
                ->whereNull('description')
                ->count()
        )->toBe(0);
});

//Verifica le capacità delle razze principali
it('assegna le capacità alle razze corrette', function () {
    $this->seed(RaceFeatureSeeder::class);

    $expectedCounts = [
        'dwarf' => 5,
        'elf' => 4,
        'halfling' => 3,
        'human' => 0,
        'dragonborn' => 3,
        'gnome' => 2,
        'half_elf' => 3,
        'half_orc' => 4,
        'tiefling' => 3,
    ];

    foreach ($expectedCounts as $raceKey => $expectedCount) {
        $race = Race::query()
            ->where('key', $raceKey)
            ->firstOrFail();

        expect($race->features()->count())
            ->toBe($expectedCount);
    }

    $dwarf = Race::query()
        ->where('key', 'dwarf')
        ->firstOrFail();

    expect(
        $dwarf->features()
            ->orderByPivot('sort_order')
            ->pluck('name')
            ->all()
    )->toBe([
        'Scurovisione',
        'Resilienza Nanica',
        'Addestramento da Combattimento Nanico',
        'Competenza negli Strumenti',
        'Esperto Minatore',
    ]);
});

//Verifica le capacità delle sottorazze
it('assegna le capacità alle sottorazze corrette', function () {
    $this->seed(RaceFeatureSeeder::class);

    $expectedCounts = [
        'hill_dwarf' => 1,
        'mountain_dwarf' => 1,
        'high_elf' => 3,
        'wood_elf' => 3,
        'drow' => 4,
        'lightfoot_halfling' => 1,
        'stout_halfling' => 1,
        'variant_human' => 0,
        'forest_gnome' => 2,
        'rock_gnome' => 2,
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

    $drow = Subrace::query()
        ->where('key', 'drow')
        ->firstOrFail();

    expect(
        $drow->features()
            ->orderByPivot('sort_order')
            ->pluck('name')
            ->all()
    )->toBe([
        'Scurovisione Superiore',
        'Sensibilità alla Luce del Sole',
        'Magia Drow',
        'Addestramento nelle Armi Drow',
    ]);
});

//Verifica le proprietà meccaniche delle capacità
it('salva utilizzi e recuperi delle capacità limitate', function () {
    $this->seed(RaceFeatureSeeder::class);

    $breathWeapon = Feature::query()
        ->where('key', 'dragonborn_breath_weapon_phb_2014')
        ->firstOrFail();

    $relentlessEndurance = Feature::query()
        ->where(
            'key',
            'half_orc_relentless_endurance_phb_2014'
        )
        ->firstOrFail();

    expect($breathWeapon->max_uses)->toBe(1)
        ->and($breathWeapon->recharge)->toBe('short_rest')
        ->and($relentlessEndurance->max_uses)->toBe(1)
        ->and($relentlessEndurance->recharge)
        ->toBe('long_rest')
        ->and($breathWeapon->races->first()->key)
        ->toBe('dragonborn')
        ->and($relentlessEndurance->races->first()->key)
        ->toBe('half_orc');
});
