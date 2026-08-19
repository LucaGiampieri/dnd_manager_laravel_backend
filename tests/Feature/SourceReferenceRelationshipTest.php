<?php

use App\Models\CreatureType;
use App\Models\SourceBook;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ripristina il database di test prima di ogni test
uses(RefreshDatabase::class);

it('collega un contenuto al manuale e alle sue pagine', function () {
    //Crea il regolamento, i manuali e i tipi di creatura
    $this->seed([
        RulesetSeeder::class,
        SourceBookSeeder::class,
        CreatureTypeSeeder::class,
    ]);

    //Recupera il Manuale dei Mostri
    $sourceBook = SourceBook::query()
        ->where('slug', 'mm-2014')
        ->firstOrFail();

    //Recupera il tipo di creatura Non Morto
    $creatureType = CreatureType::query()
        ->where('key', 'undead')
        ->firstOrFail();

    //Crea un riferimento bibliografico di prova
    $sourceReference = $creatureType
        ->sourceReferences()
        ->create([
            'source_book_id' => $sourceBook->id,
            'key' => 'mm_2014_it_definition',
            'reference_type' => 'definition',
            'page_start' => 10,
            'page_end' => 11,
            'section' => 'Tipi di creatura',
            'is_primary' => true,
            'sort_order' => 1,
            'official_text' => null,
            'notes' => 'Pagine utilizzate soltanto come esempio nel test.',
        ]);

    //Verifica il contenuto proprietario del riferimento
    expect(
        $sourceReference->sourceable->is($creatureType)
    )->toBeTrue();

    //Verifica il manuale collegato al riferimento
    expect(
        $sourceReference->sourceBook->is($sourceBook)
    )->toBeTrue();

    //Verifica la relazione dal contenuto verso i riferimenti
    expect(
        $creatureType->sourceReferences()->count()
    )->toBe(1);

    //Verifica la relazione dal manuale verso i riferimenti
    expect(
        $sourceBook->sourceReferences()->count()
    )->toBe(1);

    //Verifica le pagine e le proprietà principali
    expect($sourceReference->page_start)->toBe(10)
        ->and($sourceReference->page_end)->toBe(11)
        ->and($sourceReference->is_primary)->toBeTrue()
        ->and($sourceReference->official_text)->toBeNull();

    //Elimina il contenuto proprietario del riferimento
    $creatureType->delete();

    //Verifica che il riferimento orfano venga eliminato
    expect($sourceReference->fresh())->toBeNull();
});

it('permette più riferimenti dello stesso tipo con chiavi diverse', function () {
    //Crea i dati necessari alle relazioni
    $this->seed([
        RulesetSeeder::class,
        SourceBookSeeder::class,
        CreatureTypeSeeder::class,
    ]);

    //Recupera il manuale utilizzato nel test
    $sourceBook = SourceBook::query()
        ->where('slug', 'mm-2014')
        ->firstOrFail();

    //Recupera il contenuto a cui collegare le fonti
    $creatureType = CreatureType::query()
        ->where('key', 'undead')
        ->firstOrFail();

    //Crea il primo riferimento di tipo definizione
    $creatureType->sourceReferences()->create([
        'source_book_id' => $sourceBook->id,
        'key' => 'mm_2014_it_definition_primary',
        'reference_type' => 'definition',
        'page_start' => 10,
        'page_end' => 11,
        'section' => 'Definizione principale',
        'is_primary' => true,
        'sort_order' => 1,
    ]);

    //Crea un secondo riferimento dello stesso tipo e manuale
    $creatureType->sourceReferences()->create([
        'source_book_id' => $sourceBook->id,
        'key' => 'mm_2014_it_definition_additional',
        'reference_type' => 'definition',
        'page_start' => 20,
        'page_end' => 21,
        'section' => 'Definizione aggiuntiva',
        'is_primary' => false,
        'sort_order' => 2,
    ]);

    //Verifica che entrambi i riferimenti siano stati salvati
    expect(
        $creatureType->sourceReferences()->count()
    )->toBe(2);

    //Verifica che la stessa chiave non possa essere riutilizzata
    expect(
        fn () => $creatureType->sourceReferences()->create([
            'source_book_id' => $sourceBook->id,
            'key' => 'mm_2014_it_definition_primary',
            'reference_type' => 'reference',
            'page_start' => 30,
            'is_primary' => false,
            'sort_order' => 3,
        ])
    )->toThrow(QueryException::class);
});

it('mantiene privato il testo ufficiale nelle conversioni pubbliche', function () {
    //Crea i dati necessari al riferimento
    $this->seed([
        RulesetSeeder::class,
        SourceBookSeeder::class,
        CreatureTypeSeeder::class,
    ]);

    //Recupera il manuale e il contenuto utilizzati nel test
    $sourceBook = SourceBook::query()
        ->where('slug', 'mm-2014')
        ->firstOrFail();

    $creatureType = CreatureType::query()
        ->where('key', 'undead')
        ->firstOrFail();

    //Crea un riferimento con un testo privato di esempio
    $sourceReference = $creatureType
        ->sourceReferences()
        ->create([
            'source_book_id' => $sourceBook->id,
            'key' => 'mm_2014_it_private_text',
            'reference_type' => 'definition',
            'page_start' => 10,
            'is_primary' => true,
            'sort_order' => 1,
            'official_text' => 'Testo privato utilizzato soltanto nel test.',
        ]);

    //Verifica che Laravel possa usare internamente il valore
    expect($sourceReference->official_text)
        ->toBe('Testo privato utilizzato soltanto nel test.');

    //Verifica che il valore non venga esportato pubblicamente
    expect(
        array_key_exists(
            'official_text',
            $sourceReference->toArray()
        )
    )->toBeFalse();
});
