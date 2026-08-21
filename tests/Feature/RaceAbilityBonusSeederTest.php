<?php

use App\Models\Race;
use App\Models\RaceAbilityBonus;
use App\Models\Subrace;
use App\Models\SubraceAbilityBonus;
use Database\Seeders\AbilitySeeder;
use Database\Seeders\RaceAbilityBonusSeeder;
use Database\Seeders\RaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara i cataloghi richiesti dai bonus razziali
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea il catalogo delle caratteristiche
    $this->seed(AbilitySeeder::class);

    //Crea razze e sottorazze
    $this->seed(RaceSeeder::class);

    //Esegue due volte il seeder dei bonus
    //per verificarne l'idempotenza
    $this->seed(RaceAbilityBonusSeeder::class);
    $this->seed(RaceAbilityBonusSeeder::class);
});

//Verifica tutti i bonus fissi delle razze principali
it('crea i bonus fissi delle razze senza duplicati', function () {
    //Definisce i bonus razziali attesi
    $expectedRaceBonuses = [
        'dwarf' => [
            'COS' => 2,
        ],
        'elf' => [
            'DES' => 2,
        ],
        'halfling' => [
            'DES' => 2,
        ],
        'human' => [
            'FOR' => 1,
            'DES' => 1,
            'COS' => 1,
            'INT' => 1,
            'SAG' => 1,
            'CAR' => 1,
        ],
        'dragonborn' => [
            'FOR' => 2,
            'CAR' => 1,
        ],
        'gnome' => [
            'INT' => 2,
        ],
        'half_elf' => [
            'CAR' => 2,
        ],
        'half_orc' => [
            'FOR' => 2,
            'COS' => 1,
        ],
        'tiefling' => [
            'INT' => 1,
            'CAR' => 2,
        ],
    ];

    //Verifica il numero complessivo dei bonus razziali
    expect(RaceAbilityBonus::query()->count())->toBe(17);

    //Controlla separatamente ogni razza
    foreach (
        $expectedRaceBonuses as $raceKey => $expectedBonuses
    ) {
        //Recupera la razza verificata
        $race = Race::query()
            ->where('key', $raceKey)
            ->firstOrFail();

        //Converte i bonus in un array abbreviazione-valore
        $actualBonuses = $race
            ->abilityBonuses()
            ->with('ability')
            ->get()
            ->mapWithKeys(
                fn (RaceAbilityBonus $abilityBonus) => [
                    $abilityBonus->ability->short_name =>
                        $abilityBonus->bonus,
                ]
            )
            ->all();

        //Verifica tutti i bonus della razza
        expect($actualBonuses)->toBe($expectedBonuses);
    }
});

//Verifica tutti i bonus fissi delle sottorazze
it('crea i bonus fissi delle sottorazze senza duplicati', function () {
    //Definisce i bonus delle sottorazze attesi
    $expectedSubraceBonuses = [
        'hill_dwarf' => [
            'SAG' => 1,
        ],
        'mountain_dwarf' => [
            'FOR' => 2,
        ],
        'high_elf' => [
            'INT' => 1,
        ],
        'wood_elf' => [
            'SAG' => 1,
        ],
        'drow' => [
            'CAR' => 1,
        ],
        'lightfoot_halfling' => [
            'CAR' => 1,
        ],
        'stout_halfling' => [
            'COS' => 1,
        ],
        'forest_gnome' => [
            'DES' => 1,
        ],
        'rock_gnome' => [
            'COS' => 1,
        ],
    ];

    //Verifica il numero complessivo dei bonus delle sottorazze
    expect(
        SubraceAbilityBonus::query()->count()
    )->toBe(9);

    //Controlla separatamente ogni sottorazza
    foreach (
        $expectedSubraceBonuses as $subraceKey => $expectedBonuses
    ) {
        //Recupera la sottorazza verificata
        $subrace = Subrace::query()
            ->where('key', $subraceKey)
            ->firstOrFail();

        //Converte i bonus in un array abbreviazione-valore
        $actualBonuses = $subrace
            ->abilityBonuses()
            ->with('ability')
            ->get()
            ->mapWithKeys(
                fn (SubraceAbilityBonus $abilityBonus) => [
                    $abilityBonus->ability->short_name =>
                        $abilityBonus->bonus,
                ]
            )
            ->all();

        //Verifica tutti i bonus della sottorazza
        expect($actualBonuses)->toBe($expectedBonuses);
    }
});

//Verifica la compatibilità con la regola opzionale di Tasha
it('rende riassegnabili i bonus usando le regole di Tasha', function () {
    //Verifica che tutti i bonus delle razze siano riassegnabili
    expect(
        RaceAbilityBonus::query()
            ->where('can_be_reassigned', false)
            ->count()
    )->toBe(0);

    //Verifica che tutti i bonus delle sottorazze siano riassegnabili
    expect(
        SubraceAbilityBonus::query()
            ->where('can_be_reassigned', false)
            ->count()
    )->toBe(0);

    //Recupera l'Umano standard
    $human = Race::query()
        ->where('key', 'human')
        ->firstOrFail();

    //Verifica i sei bonus dell'Umano standard
    expect($human->abilityBonuses)->toHaveCount(6);

    //Verifica che i bonus dell'Umano ricordino l'eccezione
    expect(
        $human->abilityBonuses
            ->whereNull('notes')
            ->count()
    )->toBe(0);

    //Recupera l'Umano Variante
    $variantHuman = Subrace::query()
        ->where('key', 'variant_human')
        ->firstOrFail();

    //Verifica che sostituisca i bonus della razza principale
    expect($variantHuman->is_variant)->toBeTrue()
        ->and($variantHuman->replaces_race_ability_bonuses)
        ->toBeTrue();

    //Verifica che l'Umano Variante non abbia bonus fissi
    expect($variantHuman->abilityBonuses)->toHaveCount(0);

    //Recupera il Mezzelfo
    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    //Verifica che per ora contenga soltanto Carisma +2
    expect($halfElf->abilityBonuses)->toHaveCount(1)
        ->and($halfElf->abilityBonuses->first()->ability->short_name)
        ->toBe('CAR')
        ->and($halfElf->abilityBonuses->first()->bonus)
        ->toBe(2);
});
