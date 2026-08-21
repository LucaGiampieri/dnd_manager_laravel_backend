<?php

use App\Models\Race;
use App\Models\SourceBook;
use App\Models\Subrace;
use Database\Seeders\SwordCoastRaceVariantSourceReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione idempotente dei riferimenti
it('crea i riferimenti delle varianti SCAG senza duplicati', function () {
    $this->seed(
        SwordCoastRaceVariantSourceReferenceSeeder::class
    );

    $this->seed(
        SwordCoastRaceVariantSourceReferenceSeeder::class
    );

    $sourceBook = SourceBook::query()
        ->where('slug', 'scag-2015')
        ->firstOrFail();

    $references = $sourceBook->sourceReferences()
        ->whereIn('key', [
            'scag_2015_it_half_elf_variants',
            'scag_2015_it_tiefling_variants',
            'scag_2015_it_feral_tiefling',
        ])
        ->get();

    expect($references)->toHaveCount(3);
});

//Verifica le pagine del Mezzelfo e del Tiefling
it('assegna le pagine corrette alle varianti', function () {
    $this->seed(
        SwordCoastRaceVariantSourceReferenceSeeder::class
    );

    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    $tiefling = Race::query()
        ->where('key', 'tiefling')
        ->firstOrFail();

    $feralTiefling = Subrace::query()
        ->where('key', 'feral_tiefling_scag_2015')
        ->firstOrFail();

    expect(
        $halfElf->sourceReferences()
            ->where(
                'key',
                'scag_2015_it_half_elf_variants'
            )
            ->firstOrFail()
            ->page_start
    )->toBe(116)
        ->and(
            $tiefling->sourceReferences()
                ->where(
                    'key',
                    'scag_2015_it_tiefling_variants'
                )
                ->firstOrFail()
                ->page_start
        )->toBe(118)
        ->and(
            $feralTiefling->sourceReferences()
                ->where(
                    'key',
                    'scag_2015_it_feral_tiefling'
                )
                ->firstOrFail()
                ->page_start
        )->toBe(118);
});

//Verifica la privacy del testo ufficiale
it('mantiene privato il testo ufficiale delle varianti', function () {
    $this->seed(
        SwordCoastRaceVariantSourceReferenceSeeder::class
    );

    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    $reference = $halfElf->sourceReferences()
        ->where(
            'key',
            'scag_2015_it_half_elf_variants'
        )
        ->firstOrFail();

    expect($reference->official_text)->toBeNull()
        ->and(array_key_exists(
            'official_text',
            $reference->toArray()
        ))->toBeFalse();
});
