<?php

use App\Models\ArmorProficiency;
use App\Models\ArmorProficiencyItem;
use Database\Seeders\ArmorProficiencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Ripete il seeder per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(ArmorProficiencySeeder::class);
    $this->seed(ArmorProficiencySeeder::class);
});

//Verifica la creazione delle quattro competenze ufficiali
it('crea tutte le competenze nelle armature senza duplicati', function () {
    //Sono previste soltanto le quattro categorie ufficiali
    expect(ArmorProficiency::query()->count())
        ->toBe(4)
        ->and(
            ArmorProficiency::query()
                ->where('type', 'category')
                ->count()
        )
        ->toBe(4)
        ->and(
            ArmorProficiency::query()
                ->where('type', 'specific')
                ->count()
        )
        ->toBe(0);

    //Ogni armatura o scudo appartiene a una sola categoria
    expect(ArmorProficiencyItem::query()->count())
        ->toBe(13);
});

//Verifica il numero di oggetti appartenenti a ogni categoria
it('assegna armature e scudo alle categorie corrette', function () {
    //Definisce le quantità previste
    $expectedCounts = [
        'light_armor_phb_2014' => 3,
        'medium_armor_phb_2014' => 5,
        'heavy_armor_phb_2014' => 4,
        'shields_phb_2014' => 1,
    ];

    //Controlla ogni categoria
    foreach ($expectedCounts as $key => $expectedCount) {
        $proficiency = ArmorProficiency::query()
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

//Verifica alcuni esempi di appartenenza
it('distingue correttamente le categorie di armatura', function () {
    //Recupera le competenze da controllare
    $lightArmor = ArmorProficiency::query()
        ->where('key', 'light_armor_phb_2014')
        ->firstOrFail();

    $heavyArmor = ArmorProficiency::query()
        ->where('key', 'heavy_armor_phb_2014')
        ->firstOrFail();

    $shields = ArmorProficiency::query()
        ->where('key', 'shields_phb_2014')
        ->firstOrFail();

    //L'armatura di cuoio deve essere soltanto leggera
    expect(
        $lightArmor->items()
            ->where('items.key', 'leather_armor')
            ->exists()
    )
        ->toBeTrue()
        ->and(
            $heavyArmor->items()
                ->where('items.key', 'leather_armor')
                ->exists()
        )
        ->toBeFalse();

    //L'armatura completa deve essere pesante
    expect(
        $heavyArmor->items()
            ->where('items.key', 'plate_armor')
            ->exists()
    )
        ->toBeTrue();

    //Lo scudo deve appartenere soltanto alla categoria scudi
    expect(
        $shields->items()
            ->where('items.key', 'shield')
            ->exists()
    )
        ->toBeTrue()
        ->and(
            $lightArmor->items()
                ->where('items.key', 'shield')
                ->exists()
        )
        ->toBeFalse();
});

//Verifica che nessun oggetto appartenga a più categorie
it('assegna ogni oggetto a una sola competenza di categoria', function () {
    //Recupera tutti gli identificativi assegnati
    $assignedItemIds = ArmorProficiencyItem::query()
        ->pluck('item_id');

    //I tredici collegamenti devono riferirsi a tredici oggetti distinti
    expect($assignedItemIds->count())
        ->toBe(13)
        ->and($assignedItemIds->unique()->count())
        ->toBe(13);
});
