<?php

use App\Models\CreatureType;
use App\Models\SourceBook;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ripristina il database di test prima di ogni test
uses(RefreshDatabase::class);

//Verifica il collegamento tra contenuto, manuale e pagine
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

    //Verifica la pagina iniziale
    expect($sourceReference->page_start)->toBe(10);

    //Verifica la pagina finale
    expect($sourceReference->page_end)->toBe(11);

    //Verifica che il riferimento sia considerato principale
    expect($sourceReference->is_primary)->toBeTrue();

    //Verifica che il testo ufficiale non venga salvato nel test
    expect($sourceReference->official_text)->toBeNull();

    //Elimina il contenuto proprietario del riferimento
    $creatureType->delete();

    //Verifica che il riferimento orfano venga eliminato automaticamente
    expect($sourceReference->fresh())->toBeNull();
});
