<?php

use App\Models\ItemType;
use Database\Seeders\ItemTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima del test
uses(RefreshDatabase::class);

//Verifica la creazione completa delle tipologie di oggetto
it('crea tutte le tipologie di oggetto senza duplicati', function () {
    //Esegue due volte il seeder per verificarne l'idempotenza
    $this->seed(ItemTypeSeeder::class);
    $this->seed(ItemTypeSeeder::class);

    //Definisce le chiavi tecniche nell'ordine previsto
    $expectedKeys = [
        'weapon',
        'armor',
        'shield',
        'ammunition',
        'adventuring_gear',
        'container',
        'clothing',
        'artisan_tool',
        'gaming_set',
        'musical_instrument',
        'kit',
        'other_tool',
        'spellcasting_focus',
        'holy_symbol',
        'mount',
        'vehicle',
        'tack_and_harness',
        'trade_good',
        'food_and_drink',
        'poison',
        'explosive',
        'potion',
        'scroll',
        'wand',
        'rod',
        'staff',
        'ring',
        'wondrous_item',
        'gemstone',
        'art_object',
        'treasure',
    ];

    //Recupera le tipologie nel loro ordine ufficiale
    $itemTypes = ItemType::query()
        ->orderBy('sort_order')
        ->get();

    //Verifica quantità, chiavi e ordinamento
    expect($itemTypes)->toHaveCount(31)
        ->and($itemTypes->pluck('key')->all())
        ->toBe($expectedKeys)
        ->and($itemTypes->pluck('sort_order')->all())
        ->toBe(range(10, 310, 10));

    //Verifica che nomi e descrizioni siano sempre presenti
    expect(
        $itemTypes->pluck('name')->unique()->count()
    )->toBe(31)
        ->and(
            $itemTypes->every(
                fn (ItemType $itemType): bool =>
                    filled($itemType->description)
            )
        )
        ->toBeTrue();
});
