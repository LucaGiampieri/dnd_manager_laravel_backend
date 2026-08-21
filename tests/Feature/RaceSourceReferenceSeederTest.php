<?php

use App\Models\Race;
use App\Models\SourceBook;
use App\Models\SourceReference;
use App\Models\Subrace;
use Database\Seeders\RaceSourceReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica i riferimenti bibliografici di razze e sottorazze
it('collega le razze del phb alle pagine senza duplicati', function () {
    //Esegue due volte il seeder per verificarne l'idempotenza
    $this->seed(RaceSourceReferenceSeeder::class);
    $this->seed(RaceSourceReferenceSeeder::class);

    //Recupera il Manuale del Giocatore italiano
    $playerHandbook = SourceBook::query()
        ->where('slug', 'phb-2014')
        ->firstOrFail();

    //Verifica il numero complessivo dei riferimenti:
    //nove razze e dieci sottorazze
    expect(SourceReference::query()->count())->toBe(19);

    //Verifica che tutti i riferimenti appartengano al PHB
    expect(
        SourceReference::query()
            ->where('source_book_id', $playerHandbook->id)
            ->count()
    )->toBe(19);

    //Verifica che il manuale definisca tutti i contenuti
    expect(
        SourceReference::query()
            ->where('reference_type', 'definition')
            ->count()
    )->toBe(19);

    //Verifica che tutti siano fonti principali
    expect(
        SourceReference::query()
            ->where('is_primary', true)
            ->count()
    )->toBe(19);

    //Verifica che il testo ufficiale non sia stato inserito
    expect(
        SourceReference::query()
            ->whereNotNull('official_text')
            ->count()
    )->toBe(0);

    //Verifica che ogni razza possieda un riferimento
    expect(
        Race::query()
            ->withCount('sourceReferences')
            ->get()
            ->sum('source_references_count')
    )->toBe(9);

    //Verifica che ogni sottorazza possieda un riferimento
    expect(
        Subrace::query()
            ->withCount('sourceReferences')
            ->get()
            ->sum('source_references_count')
    )->toBe(10);

    //Recupera l'Elfo e il suo riferimento
    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    $elfReference = $elf->sourceReferences()
        ->where('key', 'phb_2014_it_primary_rules')
        ->firstOrFail();

    //Verifica le pagine italiane dell'Elfo
    expect($elfReference->page_start)->toBe(18)
        ->and($elfReference->page_end)->toBe(22)
        ->and($elfReference->sourceBook->is($playerHandbook))
        ->toBeTrue();

    //Recupera il Nano e il suo riferimento
    $dwarf = Race::query()
        ->where('key', 'dwarf')
        ->firstOrFail();

    $dwarfReference = $dwarf->sourceReferences()
        ->where('key', 'phb_2014_it_primary_rules')
        ->firstOrFail();

    //Verifica le pagine italiane del Nano
    expect($dwarfReference->page_start)->toBe(26)
        ->and($dwarfReference->page_end)->toBe(28);

    //Recupera la variante dell'Umano
    $variantHuman = Subrace::query()
        ->where('key', 'variant_human')
        ->firstOrFail();

    $variantHumanReference = $variantHuman
        ->sourceReferences()
        ->where('key', 'phb_2014_it_primary_rules')
        ->firstOrFail();

    //Verifica la pagina dei tratti umani alternativi
    expect($variantHumanReference->page_start)->toBe(31)
        ->and($variantHumanReference->page_end)->toBe(31)
        ->and($variantHumanReference->section)
        ->toBe(
            'Capitolo 2: Razze - Tratti Umani Alternativi'
        );

    //Verifica che la chiave condivisa sia presente
    //una volta per ciascun contenuto
    expect(
        SourceReference::query()
            ->where(
                'key',
                'phb_2014_it_primary_rules'
            )
            ->count()
    )->toBe(19);
});
