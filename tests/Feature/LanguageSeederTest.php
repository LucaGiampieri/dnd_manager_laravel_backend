<?php

use App\Models\Language;
use App\Models\LanguageScript;
use Database\Seeders\LanguageScriptSeeder;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica il catalogo completo delle lingue
it('crea alfabeti e tutte le lingue senza duplicati', function () {
    //Esegue due volte i seeder per verificarne l'idempotenza
    $this->seed(LanguageScriptSeeder::class);
    $this->seed(LanguageSeeder::class);

    $this->seed(LanguageScriptSeeder::class);
    $this->seed(LanguageSeeder::class);

    //Recupera le lingue nel loro ordine ufficiale
    $languages = Language::query()
        ->orderBy('sort_order')
        ->get();

    //Definisce tutte le chiavi attese
    $expectedKeys = [
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
        'aarakocra',
        'blink_dog',
        'bullywug',
        'deep_crow',
        'giant_eagle',
        'giant_elk',
        'giant_owl',
        'gith',
        'gnoll',
        'grell',
        'grung',
        'hook_horror',
        'ice_toad',
        'ixitxachitl',
        'kruthik',
        'leonin',
        'loxodon',
        'minotaur',
        'modron',
        'otyugh',
        'quori',
        'sahuagin',
        'slaad',
        'sphinx',
        'thri_kreen',
        'tlincalli',
        'troglodyte',
        'umber_hulk',
        'vegepygmy',
        'vedalken',
        'winter_wolf',
        'worg',
        'yikaria',
        'yeti',
    ];

    //Verifica alfabeti, quantità, chiavi e ordinamento
    expect(LanguageScript::query()->count())->toBe(6)
        ->and(
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
        ])
        ->and($languages)->toHaveCount(56)
        ->and($languages->pluck('key')->all())
        ->toBe($expectedKeys)
        ->and($languages->pluck('sort_order')->all())
        ->toBe(range(1, 56));

    //Verifica la suddivisione per categoria
    expect(
        Language::query()
            ->where('category', 'standard')
            ->count()
    )->toBe(8)
        ->and(
            Language::query()
                ->where('category', 'exotic')
                ->count()
        )->toBe(42)
        ->and(
            Language::query()
                ->where('category', 'secret')
                ->count()
        )->toBe(2)
        ->and(
            Language::query()
                ->where('category', 'dialect')
                ->count()
        )->toBe(4);

    //Solo le lingue generali possono essere scelte liberamente
    expect(
        Language::query()
            ->where('selectable', true)
            ->count()
    )->toBe(16)
        ->and(
            Language::query()
                ->where('requires_dm_permission', false)
                ->count()
        )->toBe(8);

    //Le lingue speciali non compaiono nelle scelte generiche
    expect(
        Language::query()
            ->where('sort_order', '>=', 23)
            ->where('selectable', false)
            ->count()
    )->toBe(34);

    //Tutte le lingue devono possedere una descrizione
    expect(
        Language::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);

    //Verifica i quattro dialetti del Primordiale
    $primordial = Language::query()
        ->where('key', 'primordial')
        ->firstOrFail();

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

    //Verifica un alfabeto condiviso
    $giant = Language::query()
        ->where('key', 'giant')
        ->firstOrFail();

    expect($giant->languageScript->key)
        ->toBe('dwarvish');

    //Verifica la lingua speciale necessaria agli aarakocra
    $aarakocra = Language::query()
        ->where('key', 'aarakocra')
        ->firstOrFail();

    expect($aarakocra->name)->toBe('Aarakocra')
        ->and($aarakocra->category)->toBe('exotic')
        ->and($aarakocra->selectable)->toBeFalse()
        ->and($aarakocra->requires_dm_permission)->toBeTrue()
        ->and($aarakocra->languageScript)->toBeNull();
});
