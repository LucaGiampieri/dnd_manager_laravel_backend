<?php

use App\Models\Ability;
use App\Models\Race;
use App\Models\RaceChoice;
use App\Models\RaceChoiceOption;
use App\Models\Subrace;
use App\Models\SubraceChoice;
use App\Models\SubraceChoiceOption;
use Database\Seeders\AbilitySeeder;
use Database\Seeders\RaceSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce i cataloghi richiesti dalle scelte
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea caratteristiche, razze e sottorazze
    $this->seed([
        AbilitySeeder::class,
        RaceSeeder::class,
    ]);
});

//Verifica le scelte definite direttamente da una razza
it('gestisce le scelte di caratteristica di una razza', function () {
    //Recupera il Mezzelfo
    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    //Recupera due caratteristiche utilizzate come opzioni
    $strength = Ability::query()
        ->where('short_name', 'FOR')
        ->firstOrFail();

    $dexterity = Ability::query()
        ->where('short_name', 'DES')
        ->firstOrFail();

    //Crea la regola che richiede due caratteristiche
    $choice = $halfElf->choices()->create([
        'key' => 'flexible_ability_increases',
        'name' => 'Incrementi di caratteristica',
        'choice_type' => 'ability',
        'choose' => 2,
        'level' => 1,
        'required' => true,
        'sort_order' => 10,
        'description' => 'Scegli due caratteristiche differenti.',
    ]);

    //Inserisce prima l'opzione con ordine maggiore
    $dexterityOption = $choice->options()->create([
        'key' => 'dexterity',
        'option_type' => 'ability',
        'option_id' => $dexterity->id,
        'value' => 1,
        'quantity' => 1,
        'sort_order' => 20,
    ]);

    //Inserisce dopo l'opzione con ordine minore
    $choice->options()->create([
        'key' => 'strength',
        'option_type' => 'ability',
        'option_id' => $strength->id,
        'value' => 1,
        'quantity' => 1,
        'sort_order' => 10,
    ]);

    //Ricarica le opzioni e le caratteristiche collegate
    $choice->load('options.ability');

    //Verifica la relazione molti-a-uno con la razza
    expect($choice->race->is($halfElf))->toBeTrue();

    //Verifica la relazione inversa dalla razza
    expect(
        $halfElf->choices()
            ->whereKey($choice->id)
            ->exists()
    )->toBeTrue();

    //Verifica i valori principali della scelta
    expect($choice->choose)->toBe(2)
        ->and($choice->level)->toBe(1)
        ->and($choice->required)->toBeTrue();

    //Verifica quantità e ordinamento delle opzioni
    expect($choice->options)->toHaveCount(2)
        ->and($choice->options->pluck('key')->all())
        ->toBe([
            'strength',
            'dexterity',
        ])
        ->and($choice->options->pluck('value')->all())
        ->toBe([
            1,
            1,
        ]);

    //Verifica il collegamento dell'opzione alla caratteristica
    expect($dexterityOption->ability->is($dexterity))
        ->toBeTrue();

    //Verifica la relazione inversa dalla caratteristica
    expect(
        $dexterity->raceChoiceOptions()
            ->whereKey($dexterityOption->id)
            ->exists()
    )->toBeTrue();
});

//Verifica le scelte definite da una sottorazza
it('gestisce le scelte di caratteristica di una sottorazza', function () {
    //Recupera l'Umano Variante
    $variantHuman = Subrace::query()
        ->where('key', 'variant_human')
        ->firstOrFail();

    //Recupera due caratteristiche utilizzate come opzioni
    $intelligence = Ability::query()
        ->where('short_name', 'INT')
        ->firstOrFail();

    $wisdom = Ability::query()
        ->where('short_name', 'SAG')
        ->firstOrFail();

    //Crea la scelta della sottorazza
    $choice = $variantHuman->choices()->create([
        'key' => 'variant_ability_increases',
        'name' => 'Incrementi di caratteristica',
        'choice_type' => 'ability',
        'choose' => 2,
        'level' => 1,
        'required' => true,
        'sort_order' => 10,
        'description' => 'Scegli due caratteristiche differenti.',
    ]);

    //Crea l'opzione di Intelligenza
    $intelligenceOption = $choice->options()->create([
        'key' => 'intelligence',
        'option_type' => 'ability',
        'option_id' => $intelligence->id,
        'value' => 1,
        'sort_order' => 10,
    ]);

    //Crea l'opzione di Saggezza
    $choice->options()->create([
        'key' => 'wisdom',
        'option_type' => 'ability',
        'option_id' => $wisdom->id,
        'value' => 1,
        'sort_order' => 20,
    ]);

    //Ricarica le opzioni nel loro ordine
    $choice->load('options');

    //Verifica la relazione con la sottorazza
    expect($choice->subrace->is($variantHuman))->toBeTrue();

    //Verifica la relazione inversa dalla sottorazza
    expect(
        $variantHuman->choices()
            ->whereKey($choice->id)
            ->exists()
    )->toBeTrue();

    //Verifica i dati e le opzioni della scelta
    expect($choice->choose)->toBe(2)
        ->and($choice->required)->toBeTrue()
        ->and($choice->options->pluck('key')->all())
        ->toBe([
            'intelligence',
            'wisdom',
        ]);

    //Verifica la relazione inversa dalla caratteristica
    expect(
        $intelligence->subraceChoiceOptions()
            ->whereKey($intelligenceOption->id)
            ->exists()
    )->toBeTrue();
});

//Verifica le validazioni applicative delle scelte
it('rifiuta scelte e opzioni non valide', function () {
    //Recupera il Mezzelfo
    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    //Recupera una caratteristica di riferimento
    $strength = Ability::query()
        ->where('short_name', 'FOR')
        ->firstOrFail();

    //Verifica che non sia possibile scegliere zero opzioni
    expect(
        fn () => $halfElf->choices()->create([
            'key' => 'invalid_quantity',
            'name' => 'Scelta non valida',
            'choice_type' => 'ability',
            'choose' => 0,
            'level' => 1,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che il tipo della scelta debba essere supportato
    expect(
        fn () => $halfElf->choices()->create([
            'key' => 'invalid_type',
            'name' => 'Scelta non valida',
            'choice_type' => 'unknown',
            'choose' => 1,
            'level' => 1,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Crea una scelta valida utilizzata nei controlli successivi
    $choice = $halfElf->choices()->create([
        'key' => 'valid_choice',
        'name' => 'Scelta valida',
        'choice_type' => 'ability',
        'choose' => 1,
        'level' => 1,
    ]);

    //Verifica che un'opzione normale richieda un elemento
    expect(
        fn () => $choice->options()->create([
            'key' => 'missing_ability',
            'option_type' => 'ability',
            'option_id' => null,
            'value' => 1,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che scelta e opzione abbiano lo stesso tipo
    expect(
        fn () => $choice->options()->create([
            'key' => 'wrong_type',
            'option_type' => 'skill',
            'option_id' => $strength->id,
            'value' => 1,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che la quantità dell'opzione sia positiva
    expect(
        fn () => $choice->options()->create([
            'key' => 'invalid_option_quantity',
            'option_type' => 'ability',
            'option_id' => $strength->id,
            'value' => 1,
            'quantity' => 0,
        ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica i vincoli univoci delle chiavi
it('rifiuta chiavi duplicate nelle scelte e nelle opzioni', function () {
    //Recupera il Mezzelfo
    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    //Recupera una caratteristica di riferimento
    $strength = Ability::query()
        ->where('short_name', 'FOR')
        ->firstOrFail();

    //Crea una scelta di riferimento
    $choice = $halfElf->choices()->create([
        'key' => 'ability_increases',
        'name' => 'Incrementi di caratteristica',
        'choice_type' => 'ability',
        'choose' => 2,
        'level' => 1,
    ]);

    //Crea un'opzione di riferimento
    $choice->options()->create([
        'key' => 'strength',
        'option_type' => 'ability',
        'option_id' => $strength->id,
        'value' => 1,
    ]);

    //Verifica che la stessa scelta non possa essere duplicata
    expect(
        fn () => $halfElf->choices()->create([
            'key' => 'ability_increases',
            'name' => 'Seconda scelta',
            'choice_type' => 'ability',
            'choose' => 1,
            'level' => 1,
        ])
    )->toThrow(QueryException::class);

    //Verifica che la stessa opzione non possa essere duplicata
    expect(
        fn () => $choice->options()->create([
            'key' => 'strength',
            'option_type' => 'ability',
            'option_id' => $strength->id,
            'value' => 1,
        ])
    )->toThrow(QueryException::class);
});

//Verifica la cancellazione a cascata di scelte e opzioni
it('elimina scelte e opzioni insieme alla razza', function () {
    //Recupera il Mezzelfo
    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    //Recupera una caratteristica di riferimento
    $strength = Ability::query()
        ->where('short_name', 'FOR')
        ->firstOrFail();

    //Crea una scelta collegata alla razza
    $choice = $halfElf->choices()->create([
        'key' => 'ability_increases',
        'name' => 'Incrementi di caratteristica',
        'choice_type' => 'ability',
        'choose' => 1,
        'level' => 1,
    ]);

    //Crea un'opzione collegata alla scelta
    $option = $choice->options()->create([
        'key' => 'strength',
        'option_type' => 'ability',
        'option_id' => $strength->id,
        'value' => 1,
    ]);

    //Memorizza gli identificativi prima della cancellazione
    $choiceId = $choice->id;
    $optionId = $option->id;

    //Cancella la razza
    $halfElf->delete();

    //Verifica che la scelta sia stata eliminata
    expect(
        RaceChoice::query()
            ->whereKey($choiceId)
            ->exists()
    )->toBeFalse();

    //Verifica che anche l'opzione sia stata eliminata
    expect(
        RaceChoiceOption::query()
            ->whereKey($optionId)
            ->exists()
    )->toBeFalse();

    //Verifica che le tabelle delle sottorazze siano ancora utilizzabili
    expect(SubraceChoice::query()->count())->toBe(0)
        ->and(SubraceChoiceOption::query()->count())
        ->toBe(0);
});
