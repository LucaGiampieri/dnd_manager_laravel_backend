<?php

use App\Models\Feature;
use App\Models\Subrace;
use Database\Seeders\SwordCoastRaceFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione idempotente delle capacità SCAG
it('crea le capacità razziali dello SCAG senza duplicati', function () {
    $this->seed(SwordCoastRaceFeatureSeeder::class);
    $this->seed(SwordCoastRaceFeatureSeeder::class);

    $featureKeys = [
        'duergar_superior_darkvision_scag_2015',
        'duergar_resilience_scag_2015',
        'duergar_magic_scag_2015',
        'duergar_sunlight_sensitivity_scag_2015',
        'ghostwise_silent_speech_scag_2015',
    ];

    $features = Feature::query()
        ->whereIn('key', $featureKeys)
        ->get();

    expect($features)->toHaveCount(5)
        ->and($features->pluck('key')->sort()->values()->all())
        ->toBe(collect($featureKeys)->sort()->values()->all())
        ->and($features->every(
            fn (Feature $feature) => $feature->type === 'subrace'
        ))->toBeTrue();
});

//Verifica le capacità assegnate al Duergar
it('assegna tutte le capacità al Duergar', function () {
    $this->seed(SwordCoastRaceFeatureSeeder::class);

    $duergar = Subrace::query()
        ->where('key', 'duergar_scag_2015')
        ->firstOrFail();

    $featureKeys = $duergar->featureAssignments()
        ->with('feature')
        ->orderBy('sort_order')
        ->get()
        ->pluck('feature.key')
        ->all();

    expect($featureKeys)->toBe([
        'duergar_superior_darkvision_scag_2015',
        'duergar_resilience_scag_2015',
        'duergar_magic_scag_2015',
        'duergar_sunlight_sensitivity_scag_2015',
    ]);
});

//Verifica la capacità dell'Halfling degli Spiriti
it('assegna il Linguaggio Silenzioso all Halfling degli Spiriti', function () {
    $this->seed(SwordCoastRaceFeatureSeeder::class);

    $ghostwise = Subrace::query()
        ->where('key', 'ghostwise_halfling_scag_2015')
        ->firstOrFail();

    $assignment = $ghostwise->featureAssignments()
        ->with('feature')
        ->firstOrFail();

    expect($assignment->feature->key)
        ->toBe('ghostwise_silent_speech_scag_2015')
        ->and($assignment->feature->description)
        ->toContain('9 metri')
        ->and($assignment->level)->toBe(1);
});

//Verifica i livelli previsti dalla Magia Duergar
it('descrive correttamente la progressione della Magia Duergar', function () {
    $this->seed(SwordCoastRaceFeatureSeeder::class);

    $duergarMagic = Feature::query()
        ->where('key', 'duergar_magic_scag_2015')
        ->firstOrFail();

    expect($duergarMagic->description)
        ->toContain('3° livello')
        ->toContain('5° livello')
        ->toContain('riposo lungo')
        ->and($duergarMagic->max_uses)->toBeNull()
        ->and($duergarMagic->recharge)->toBeNull();
});
