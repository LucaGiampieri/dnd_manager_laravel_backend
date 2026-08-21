<?php

use App\Models\Item;
use App\Models\ToolProficiency;
use App\Models\ToolProficiencyItem;
use Database\Seeders\ToolProficiencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Ripete il seeder per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(ToolProficiencySeeder::class);
    $this->seed(ToolProficiencySeeder::class);
});

//Verifica la creazione di tutte le competenze
it('crea tutte le competenze negli strumenti senza duplicati', function () {
    //Sono previste 37 competenze specifiche e due categorie
    expect(ToolProficiency::query()->count())
        ->toBe(39)
        ->and(
            ToolProficiency::query()
                ->where('type', 'specific')
                ->count()
        )
        ->toBe(37)
        ->and(
            ToolProficiency::query()
                ->where('type', 'category')
                ->count()
        )
        ->toBe(2);

    //Le categorie dei veicoli non possiedono ancora oggetti
    expect(ToolProficiencyItem::query()->count())
        ->toBe(0);
});

//Verifica che ogni strumento possieda una competenza
it('crea una competenza specifica per ogni strumento', function () {
    //Carica le definizioni ufficiali
    $definitions = require database_path(
        'data/phb_2014_tools.php'
    );

    //Costruisce le chiavi delle competenze attese
    $expectedKeys = collect($definitions)
        ->pluck('key')
        ->map(
            fn (string $key): string =>
                "tool_{$key}_phb_2014"
        )
        ->sort()
        ->values()
        ->all();

    //Recupera le competenze specifiche realmente create
    $actualKeys = ToolProficiency::query()
        ->where('type', 'specific')
        ->pluck('key')
        ->sort()
        ->values()
        ->all();

    //Verifica la corrispondenza completa
    expect($actualKeys)
        ->toBe($expectedKeys);
});

//Verifica il collegamento diretto agli strumenti
it('collega le competenze ai rispettivi strumenti', function () {
    //Recupera gli arnesi da scasso
    $thievesTools = Item::query()
        ->where('key', 'thieves_tools')
        ->where('version_key', 'phb_2014')
        ->firstOrFail();

    //Recupera la relativa competenza
    $proficiency = ToolProficiency::query()
        ->where('key', 'tool_thieves_tools_phb_2014')
        ->firstOrFail();

    //Verifica il collegamento all'oggetto
    expect($proficiency->type)
        ->toBe('specific')
        ->and($proficiency->item_id)
        ->toBe($thievesTools->id)
        ->and($proficiency->item->is($thievesTools))
        ->toBeTrue()
        ->and($proficiency->items()->count())
        ->toBe(0);
});

//Verifica l'unicità degli oggetti collegati
it('non assegna lo stesso strumento a competenze duplicate', function () {
    //Recupera tutti gli item_id delle competenze specifiche
    $itemIds = ToolProficiency::query()
        ->where('type', 'specific')
        ->pluck('item_id');

    //Ogni competenza deve riferirsi a un oggetto differente
    expect($itemIds->count())
        ->toBe(37)
        ->and($itemIds->filter()->count())
        ->toBe(37)
        ->and($itemIds->unique()->count())
        ->toBe(37);
});

//Verifica le competenze nei veicoli
it('crea le competenze nei veicoli terrestri e acquatici', function () {
    //Recupera le due competenze ufficiali
    $landVehicles = ToolProficiency::query()
        ->where('key', 'land_vehicles_phb_2014')
        ->firstOrFail();

    $waterVehicles = ToolProficiency::query()
        ->where('key', 'water_vehicles_phb_2014')
        ->firstOrFail();

    //Le competenze nei veicoli sono categorie
    expect($landVehicles->type)
        ->toBe('category')
        ->and($landVehicles->item_id)
        ->toBeNull()
        ->and($landVehicles->items()->count())
        ->toBe(0)
        ->and($waterVehicles->type)
        ->toBe('category')
        ->and($waterVehicles->item_id)
        ->toBeNull()
        ->and($waterVehicles->items()->count())
        ->toBe(0);
});
