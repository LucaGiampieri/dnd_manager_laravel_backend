<?php

use App\Models\Item;
use App\Models\WeaponProficiency;
use App\Models\WeaponProficiencyItem;
use Database\Seeders\WeaponProficiencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Crea il catalogo e ripete il seeder per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(WeaponProficiencySeeder::class);
    $this->seed(WeaponProficiencySeeder::class);
});

//Verifica la creazione delle categorie e delle competenze specifiche
it('crea tutte le competenze nelle armi senza duplicati', function () {
    //Sono previste sei categorie e trentasette competenze specifiche
    expect(WeaponProficiency::query()->count())
        ->toBe(43)
        ->and(
            WeaponProficiency::query()
                ->where('type', 'category')
                ->count()
        )
        ->toBe(6)
        ->and(
            WeaponProficiency::query()
                ->where('type', 'specific')
                ->count()
        )
        ->toBe(37);

    //Le appartenenze alle sei categorie devono essere idempotenti
    expect(WeaponProficiencyItem::query()->count())
        ->toBe(74);
});

//Verifica il contenuto delle categorie semplici e marziali
it('assegna tutte le armi alle categorie corrette', function () {
    //Definisce il numero previsto di armi per categoria
    $expectedCounts = [
        'simple_weapons_phb_2014' => 14,
        'simple_melee_weapons_phb_2014' => 10,
        'simple_ranged_weapons_phb_2014' => 4,
        'martial_weapons_phb_2014' => 23,
        'martial_melee_weapons_phb_2014' => 18,
        'martial_ranged_weapons_phb_2014' => 5,
    ];

    //Verifica ogni categoria e il numero delle armi collegate
    foreach ($expectedCounts as $key => $expectedCount) {
        $proficiency = WeaponProficiency::query()
            ->where('key', $key)
            ->firstOrFail();

        expect($proficiency->type)
            ->toBe('category')
            ->and($proficiency->item_id)
            ->toBeNull()
            ->and($proficiency->items()->count())
            ->toBe($expectedCount);
    }
});

//Verifica alcuni esempi di appartenenza alle categorie
it('distingue le armi semplici da quelle marziali', function () {
    //Recupera le categorie principali
    $simpleWeapons = WeaponProficiency::query()
        ->where('key', 'simple_weapons_phb_2014')
        ->firstOrFail();

    $martialWeapons = WeaponProficiency::query()
        ->where('key', 'martial_weapons_phb_2014')
        ->firstOrFail();

    //Verifica che il pugnale sia soltanto tra le armi semplici
    expect(
        $simpleWeapons->items()
            ->where('items.key', 'dagger')
            ->exists()
    )
        ->toBeTrue()
        ->and(
            $martialWeapons->items()
                ->where('items.key', 'dagger')
                ->exists()
        )
        ->toBeFalse();

    //Verifica che la spada lunga sia soltanto tra le armi marziali
    expect(
        $martialWeapons->items()
            ->where('items.key', 'longsword')
            ->exists()
    )
        ->toBeTrue()
        ->and(
            $simpleWeapons->items()
                ->where('items.key', 'longsword')
                ->exists()
        )
        ->toBeFalse();
});

//Verifica le competenze collegate a una singola arma
it('collega le competenze specifiche alle rispettive armi', function () {
    //Recupera la spada lunga del PHB 2014
    $longsword = Item::query()
        ->where('canonical_key', 'longsword')
        ->where('version_key', 'phb_2014')
        ->firstOrFail();

    //Recupera la relativa competenza specifica
    $proficiency = WeaponProficiency::query()
        ->where('key', 'weapon_longsword_phb_2014')
        ->firstOrFail();

    //Verifica tipo e collegamento diretto all'oggetto
    expect($proficiency->type)
        ->toBe('specific')
        ->and($proficiency->item->is($longsword))
        ->toBeTrue()
        ->and($proficiency->items()->count())
        ->toBe(0);
});

//Verifica che ogni arma possieda una competenza specifica
it('crea una competenza specifica per ogni arma', function () {
    //Recupera le chiavi canoniche delle armi
    $weaponKeys = Item::query()
        ->where('version_key', 'phb_2014')
        ->whereHas('weaponProfile')
        ->pluck('canonical_key');

    //Costruisce le chiavi attese delle competenze
    $expectedProficiencyKeys = $weaponKeys
        ->map(
            fn (string $key): string =>
                "weapon_{$key}_phb_2014"
        )
        ->sort()
        ->values()
        ->all();

    //Recupera le competenze specifiche realmente create
    $actualProficiencyKeys = WeaponProficiency::query()
        ->where('type', 'specific')
        ->pluck('key')
        ->sort()
        ->values()
        ->all();

    //Verifica la corrispondenza completa
    expect($actualProficiencyKeys)
        ->toBe($expectedProficiencyKeys);
});
