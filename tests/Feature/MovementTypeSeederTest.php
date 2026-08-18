<?php

use App\Models\MovementType;
use Database\Seeders\MovementTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per evitare interferenze con tipi di movimento già presenti
uses(RefreshDatabase::class);

it('crea i cinque tipi di movimento senza duplicati', function () {
    //Esegue due volte il seeder per verificare
    //che non vengano creati tipi di movimento duplicati
    $this->seed(MovementTypeSeeder::class);
    $this->seed(MovementTypeSeeder::class);

    //Verifica che siano stati creati esattamente cinque tipi
    expect(MovementType::count())->toBe(5);

    //Verifica i nomi italiani ordinandoli alfabeticamente
    expect(
        MovementType::query()
            ->orderBy('name')
            ->pluck('name')
            ->all()
    )->toBe([
        'Nuotare',
        'Scalare',
        'Scavare',
        'Terrestre',
        'Volare',
    ]);

    //Verifica che ogni tipo di movimento possieda una descrizione
    expect(
        MovementType::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);
});
