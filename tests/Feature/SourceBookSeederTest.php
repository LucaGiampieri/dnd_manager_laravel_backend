<?php

use App\Models\Ruleset;
use App\Models\SourceBook;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione del catalogo principale dei manuali
it('crea i principali manuali senza duplicati', function () {
    /** @var \Tests\TestCase $this */

    //Esegue due volte i seeder per verificarne l'idempotenza
    $this->seed(RulesetSeeder::class);
    $this->seed(SourceBookSeeder::class);

    $this->seed(RulesetSeeder::class);
    $this->seed(SourceBookSeeder::class);

    //Recupera il regolamento utilizzato dai manuali
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera i manuali nel loro ordine di inserimento
    $sourceBooks = SourceBook::query()
        ->orderBy('id')
        ->get();

    //Verifica che il regolamento non sia stato duplicato
    expect(
        Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->count()
    )->toBe(1);

    //Verifica il numero complessivo delle fonti
    expect($sourceBooks)->toHaveCount(22);

    //Verifica le chiavi tecniche e il loro ordine
    expect($sourceBooks->pluck('slug')->all())->toBe([
        'phb-2014',
        'dmg-2014',
        'mm-2014',
        'scag-2015',
        'vgm-2016',
        'xgte-2017',
        'mtf-2018',
        'tcoe-2020',
        'vrgtr-2021',
        'ftod-2021',
        'mpmm-2022',
        'bgg-2023',
        'bmt-2023',
        'erlw-2019',
        'egtw-2020',
        'ggtr-2018',
        'moot-2020',
        'scc-2021',
        'sais-2022',
        'paitm-2023',
        'dsotdq-2022',
        'eepc-2015',
    ]);

    //Verifica la quantità dei manuali fondamentali
    expect(
        $sourceBooks
            ->where('type', 'core_rulebook')
            ->count()
    )->toBe(3);

    //Verifica la quantità dei supplementi generali
    expect(
        $sourceBooks
            ->where('type', 'supplement')
            ->count()
    )->toBe(8);

    //Verifica la quantità dei manuali di ambientazione
    expect(
        $sourceBooks
            ->where('type', 'setting')
            ->count()
    )->toBe(9);

    //Verifica la presenza della fonte di Tasha
    $tashasCauldron = $sourceBooks->firstWhere(
        'slug',
        'tcoe-2020'
    );

    expect($tashasCauldron)->not->toBeNull()
        ->and($tashasCauldron->abbreviation)->toBe('TCoE')
        ->and($tashasCauldron->type)->toBe('supplement')
        ->and($tashasCauldron->is_official)->toBeTrue()
        ->and($tashasCauldron->notes)
        ->toContain('personalizzazione dell’origine');

    //Verifica la relazione molti-a-uno (BelongsTo):
    //il manuale di Tasha appartiene al regolamento corretto
    expect($tashasCauldron->ruleset->is($ruleset))
        ->toBeTrue();

    //Verifica la relazione inversa uno-a-molti (HasMany):
    //il regolamento contiene tutte le fonti inserite
    expect($ruleset->sourceBooks()->count())->toBe(22);

    //Verifica che ogni fonte abbia una nota descrittiva
    expect(
        SourceBook::query()
            ->whereNull('notes')
            ->count()
    )->toBe(0);

    //Verifica che ogni fonte abbia un editore
    expect(
        SourceBook::query()
            ->whereNull('publisher')
            ->count()
    )->toBe(0);

    //Verifica che tutte le fonti siano ufficiali e attive
    expect(
        SourceBook::query()
            ->where('is_official', false)
            ->orWhere('is_active', false)
            ->count()
    )->toBe(0);

    //Verifica che siano conservate anche le fonti legacy
    expect($sourceBooks->pluck('slug')->all())
        ->toContain('vgm-2016')
        ->toContain('mtf-2018')
        ->toContain('mpmm-2022');
});
