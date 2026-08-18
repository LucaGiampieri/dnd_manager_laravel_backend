<?php

use App\Models\CreatureType;
use Database\Seeders\CreatureTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per garantire che il test parta da uno stato pulito
uses(RefreshDatabase::class);

it('crea i quattordici tipi di creatura senza duplicati', function () {
    //Esegue due volte il seeder per verificare
    //che non vengano creati tipi di creatura duplicati
    $this->seed(CreatureTypeSeeder::class);
    $this->seed(CreatureTypeSeeder::class);

    //Recupera tutti i tipi di creatura nell’ordine previsto
    $creatureTypes = CreatureType::query()
        ->orderBy('sort_order')
        ->get();

    //Verifica che siano stati creati esattamente quattordici tipi
    expect($creatureTypes)->toHaveCount(14);

    //Verifica le chiavi tecniche e il loro ordine
    expect($creatureTypes->pluck('key')->all())->toBe([
        'aberration',
        'beast',
        'celestial',
        'construct',
        'dragon',
        'elemental',
        'fey',
        'fiend',
        'giant',
        'humanoid',
        'monstrosity',
        'ooze',
        'plant',
        'undead',
    ]);

    //Verifica i nomi italiani e il loro ordine
    expect($creatureTypes->pluck('name')->all())->toBe([
        'Aberrazione',
        'Bestia',
        'Celestiale',
        'Costrutto',
        'Drago',
        'Elementale',
        'Folletto',
        'Immondo',
        'Gigante',
        'Umanoide',
        'Mostruosità',
        'Melma',
        'Vegetale',
        'Non Morto',
    ]);

    //Verifica che ogni tipo di creatura possieda una descrizione
    expect(
        CreatureType::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);
});
