<?php

use App\Models\Language;
use App\Models\LanguageScript;
use Database\Seeders\LanguageScriptSeeder;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea alfabeti lingue e dialetti senza duplicati', function () {
    $this->seed(LanguageScriptSeeder::class);
    $this->seed(LanguageSeeder::class);

    $this->seed(LanguageScriptSeeder::class);
    $this->seed(LanguageSeeder::class);

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
        ->and(Language::query()->count())->toBe(22)
        ->and(
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
        ])
        ->and(
            Language::query()
                ->where('category', 'standard')
                ->count()
        )->toBe(8)
        ->and(
            Language::query()
                ->where('category', 'exotic')
                ->count()
        )->toBe(8)
        ->and(
            Language::query()
                ->where('category', 'secret')
                ->count()
        )->toBe(2)
        ->and(
            Language::query()
                ->where('category', 'dialect')
                ->count()
        )->toBe(4)
        ->and(Language::query()->whereNull('description')->count())->toBe(0);

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

    $giant = Language::query()
        ->where('key', 'giant')
        ->firstOrFail();

    expect($giant->languageScript->key)->toBe('dwarvish');
});
