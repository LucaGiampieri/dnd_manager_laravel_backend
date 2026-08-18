<?php

use App\Models\Sense;
use Database\Seeders\SenseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per evitare interferenze con sensi già presenti
uses(RefreshDatabase::class);

it('crea i quattro sensi speciali senza duplicati', function () {
    //Esegue due volte il seeder per verificare
    //che non vengano creati sensi duplicati
    $this->seed(SenseSeeder::class);
    $this->seed(SenseSeeder::class);

    //Recupera i sensi speciali nell’ordine stabilito
    $senses = Sense::query()
        ->orderBy('sort_order')
        ->get();

    //Verifica che siano stati creati esattamente quattro sensi
    expect($senses)->toHaveCount(4);

    //Verifica le chiavi tecniche e il loro ordine
    expect($senses->pluck('key')->all())->toBe([
        'blindsight',
        'darkvision',
        'tremorsense',
        'truesight',
    ]);

    //Verifica i nomi italiani e il loro ordine
    expect($senses->pluck('name')->all())->toBe([
        'Vista Cieca',
        'Scurovisione',
        'Percezione Tellurica',
        'Vista Pura',
    ]);

    //Verifica che ogni senso possieda una descrizione
    expect(
        Sense::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);
});
