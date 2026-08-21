<?php

use App\Models\Race;
use App\Models\SourceBook;
use App\Models\SourceReference;
use App\Models\Subrace;
use Database\Seeders\ElementalEvilRaceSourceReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce razze e riferimenti EEPC prima di ogni verifica
beforeEach(function () {
    $this->seed(
        ElementalEvilRaceSourceReferenceSeeder::class
    );
});

//Verifica quantità, manuale e idempotenza
it('crea i riferimenti alle razze del male elementale senza duplicati', function () {
    //Esegue nuovamente il seeder
    $this->seed(
        ElementalEvilRaceSourceReferenceSeeder::class
    );

    //Recupera il manuale italiano EEPC
    $sourceBook = SourceBook::query()
        ->where('slug', 'eepc-2015')
        ->firstOrFail();

    //Recupera i riferimenti creati dal seeder
    $references = SourceReference::query()
        ->where('source_book_id', $sourceBook->id)
        ->where(
            'key',
            'eepc_2015_it_primary_rules'
        )
        ->get();

    //Tre razze e cinque sottorazze producono otto riferimenti
    expect($references)->toHaveCount(8)
        ->and(
            $references
                ->where('reference_type', 'definition')
                ->count()
        )
        ->toBe(8)
        ->and(
            $references
                ->where('is_primary', true)
                ->count()
        )
        ->toBe(8)
        ->and(
            $references
                ->where('sort_order', 10)
                ->count()
        )
        ->toBe(8);

    //Verifica che tutti i riferimenti appartengano
    //soltanto a razze o sottorazze
    expect(
        $references
            ->pluck('sourceable_type')
            ->unique()
            ->sort()
            ->values()
            ->all()
    )->toBe([
        Race::class,
        Subrace::class,
    ]);
});

//Verifica le pagine delle razze principali
it('assegna le pagine corrette alle razze principali', function () {
    //Definisce gli intervalli verificati nel manuale italiano
    $expectedReferences = [
        'aarakocra_eepc_2015' => [
            'page_start' => 3,
            'page_end' => 5,
            'section' => 'Aarakocra',
        ],
        'genasi_eepc_2015' => [
            'page_start' => 5,
            'page_end' => 8,
            'section' => 'Genasi',
        ],
        'goliath_eepc_2015' => [
            'page_start' => 10,
            'page_end' => 11,
            'section' => 'Goliath',
        ],
    ];

    //Verifica separatamente ogni razza
    foreach (
        $expectedReferences as $raceKey => $expected
    ) {
        $race = Race::query()
            ->where('key', $raceKey)
            ->firstOrFail();

        $reference = $race
            ->sourceReferences()
            ->where(
                'key',
                'eepc_2015_it_primary_rules'
            )
            ->firstOrFail();

        expect($reference->page_start)
            ->toBe($expected['page_start'])
            ->and($reference->page_end)
            ->toBe($expected['page_end'])
            ->and($reference->section)
            ->toContain($expected['section'])
            ->and($reference->sourceBook->slug)
            ->toBe('eepc-2015');
    }
});

//Verifica le pagine delle sottorazze
it('assegna le pagine corrette alle sottorazze', function () {
    //Definisce gli intervalli verificati nel manuale italiano
    $expectedReferences = [
        'water_genasi_eepc_2015' => [
            'page_start' => 6,
            'page_end' => 7,
            'section' => 'Genasi dell’Acqua',
        ],
        'air_genasi_eepc_2015' => [
            'page_start' => 7,
            'page_end' => 7,
            'section' => 'Genasi dell’Aria',
        ],
        'fire_genasi_eepc_2015' => [
            'page_start' => 7,
            'page_end' => 7,
            'section' => 'Genasi del Fuoco',
        ],
        'earth_genasi_eepc_2015' => [
            'page_start' => 7,
            'page_end' => 8,
            'section' => 'Genasi della Terra',
        ],
        'deep_gnome_eepc_2015' => [
            'page_start' => 8,
            'page_end' => 10,
            'section' => 'Gnomo delle Profondità',
        ],
    ];

    //Verifica separatamente ogni sottorazza
    foreach (
        $expectedReferences as $subraceKey => $expected
    ) {
        $subrace = Subrace::query()
            ->where('key', $subraceKey)
            ->firstOrFail();

        $reference = $subrace
            ->sourceReferences()
            ->where(
                'key',
                'eepc_2015_it_primary_rules'
            )
            ->firstOrFail();

        expect($reference->page_start)
            ->toBe($expected['page_start'])
            ->and($reference->page_end)
            ->toBe($expected['page_end'])
            ->and($reference->section)
            ->toContain($expected['section'])
            ->and($reference->sourceBook->slug)
            ->toBe('eepc-2015');
    }
});

//Verifica che il testo ufficiale non venga esposto
it('mantiene privato il testo ufficiale dei riferimenti', function () {
    //Recupera tutti i riferimenti EEPC
    $references = SourceReference::query()
        ->where(
            'key',
            'eepc_2015_it_primary_rules'
        )
        ->get();

    expect($references)->toHaveCount(8);

    foreach ($references as $reference) {
        //Il testo integrale non viene salvato
        expect($reference->official_text)
            ->toBeNull();

        //Il campo rimane comunque nascosto
        //nelle conversioni pubbliche del modello
        expect(
            array_key_exists(
                'official_text',
                $reference->toArray()
            )
        )->toBeFalse();

        //Le note contengono soltanto informazioni bibliografiche
        expect($reference->notes)
            ->toContain(
                'Compendio del Giocatore '
                . 'del Male Elementale'
            );
    }
});
