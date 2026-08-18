<?php

use App\Models\DamageType;
use Database\Seeders\DamageTypeSeeder;

test('il seeder crea i tredici tipi di danno senza duplicati', function () {
    //Esegue due volte il seeder per verificare
    //che non vengano creati tipi di danno duplicati
    $this->seed(DamageTypeSeeder::class);
    $this->seed(DamageTypeSeeder::class);

    //Verifica che siano stati creati esattamente tredici tipi di danno
    expect(DamageType::count())->toBe(13);

    //Verifica che ogni tipo di danno possieda una descrizione
    expect(
        DamageType::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);

    //Verifica i nomi italiani ordinandoli alfabeticamente
    expect(
        DamageType::query()
            ->orderBy('name')
            ->pluck('name')
            ->all()
    )->toBe([
        'Acido',
        'Contundente',
        'Forza',
        'Freddo',
        'Fulmine',
        'Fuoco',
        'Necrotico',
        'Perforante',
        'Psichico',
        'Radioso',
        'Tagliente',
        'Tuono',
        'Veleno',
    ]);
});
