<?php

use App\Models\Language;
use App\Models\LanguageScript;
use Database\Seeders\LanguageScriptSeeder;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per garantire che alfabeti e lingue partano da uno stato pulito
uses(RefreshDatabase::class);

it('crea alfabeti lingue e dialetti senza duplicati', function () {
    //Esegue una prima volta i seeder degli alfabeti e delle lingue
    $this->seed(LanguageScriptSeeder::class);
    $this->seed(LanguageSeeder::class);

    //Ripete entrambi i seeder per verificare
    //che non vengano creati record duplicati
    $this->seed(LanguageScriptSeeder::class);
    $this->seed(LanguageSeeder::class);

    //Verifica che siano stati creati esattamente sei alfabeti
    expect(LanguageScript::query()->count())->toBe(6);

    //Verifica i nomi degli alfabeti e il loro ordine
    expect(
        LanguageScript::query()
            ->orderBy('sort_order')
            ->pluck('name')
            ->all()
    )->toBe([
        'Alfabeto Comune',
        'Alfabeto Nanico',
        'Alfabeto Elfico',
        'Alfabeto Infernale',
        'Alfabeto Celestiale',
        'Alfabeto Draconico',
    ]);

    //Verifica che siano state create esattamente ventidue lingue
    expect(Language::query()->count())->toBe(22);

    //Verifica le chiavi di tutte le lingue e il loro ordine
    expect(
        Language::query()
            ->orderBy('sort_order')
            ->pluck('key')
            ->all()
    )->toBe([
        'common',
        'dwarvish',
        'elvish',
        'giant',
        'gnomish',
        'goblin',
        'halfling',
        'orc',
        'abyssal',
        'celestial',
        'draconic',
        'deep_speech',
        'infernal',
        'primordial',
        'sylvan',
        'undercommon',
        'druidic',
        'thieves_cant',
        'auran',
        'aquan',
        'ignan',
        'terran',
    ]);

    //Verifica che siano presenti otto lingue standard
    expect(
        Language::query()
            ->where('category', 'standard')
            ->count()
    )->toBe(8);

    //Verifica che siano presenti otto lingue esotiche
    expect(
        Language::query()
            ->where('category', 'exotic')
            ->count()
    )->toBe(8);

    //Verifica che siano presenti due lingue segrete
    expect(
        Language::query()
            ->where('category', 'secret')
            ->count()
    )->toBe(2);

    //Verifica che siano presenti quattro dialetti
    expect(
        Language::query()
            ->where('category', 'dialect')
            ->count()
    )->toBe(4);

    //Verifica che ogni lingua possieda una descrizione
    expect(
        Language::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);

    //Recupera le due lingue segrete concesse dalle capacità di classe
    $secretLanguages = Language::query()
        ->whereIn('key', [
            'druidic',
            'thieves_cant',
        ])
        ->get();

    //Verifica che le lingue segrete non siano
    //selezionabili come normali opzioni linguistiche
    expect(
        $secretLanguages->every(
            fn (Language $language): bool =>
                $language->selectable === false
        )
    )->toBeTrue();

    //Verifica che un’assegnazione speciale
    //delle lingue segrete richieda il permesso del DM
    expect(
        $secretLanguages->every(
            fn (Language $language): bool =>
                $language->requires_dm_permission === true
        )
    )->toBeTrue();

    //Recupera la lingua principale Primordiale
    $primordial = Language::query()
        ->where('key', 'primordial')
        ->firstOrFail();

    //Relazione uno-a-molti autoriferita (HasMany):
    //verifica che Primordiale possieda i suoi quattro dialetti
    expect(
        $primordial->dialects
            ->pluck('key')
            ->sort()
            ->values()
            ->all()
    )->toBe([
        'aquan',
        'auran',
        'ignan',
        'terran',
    ]);

    //Recupera la lingua Gigante
    $giant = Language::query()
        ->where('key', 'giant')
        ->firstOrFail();

    //Relazione molti-a-uno (BelongsTo):
    //verifica che Gigante utilizzi l’Alfabeto Nanico
    expect($giant->languageScript->key)->toBe('dwarvish');
});
