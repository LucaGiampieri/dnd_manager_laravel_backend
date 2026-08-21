<?php

use App\Models\Item;
use App\Models\ItemCost;
use App\Models\ItemType;
use Database\Seeders\ToolItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Ripete il seeder per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(ToolItemSeeder::class);
    $this->seed(ToolItemSeeder::class);
});

//Verifica la creazione di tutti gli strumenti
it('crea tutti gli strumenti del phb senza duplicati', function () {
    //Recupera le tipologie interessate
    $toolTypeIds = ItemType::query()
        ->whereIn('key', [
            'artisan_tool',
            'gaming_set',
            'musical_instrument',
            'kit',
            'other_tool',
        ])
        ->pluck('id');

    //Conta gli strumenti PHB creati
    expect(
        Item::query()
            ->where('version_key', 'phb_2014')
            ->whereIn('item_type_id', $toolTypeIds)
            ->count()
    )
        ->toBe(37)
        ->and(ItemCost::query()->count())
        ->toBe(37);
});

//Verifica la suddivisione nelle tipologie
it('assegna gli strumenti alle tipologie corrette', function () {
    //Definisce il numero previsto per ogni tipologia
    $expectedCounts = [
        'artisan_tool' => 17,
        'gaming_set' => 4,
        'musical_instrument' => 10,
        'kit' => 4,
        'other_tool' => 2,
    ];

    //Controlla ogni tipologia
    foreach ($expectedCounts as $typeKey => $expectedCount) {
        $itemType = ItemType::query()
            ->where('key', $typeKey)
            ->firstOrFail();

        expect(
            Item::query()
                ->where('version_key', 'phb_2014')
                ->where('item_type_id', $itemType->id)
                ->count()
        )->toBe($expectedCount);
    }
});

//Verifica alcuni strumenti da artigiano
it('salva prezzi e pesi degli strumenti da artigiano', function () {
    //Recupera le scorte da alchimista
    $alchemistSupplies = Item::query()
        ->where('key', 'alchemists_supplies')
        ->with('costs.currency')
        ->firstOrFail();

    //Recupera gli utensili da cuoco
    $cooksUtensils = Item::query()
        ->where('key', 'cooks_utensils')
        ->with('costs.currency')
        ->firstOrFail();

    //Verifica le scorte da alchimista
    expect((int) $alchemistSupplies->costs->first()->amount)
        ->toBe(50)
        ->and(
            (int) $alchemistSupplies->costs
                ->first()
                ->currency
                ->value_in_copper_pieces
        )
        ->toBe(100)
        ->and((float) $alchemistSupplies->weight_kg)
        ->toBe(3.629);

    //Verifica gli utensili da cuoco
    expect((int) $cooksUtensils->costs->first()->amount)
        ->toBe(1)
        ->and((float) $cooksUtensils->weight_kg)
        ->toBe(3.629);
});

//Verifica set da gioco e strumenti musicali
it('salva set da gioco e strumenti musicali', function () {
    //Recupera il set di dadi
    $diceSet = Item::query()
        ->where('key', 'dice_set')
        ->with('costs.currency')
        ->firstOrFail();

    //Recupera la cornamusa
    $bagpipes = Item::query()
        ->where('key', 'bagpipes')
        ->with('costs.currency')
        ->firstOrFail();

    //Il set di dadi costa una moneta d'argento
    expect((int) $diceSet->costs->first()->amount)
        ->toBe(1)
        ->and(
            (int) $diceSet->costs
                ->first()
                ->currency
                ->value_in_copper_pieces
        )
        ->toBe(10)
        ->and($diceSet->weight_kg)
        ->toBeNull();

    //La cornamusa costa trenta monete d'oro
    expect((int) $bagpipes->costs->first()->amount)
        ->toBe(30)
        ->and((float) $bagpipes->weight_kg)
        ->toBe(2.722);
});

//Verifica kit e strumenti specializzati
it('salva kit e strumenti specializzati', function () {
    //Recupera il kit da erborista
    $herbalismKit = Item::query()
        ->where('key', 'herbalism_kit')
        ->with('itemType')
        ->firstOrFail();

    //Recupera gli arnesi da scasso
    $thievesTools = Item::query()
        ->where('key', 'thieves_tools')
        ->with([
            'itemType',
            'costs.currency',
        ])
        ->firstOrFail();

    //Verifica la tipologia del kit da erborista
    expect($herbalismKit->itemType->key)
        ->toBe('kit');

    //Verifica gli arnesi da scasso
    expect($thievesTools->itemType->key)
        ->toBe('other_tool')
        ->and((int) $thievesTools->costs->first()->amount)
        ->toBe(25)
        ->and((float) $thievesTools->weight_kg)
        ->toBe(0.454);
});

//Verifica i campi di versione
it('assegna la versione phb 2014 a tutti gli strumenti', function () {
    //Recupera tutte le definizioni attese
    $definitions = require database_path(
        'data/phb_2014_tools.php'
    );

    $expectedKeys = collect($definitions)
        ->pluck('key')
        ->sort()
        ->values()
        ->all();

    //Recupera gli strumenti realmente creati
    $actualKeys = Item::query()
        ->where('version_key', 'phb_2014')
        ->whereIn('key', $expectedKeys)
        ->pluck('key')
        ->sort()
        ->values()
        ->all();

    //Verifica chiavi e versione
    expect($actualKeys)
        ->toBe($expectedKeys)
        ->and(
            Item::query()
                ->whereIn('key', $expectedKeys)
                ->where('is_legacy', true)
                ->count()
        )
        ->toBe(0);
});
