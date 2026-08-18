<?php

use App\Models\Ruleset;
use App\Models\SourceBook;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;

test('i seeder creano tre manuali senza duplicati', function () {
    //Esegue una prima volta i seeder
    //del regolamento e dei manuali
    $this->seed(RulesetSeeder::class);
    $this->seed(SourceBookSeeder::class);

    //Ripete entrambi i seeder per verificare
    //che non vengano creati record duplicati
    $this->seed(RulesetSeeder::class);
    $this->seed(SourceBookSeeder::class);

    //Verifica che esista un solo regolamento D&D 5e 2014
    expect(
        Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->count()
    )->toBe(1);

    //Verifica che siano stati creati esattamente tre manuali
    expect(SourceBook::query()->count())->toBe(3);

    //Verifica che siano presenti i tre manuali base
    expect(
        SourceBook::query()
            ->pluck('slug')
            ->all()
    )
        ->toContain('phb-2014')
        ->toContain('dmg-2014')
        ->toContain('mm-2014');

    //Recupera il Manuale del Giocatore
    $playerHandbook = SourceBook::query()
        ->where('slug', 'phb-2014')
        ->firstOrFail();

    //Relazione molti-a-uno (BelongsTo):
    //verifica che il manuale appartenga al regolamento corretto
    expect($playerHandbook->ruleset->key)
        ->toBe('dnd5e_2014');
});
