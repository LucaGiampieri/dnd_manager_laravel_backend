<?php

use App\Models\SourceBook;
use App\Models\Subrace;
use Database\Seeders\SwordCoastRaceSourceReferenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione idempotente dei riferimenti SCAG
it('crea i riferimenti SCAG senza duplicati', function () {
    $this->seed(SwordCoastRaceSourceReferenceSeeder::class);
    $this->seed(SwordCoastRaceSourceReferenceSeeder::class);

    $sourceBook = SourceBook::query()
        ->where('slug', 'scag-2015')
        ->firstOrFail();

    $references = $sourceBook->sourceReferences()
        ->where('sourceable_type', Subrace::class)
        ->get();

    expect($references)->toHaveCount(3);
});

//Verifica le pagine delle nuove sottorazze
it('assegna le pagine corrette a Duergar e Halfling degli Spiriti', function () {
    $this->seed(SwordCoastRaceSourceReferenceSeeder::class);

    $duergar = Subrace::query()
        ->where('key', 'duergar_scag_2015')
        ->firstOrFail();

    $ghostwise = Subrace::query()
        ->where('key', 'ghostwise_halfling_scag_2015')
        ->firstOrFail();

    $duergarReference = $duergar->sourceReferences()
        ->where('key', 'scag_2015_it_primary_rules')
        ->firstOrFail();

    $ghostwiseReference = $ghostwise->sourceReferences()
        ->where('key', 'scag_2015_it_primary_rules')
        ->firstOrFail();

    expect($duergarReference->page_start)->toBe(109)
        ->and($duergarReference->reference_type)
        ->toBe('definition')
        ->and($duergarReference->is_primary)->toBeTrue()
        ->and($ghostwiseReference->page_start)->toBe(107)
        ->and($ghostwiseReference->reference_type)
        ->toBe('definition')
        ->and($ghostwiseReference->is_primary)->toBeTrue();
});

//Verifica che lo Svirfneblin sia trattato come ristampa
it('collega lo Svirfneblin allo SCAG come ristampa', function () {
    $this->seed(SwordCoastRaceSourceReferenceSeeder::class);

    $deepGnome = Subrace::query()
        ->where('key', 'deep_gnome_eepc_2015')
        ->firstOrFail();

    $reference = $deepGnome->sourceReferences()
        ->where('key', 'scag_2015_it_reprint')
        ->firstOrFail();

    expect($reference->page_start)->toBe(115)
        ->and($reference->page_end)->toBe(115)
        ->and($reference->reference_type)->toBe('reprint')
        ->and($reference->is_primary)->toBeFalse()
        ->and($reference->sourceBook->slug)->toBe('scag-2015');
});

//Verifica che il testo ufficiale non venga esposto
it('mantiene privato il testo ufficiale dei riferimenti SCAG', function () {
    $this->seed(SwordCoastRaceSourceReferenceSeeder::class);

    $duergar = Subrace::query()
        ->where('key', 'duergar_scag_2015')
        ->firstOrFail();

    $reference = $duergar->sourceReferences()
        ->where('key', 'scag_2015_it_primary_rules')
        ->firstOrFail();

    expect($reference->official_text)->toBeNull()
        ->and(array_key_exists(
            'official_text',
            $reference->toArray()
        ))->toBeFalse();
});
