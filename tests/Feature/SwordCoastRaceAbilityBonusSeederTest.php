<?php

use App\Models\Subrace;
use App\Models\SubraceAbilityBonus;
use Database\Seeders\SwordCoastRaceAbilityBonusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione idempotente dei bonus SCAG
it('crea i bonus delle sottorazze SCAG senza duplicati', function () {
    $this->seed(SwordCoastRaceAbilityBonusSeeder::class);
    $this->seed(SwordCoastRaceAbilityBonusSeeder::class);

    //Conta soltanto i bonus appartenenti alle sottorazze SCAG
    $bonusCount = SubraceAbilityBonus::query()
        ->whereHas('subrace', function ($query) {
            $query->where('version_key', 'scag_2015');
        })
        ->count();

    expect($bonusCount)->toBe(2);
});

//Verifica i valori assegnati alle caratteristiche corrette
it('assegna Forza al Duergar e Saggezza all Halfling degli Spiriti', function () {
    $this->seed(SwordCoastRaceAbilityBonusSeeder::class);

    $duergar = Subrace::query()
        ->where('key', 'duergar_scag_2015')
        ->firstOrFail();

    $ghostwise = Subrace::query()
        ->where('key', 'ghostwise_halfling_scag_2015')
        ->firstOrFail();

    $duergarBonus = $duergar->abilityBonuses()
        ->with('ability')
        ->firstOrFail();

    $ghostwiseBonus = $ghostwise->abilityBonuses()
        ->with('ability')
        ->firstOrFail();

    expect($duergarBonus->ability->short_name)->toBe('FOR')
        ->and($duergarBonus->bonus)->toBe(1)
        ->and($ghostwiseBonus->ability->short_name)->toBe('SAG')
        ->and($ghostwiseBonus->bonus)->toBe(1);
});

//Verifica la compatibilità con la regola opzionale di Tasha
it('rende riassegnabili i bonus attraverso le regole opzionali', function () {
    $this->seed(SwordCoastRaceAbilityBonusSeeder::class);

    $bonuses = SubraceAbilityBonus::query()
        ->whereHas('subrace', function ($query) {
            $query->where('version_key', 'scag_2015');
        })
        ->get();

    expect($bonuses)->toHaveCount(2)
        ->and($bonuses->every(
            fn (SubraceAbilityBonus $bonus) =>
                $bonus->can_be_reassigned === true
        ))->toBeTrue();
});
