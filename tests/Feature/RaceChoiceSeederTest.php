<?php

use App\Models\Race;
use App\Models\RaceChoice;
use App\Models\Subrace;
use App\Models\SubraceChoice;
use Database\Seeders\AbilitySeeder;
use Database\Seeders\RaceAbilityBonusSeeder;
use Database\Seeders\RaceChoiceSeeder;
use Database\Seeders\RaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica che il seeder crei tutte le scelte senza duplicati
it('crea le scelte flessibili delle razze senza duplicati', function () {
    //Crea le caratteristiche, le razze e i bonus fissi richiesti
    $this->seed(AbilitySeeder::class);
    $this->seed(RaceSeeder::class);
    $this->seed(RaceAbilityBonusSeeder::class);

    //Esegue due volte il seeder per verificarne l'idempotenza
    $this->seed(RaceChoiceSeeder::class);
    $this->seed(RaceChoiceSeeder::class);

    //Verifica che siano state create soltanto le due scelte previste
    expect(RaceChoice::query()->count())->toBe(1)
        ->and(SubraceChoice::query()->count())->toBe(1);

    //Recupera il Mezzelfo
    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    //Recupera la scelta flessibile del Mezzelfo
    $halfElfChoice = $halfElf->choices()
        ->where('key', 'flexible_ability_score_increases')
        ->firstOrFail();

    //Recupera le opzioni nell'ordine di visualizzazione
    $halfElfOptions = $halfElfChoice->options()
        ->orderBy('sort_order')
        ->get();

    //Verifica le regole della scelta del Mezzelfo
    expect($halfElfChoice->choice_type)->toBe('ability')
        ->and($halfElfChoice->choose)->toBe(2)
        ->and($halfElfChoice->required)->toBeTrue()
        ->and($halfElfOptions)->toHaveCount(5)
        ->and($halfElfOptions->pluck('key')->all())->toBe([
            'strength',
            'dexterity',
            'constitution',
            'intelligence',
            'wisdom',
        ]);

    //Recupera la sottorazza dell'Umano Variante
    $variantHuman = Subrace::query()
        ->where('key', 'variant_human')
        ->firstOrFail();

    //Recupera la scelta dell'Umano Variante
    $variantHumanChoice = $variantHuman->choices()
        ->where('key', 'variant_ability_score_increases')
        ->firstOrFail();

    //Recupera tutte le caratteristiche selezionabili
    $variantHumanOptions = $variantHumanChoice->options()
        ->orderBy('sort_order')
        ->get();

    //Verifica le regole della scelta dell'Umano Variante
    expect($variantHumanChoice->choice_type)->toBe('ability')
        ->and($variantHumanChoice->choose)->toBe(2)
        ->and($variantHumanChoice->required)->toBeTrue()
        ->and($variantHumanOptions)->toHaveCount(6)
        ->and($variantHumanOptions->pluck('key')->all())->toBe([
            'strength',
            'dexterity',
            'constitution',
            'intelligence',
            'wisdom',
            'charisma',
        ]);

    //Verifica che ogni opzione conceda un incremento di uno
    expect(
        $halfElfOptions->pluck('value')->unique()->values()->all()
    )->toBe([1]);

    expect(
        $variantHumanOptions->pluck('value')->unique()->values()->all()
    )->toBe([1]);
});
