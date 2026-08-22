<?php

use App\Models\Currency;
use App\Models\Item;
use App\Models\ItemCost;
use App\Models\ItemType;
use App\Models\Ruleset;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara il regolamento e le valute richiesti dagli oggetti
beforeEach(function () {
    $this->seed([
        RulesetSeeder::class,
        CurrencySeeder::class,
    ]);

    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    $this->currency = Currency::query()
        ->orderBy('sort_order')
        ->firstOrFail();

    $this->itemType = ItemType::query()->create([
        'key' => 'weapon',
        'name' => 'Arma',
        'description' => 'Oggetti utilizzati come armi.',
        'sort_order' => 10,
    ]);
});

//Verifica le relazioni principali degli oggetti
it('gestisce tipologie prezzi e relazioni degli oggetti', function () {
    $item = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_weapon',
        'name' => 'Arma di prova',
        'item_type_id' => $this->itemType->id,
        'description' => 'Oggetto creato soltanto per il test.',
        'weight_kg' => '1.500',
        'is_stackable' => false,
        'rarity' => null,
        'is_magical' => false,
        'requires_attunement' => false,
        'requirements' => null,
        'notes' => null,
        'sort_order' => 10,
    ]);

    $cost = $item->costs()->create([
        'currency_id' => $this->currency->id,
        'amount' => 15,
        'notes' => 'Prezzo utilizzato soltanto nel test.',
    ]);

    $item->load([
        'ruleset',
        'itemType',
        'costs.currency',
    ]);

    expect($item->ruleset->is($this->ruleset))->toBeTrue()
        ->and($item->itemType->is($this->itemType))->toBeTrue()
        ->and($item->costs)->toHaveCount(1)
        ->and($item->costs->first()->is($cost))->toBeTrue()
        ->and($cost->currency->is($this->currency))->toBeTrue()
        ->and($item->weight_kg)->toBe(1.5)
        ->and($item->is_stackable)->toBeFalse()
        ->and($item->is_magical)->toBeFalse()
        ->and($cost->amount)->toBe(15);
});

//Verifica l'univocità delle tipologie
it('rifiuta chiavi duplicate nelle tipologie di oggetto', function () {
    expect(
        fn () => ItemType::query()->create([
            'key' => 'weapon',
            'name' => 'Seconda tipologia di arma',
            'sort_order' => 20,
        ])
    )->toThrow(QueryException::class);
});

//Verifica l'univocità degli oggetti nello stesso regolamento
it('rifiuta chiavi duplicate degli oggetti nello stesso regolamento', function () {
    $itemData = [
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_weapon',
        'name' => 'Arma di prova',
        'item_type_id' => $this->itemType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ];

    Item::query()->create($itemData);

    expect(
        fn () => Item::query()->create([
            ...$itemData,
            'name' => 'Seconda arma di prova',
        ])
    )->toThrow(QueryException::class);
});

//Verifica la cancellazione a cascata dei prezzi
it('elimina i prezzi insieme agli oggetti', function () {
    $item = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'temporary_weapon',
        'name' => 'Arma temporanea',
        'item_type_id' => $this->itemType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    $cost = $item->costs()->create([
        'currency_id' => $this->currency->id,
        'amount' => 10,
    ]);

    $costId = $cost->id;

    $item->delete();

    expect(
        ItemCost::query()
            ->whereKey($costId)
            ->exists()
    )->toBeFalse();
});
