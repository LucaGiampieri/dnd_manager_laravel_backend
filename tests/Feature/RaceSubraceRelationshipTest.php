<?php

use App\Models\CreatureType;
use App\Models\Race;
use App\Models\Ruleset;
use App\Models\Subrace;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara i cataloghi richiesti prima di ogni test
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea il regolamento e i tipi di creatura
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
    ]);
});

//Verifica le relazioni principali di razze e sottorazze
it('gestisce le relazioni principali di razze e sottorazze', function () {
    //Recupera il regolamento utilizzato dal test
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera il tipo di creatura utilizzato dalle razze giocabili
    $humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();

    //Crea una razza attraverso la relazione con il regolamento
    $elf = $ruleset->races()->create([
        'key' => 'elf',
        'name' => 'Elfo',
        'creature_type_id' => $humanoid->id,
        'is_lineage' => false,
        'can_replace_race' => false,
        'selectable' => true,
        'requires_dm_permission' => false,
        'description' => 'Razza creata soltanto per il test.',
        'typical_alignment' => 'Generalmente caotico.',
        'sort_order' => 2,
    ]);

    //Crea per prima la sottorazza con ordine maggiore
    $highElf = $elf->subraces()->create([
        'key' => 'high_elf',
        'name' => 'Elfo Alto',
        'typical_alignment' => 'Generalmente caotico buono.',
        'is_variant' => false,
        'selectable' => true,
        'requires_dm_permission' => false,
        'sort_order' => 20,
        'description' => 'Sottorazza creata soltanto per il test.',
    ]);

    //Crea per seconda la sottorazza con ordine minore
    $woodElf = $elf->subraces()->create([
        'key' => 'wood_elf',
        'name' => 'Elfo dei Boschi',
        'typical_alignment' => 'Generalmente caotico buono.',
        'is_variant' => false,
        'selectable' => true,
        'requires_dm_permission' => false,
        'sort_order' => 10,
        'description' => 'Sottorazza creata soltanto per il test.',
    ]);

    //Ricarica le relazioni utilizzate dalle verifiche
    $elf->load([
        'ruleset',
        'creatureType',
        'subraces',
    ]);

    //Verifica la relazione molti-a-uno con il regolamento
    expect($elf->ruleset->is($ruleset))->toBeTrue();

    //Verifica la relazione molti-a-uno con il tipo di creatura
    expect($elf->creatureType->is($humanoid))->toBeTrue();

    //Verifica la relazione inversa dal regolamento alla razza
    expect(
        $ruleset
            ->races()
            ->whereKey($elf->id)
            ->exists()
    )->toBeTrue();

    //Verifica la relazione inversa dal tipo di creatura alla razza
    expect(
        $humanoid
            ->races()
            ->whereKey($elf->id)
            ->exists()
    )->toBeTrue();

    //Verifica quantità e ordinamento delle sottorazze
    expect($elf->subraces)->toHaveCount(2)
        ->and($elf->subraces->pluck('key')->all())
        ->toBe([
            'wood_elf',
            'high_elf',
        ]);

    //Verifica la relazione molti-a-uno dalla sottorazza alla razza
    expect($highElf->race->is($elf))->toBeTrue()
        ->and($woodElf->race->is($elf))->toBeTrue();

    //Verifica le conversioni automatiche dei valori booleani
    expect($elf->selectable)->toBeTrue()
        ->and($elf->is_lineage)->toBeFalse()
        ->and($highElf->is_variant)->toBeFalse();
});

//Verifica i vincoli univoci delle chiavi tecniche
it('rifiuta chiavi duplicate nello stesso contenitore', function () {
    //Recupera il regolamento utilizzato dal test
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera il tipo di creatura utilizzato dalla razza
    $humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();

    //Crea una razza di riferimento
    $elf = $ruleset->races()->create([
        'key' => 'elf',
        'name' => 'Elfo',
        'creature_type_id' => $humanoid->id,
        'sort_order' => 1,
    ]);

    //Verifica che lo stesso regolamento non accetti due razze
    //con la stessa chiave tecnica
    expect(
        fn () => $ruleset->races()->create([
            'key' => 'elf',
            'name' => 'Secondo Elfo',
            'creature_type_id' => $humanoid->id,
            'sort_order' => 2,
        ])
    )->toThrow(QueryException::class);

    //Crea una sottorazza di riferimento
    $elf->subraces()->create([
        'key' => 'high_elf',
        'name' => 'Elfo Alto',
        'sort_order' => 1,
    ]);

    //Verifica che la stessa razza non accetti due sottorazze
    //con la stessa chiave tecnica
    expect(
        fn () => $elf->subraces()->create([
            'key' => 'high_elf',
            'name' => 'Secondo Elfo Alto',
            'sort_order' => 2,
        ])
    )->toThrow(QueryException::class);
});

//Verifica la cancellazione a cascata delle sottorazze
it('elimina le sottorazze quando viene cancellata la razza', function () {
    //Recupera il regolamento utilizzato dal test
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera il tipo di creatura utilizzato dalla razza
    $humanoid = CreatureType::query()
        ->where('key', 'humanoid')
        ->firstOrFail();

    //Crea una razza di riferimento
    $elf = $ruleset->races()->create([
        'key' => 'elf',
        'name' => 'Elfo',
        'creature_type_id' => $humanoid->id,
        'sort_order' => 1,
    ]);

    //Crea una sottorazza collegata
    $subrace = $elf->subraces()->create([
        'key' => 'high_elf',
        'name' => 'Elfo Alto',
        'sort_order' => 1,
    ]);

    //Memorizza l'identificativo prima della cancellazione
    $subraceId = $subrace->id;

    //Cancella la razza principale
    $elf->delete();

    //Verifica che il database abbia eliminato anche la sottorazza
    expect(
        Subrace::query()
            ->whereKey($subraceId)
            ->exists()
    )->toBeFalse();

    //Verifica che non siano rimaste razze create dal test
    expect(Race::query()->count())->toBe(0);
});
