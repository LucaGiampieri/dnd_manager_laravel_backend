<?php

use App\Models\Race;
use App\Models\RaceLanguage;
use App\Models\Subrace;
use App\Models\SubraceLanguage;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RaceLanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce tutti i cataloghi richiesti dalle razze
beforeEach(function () {
    $this->seed(DatabaseSeeder::class);

    //Ripete il seeder per verificarne l'idempotenza
    $this->seed(RaceLanguageSeeder::class);
});

//Verifica le lingue automatiche delle razze
it('assegna le lingue automatiche senza duplicati', function () {
    $expectedLanguages = [
        'dwarf:phb_2014' => [
            'common',
            'dwarvish',
        ],
        'elf:phb_2014' => [
            'common',
            'elvish',
        ],
        'halfling:phb_2014' => [
            'common',
            'halfling',
        ],
        'human:phb_2014' => [
            'common',
        ],
        'dragonborn:phb_2014' => [
            'common',
            'draconic',
        ],
        'gnome:phb_2014' => [
            'common',
            'gnomish',
        ],
        'half_elf:phb_2014' => [
            'common',
            'elvish',
        ],
        'half_orc:phb_2014' => [
            'common',
            'orc',
        ],
        'tiefling:phb_2014' => [
            'common',
            'infernal',
        ],
        'aarakocra:eepc_2015' => [
            'aarakocra',
            'auran',
            'common',
        ],
        'genasi:eepc_2015' => [
            'common',
            'primordial',
        ],
        'goliath:eepc_2015' => [
            'common',
            'giant',
        ],
    ];

    foreach ($expectedLanguages as $ownerKey => $languageKeys) {
        [
            $canonicalKey,
            $versionKey,
        ] = explode(':', $ownerKey);

        $race = Race::query()
            ->where('canonical_key', $canonicalKey)
            ->where('version_key', $versionKey)
            ->firstOrFail();

        $actualKeys = $race->languageAssignments()
            ->with('language')
            ->get()
            ->pluck('language.key')
            ->sort()
            ->values()
            ->all();

        sort($languageKeys);

        expect($actualKeys)->toBe($languageKeys);
    }

    expect(RaceLanguage::query()->count())->toBe(24);
});

//Verifica le lingue specifiche delle sottorazze
it('assegna le lingue aggiuntive alle sottorazze', function () {
    $deepGnome = Subrace::query()
        ->where('canonical_key', 'deep_gnome')
        ->where('version_key', 'eepc_2015')
        ->firstOrFail();

    $duergar = Subrace::query()
        ->where('canonical_key', 'duergar')
        ->where('version_key', 'scag_2015')
        ->firstOrFail();

    expect(
        $deepGnome->languageAssignments()
            ->with('language')
            ->firstOrFail()
            ->language
            ->key
    )->toBe('undercommon')
        ->and(
            $duergar->languageAssignments()
                ->with('language')
                ->firstOrFail()
                ->language
                ->key
        )->toBe('undercommon')
        ->and(SubraceLanguage::query()->count())
        ->toBe(2);
});

//Verifica le scelte linguistiche del PHB
it('crea le scelte delle lingue aggiuntive', function () {
    $human = Race::query()
        ->where('canonical_key', 'human')
        ->where('version_key', 'phb_2014')
        ->firstOrFail();

    $halfElf = Race::query()
        ->where('canonical_key', 'half_elf')
        ->where('version_key', 'phb_2014')
        ->firstOrFail();

    $highElf = Subrace::query()
        ->where('canonical_key', 'high_elf')
        ->where('version_key', 'phb_2014')
        ->firstOrFail();

    $humanChoice = $human->choices()
        ->where('key', 'human_extra_language_phb_2014')
        ->firstOrFail();

    $halfElfChoice = $halfElf->choices()
        ->where('key', 'half_elf_extra_language_phb_2014')
        ->firstOrFail();

    $highElfChoice = $highElf->choices()
        ->where('key', 'high_elf_extra_language_phb_2014')
        ->firstOrFail();

    expect($humanChoice->choice_type)->toBe('language')
        ->and($humanChoice->choose)->toBe(1)
        ->and($humanChoice->options()->count())->toBe(15)
        ->and($halfElfChoice->options()->count())->toBe(14)
        ->and($highElfChoice->options()->count())->toBe(14);

    //Le opzioni non devono riproporre lingue già conosciute
    expect(
        $humanChoice->options()
            ->where('key', 'language_common')
            ->exists()
    )->toBeFalse()
        ->and(
            $halfElfChoice->options()
                ->whereIn('key', [
                    'language_common',
                    'language_elvish',
                ])
                ->exists()
        )->toBeFalse()
        ->and(
            $highElfChoice->options()
                ->whereIn('key', [
                    'language_common',
                    'language_elvish',
                ])
                ->exists()
        )->toBeFalse();

    //Le lingue speciali non devono apparire nelle scelte generiche
    expect(
        $humanChoice->options()
            ->whereIn('key', [
                'language_aarakocra',
                'language_gith',
                'language_quori',
            ])
            ->exists()
    )->toBeFalse();
});
