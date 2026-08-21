<?php

use App\Models\CreatureType;
use App\Models\Race;
use App\Models\Ruleset;
use App\Models\Subrace;
use Database\Seeders\RaceSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica il versionamento delle razze del Manuale del Giocatore
it('assegna la versione phb 2014 alle razze esistenti', function () {
    //Crea le razze e tutti i cataloghi richiesti
    $this->seed(RaceSeeder::class);

    //Recupera le razze e le sottorazze create dal seeder
    $races = Race::query()->get();
    $subraces = Subrace::query()->get();

    //Verifica il numero e il versionamento delle razze
    expect($races)->toHaveCount(9)
        ->and($races->where('version_key', 'phb_2014'))
        ->toHaveCount(9)
        ->and($races->where('is_legacy', true))
        ->toHaveCount(0);

    //Verifica che ogni razza possieda la chiave canonica
    foreach ($races as $race) {
        expect($race->canonical_key)->toBe($race->key)
            ->and($race->is_legacy)->toBeFalse();
    }

    //Verifica il numero e il versionamento delle sottorazze
    expect($subraces)->toHaveCount(10)
        ->and($subraces->where('version_key', 'phb_2014'))
        ->toHaveCount(10)
        ->and($subraces->where('is_legacy', true))
        ->toHaveCount(0);

    //Verifica che ogni sottorazza possieda la chiave canonica
    foreach ($subraces as $subrace) {
        expect($subrace->canonical_key)->toBe($subrace->key)
            ->and($subrace->is_legacy)->toBeFalse();
    }
});

//Verifica che possano esistere più versioni della stessa razza
it('permette versioni diverse della stessa razza', function () {
    //Crea le razze di base
    $this->seed(RaceSeeder::class);

    //Recupera il regolamento utilizzato dal test
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera il tipo di creatura Umanoide
    $humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();

    //Crea una vecchia versione dimostrativa dell'Elfo
    $legacyElf = $ruleset->races()->create([
        'key' => 'elf_legacy_test',
        'canonical_key' => 'elf',
        'version_key' => 'legacy_test',
        'is_legacy' => true,
        'name' => 'Elfo legacy di test',
        'creature_type_id' => $humanoid->id,
        'sort_order' => 100,
    ]);

    //Crea una revisione dimostrativa della stessa razza
    $revisedElf = $ruleset->races()->create([
        'key' => 'elf_revision_test',
        'canonical_key' => 'elf',
        'version_key' => 'revision_test',
        'is_legacy' => false,
        'name' => 'Elfo revisionato di test',
        'creature_type_id' => $humanoid->id,
        'sort_order' => 101,
    ]);

    //Verifica che entrambe le versioni siano state salvate
    expect($legacyElf->canonical_key)->toBe('elf')
        ->and($legacyElf->is_legacy)->toBeTrue()
        ->and($revisedElf->canonical_key)->toBe('elf')
        ->and($revisedElf->is_legacy)->toBeFalse()
        ->and(
            Race::query()
                ->where('ruleset_id', $ruleset->id)
                ->where('canonical_key', 'elf')
                ->count()
        )->toBe(3);

    //Verifica che la stessa versione non possa essere duplicata
    expect(
        fn () => $ruleset->races()->create([
            'key' => 'another_elf_revision_test',
            'canonical_key' => 'elf',
            'version_key' => 'revision_test',
            'is_legacy' => false,
            'name' => 'Secondo Elfo revisionato di test',
            'creature_type_id' => $humanoid->id,
            'sort_order' => 102,
        ])
    )->toThrow(QueryException::class);
});

//Verifica il versionamento delle sottorazze
it('permette versioni diverse della stessa sottorazza', function () {
    //Crea le razze e le sottorazze di base
    $this->seed(RaceSeeder::class);

    //Recupera la razza Elfo
    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    //Crea una versione legacy dimostrativa
    $legacyHighElf = $elf->subraces()->create([
        'key' => 'high_elf_legacy_test',
        'canonical_key' => 'high_elf',
        'version_key' => 'legacy_test',
        'is_legacy' => true,
        'name' => 'Elfo Alto legacy di test',
        'sort_order' => 100,
    ]);

    //Crea una revisione dimostrativa
    $revisedHighElf = $elf->subraces()->create([
        'key' => 'high_elf_revision_test',
        'canonical_key' => 'high_elf',
        'version_key' => 'revision_test',
        'is_legacy' => false,
        'name' => 'Elfo Alto revisionato di test',
        'sort_order' => 101,
    ]);

    //Verifica che entrambe le versioni siano state salvate
    expect($legacyHighElf->is_legacy)->toBeTrue()
        ->and($revisedHighElf->is_legacy)->toBeFalse()
        ->and(
            $elf->subraces()
                ->where('canonical_key', 'high_elf')
                ->count()
        )->toBe(3);

    //Verifica che la stessa versione non possa essere duplicata
    expect(
        fn () => $elf->subraces()->create([
            'key' => 'another_high_elf_revision_test',
            'canonical_key' => 'high_elf',
            'version_key' => 'revision_test',
            'is_legacy' => false,
            'name' => 'Secondo Elfo Alto revisionato di test',
            'sort_order' => 102,
        ])
    )->toThrow(QueryException::class);
});
