<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Race;
use App\Models\RaceChoice;
use App\Models\Subrace;
use App\Models\SubraceChoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class RaceLanguageSeeder extends Seeder
{
    //Inserisce lingue automatiche e scelte linguistiche
    public function run(): void
    {
        //Indicizza tutte le lingue tramite la chiave tecnica
        $languages = Language::query()
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');

        //Inserisce le lingue automatiche delle razze
        foreach ($this->raceLanguageDefinitions() as [
            $canonicalKey,
            $versionKey,
            $languageKeys,
        ]) {
            $race = $this->findRace(
                $canonicalKey,
                $versionKey
            );

            $this->syncLanguages(
                $race,
                $languages,
                $languageKeys
            );
        }

        //Inserisce le lingue aggiuntive delle sottorazze
        foreach ($this->subraceLanguageDefinitions() as [
            $canonicalKey,
            $versionKey,
            $languageKeys,
        ]) {
            $subrace = $this->findSubrace(
                $canonicalKey,
                $versionKey
            );

            $this->syncLanguages(
                $subrace,
                $languages,
                $languageKeys
            );
        }

        //Crea le scelte linguistiche del PHB
        $this->seedLanguageChoices($languages);
    }

    //Definisce le lingue automatiche delle razze
    private function raceLanguageDefinitions(): array
    {
        return [
            //Razze del Manuale del Giocatore
            ['dwarf', 'phb_2014', [
                'common',
                'dwarvish',
            ]],
            ['elf', 'phb_2014', [
                'common',
                'elvish',
            ]],
            ['halfling', 'phb_2014', [
                'common',
                'halfling',
            ]],
            ['human', 'phb_2014', [
                'common',
            ]],
            ['dragonborn', 'phb_2014', [
                'common',
                'draconic',
            ]],
            ['gnome', 'phb_2014', [
                'common',
                'gnomish',
            ]],
            ['half_elf', 'phb_2014', [
                'common',
                'elvish',
            ]],
            ['half_orc', 'phb_2014', [
                'common',
                'orc',
            ]],
            ['tiefling', 'phb_2014', [
                'common',
                'infernal',
            ]],

            //Razze dell'Elemental Evil Player's Companion
            ['aarakocra', 'eepc_2015', [
                'common',
                'aarakocra',
                'auran',
            ]],
            ['genasi', 'eepc_2015', [
                'common',
                'primordial',
            ]],
            ['goliath', 'eepc_2015', [
                'common',
                'giant',
            ]],
        ];
    }

    //Definisce le lingue aggiunte dalle sottorazze
    private function subraceLanguageDefinitions(): array
    {
        return [
            //Lo Gnomo delle Profondità aggiunge il Sottocomune
            ['deep_gnome', 'eepc_2015', [
                'undercommon',
            ]],

            //Il Duergar aggiunge il Sottocomune
            ['duergar', 'scag_2015', [
                'undercommon',
            ]],
        ];
    }

    //Crea le scelte di una lingua aggiuntiva
    private function seedLanguageChoices(
        Collection $languages
    ): void {
        //L'Umano sceglie una lingua oltre al Comune
        $humanChoice = $this->findRace(
            'human',
            'phb_2014'
        )->choices()->updateOrCreate(
            [
                'key' => 'human_extra_language_phb_2014',
            ],
            [
                'name' => 'Lingua aggiuntiva',
                'choice_type' => 'language',
                'choose' => 1,
                'level' => 1,
                'required' => true,
                'requires_dm_permission' => false,
                'replaces_feature_id' => null,
                'sort_order' => 20,
                'description' =>
                    'Sceglie una lingua aggiuntiva tra quelle '
                    . 'disponibili nella campagna.',
                'notes' =>
                    'Le lingue esotiche richiedono il permesso '
                    . 'del Dungeon Master.',
            ]
        );

        $this->syncLanguageOptions(
            $humanChoice,
            $languages,
            [
                'common',
            ]
        );

        //Il Mezzelfo sceglie una lingua oltre a Comune ed Elfico
        $halfElfChoice = $this->findRace(
            'half_elf',
            'phb_2014'
        )->choices()->updateOrCreate(
            [
                'key' => 'half_elf_extra_language_phb_2014',
            ],
            [
                'name' => 'Lingua aggiuntiva',
                'choice_type' => 'language',
                'choose' => 1,
                'level' => 1,
                'required' => true,
                'requires_dm_permission' => false,
                'replaces_feature_id' => null,
                'sort_order' => 20,
                'description' =>
                    'Sceglie una lingua aggiuntiva oltre '
                    . 'al Comune e all’Elfico.',
                'notes' =>
                    'Le lingue esotiche richiedono il permesso '
                    . 'del Dungeon Master.',
            ]
        );

        $this->syncLanguageOptions(
            $halfElfChoice,
            $languages,
            [
                'common',
                'elvish',
            ]
        );

        //L'Elfo Alto sceglie una lingua aggiuntiva
        $highElfChoice = $this->findSubrace(
            'high_elf',
            'phb_2014'
        )->choices()->updateOrCreate(
            [
                'key' => 'high_elf_extra_language_phb_2014',
            ],
            [
                'name' => 'Lingua aggiuntiva',
                'choice_type' => 'language',
                'choose' => 1,
                'level' => 1,
                'required' => true,
                'requires_dm_permission' => false,
                'replaces_feature_id' => null,
                'sort_order' => 20,
                'description' =>
                    'Sceglie una lingua aggiuntiva tra quelle '
                    . 'disponibili nella campagna.',
                'notes' =>
                    'Le lingue esotiche richiedono il permesso '
                    . 'del Dungeon Master.',
            ]
        );

        $this->syncLanguageOptions(
            $highElfChoice,
            $languages,
            [
                'common',
                'elvish',
            ]
        );
    }

    //Sincronizza le lingue automatiche di una razza o sottorazza
    private function syncLanguages(
        Race|Subrace $owner,
        Collection $languages,
        array $languageKeys
    ): void {
        $languageIds = [];

        foreach ($languageKeys as $languageKey) {
            $language = $languages->get($languageKey);

            if ($language === null) {
                throw new RuntimeException(
                    "Lingua {$languageKey} non trovata."
                );
            }

            $languageIds[] = $language->id;

            $owner->languageAssignments()->updateOrCreate(
                [
                    'language_id' => $language->id,
                ],
                [
                    'notes' =>
                        'Lingua conosciuta automaticamente.',
                ]
            );
        }

        //Rimuove assegnazioni obsolete controllate dal seeder
        $owner->languageAssignments()
            ->whereNotIn('language_id', $languageIds)
            ->delete();
    }

    //Sincronizza le opzioni di una scelta linguistica
    private function syncLanguageOptions(
        RaceChoice|SubraceChoice $choice,
        Collection $languages,
        array $excludedKeys
    ): void {
        //Utilizza soltanto lingue normalmente selezionabili
        $selectableLanguages = $languages
            ->where('selectable', true)
            ->reject(
                fn (Language $language) => in_array(
                    $language->key,
                    $excludedKeys,
                    true
                )
            )
            ->values();

        $expectedOptionKeys = [];

        foreach ($selectableLanguages as $language) {
            $optionKey = 'language_' . $language->key;

            $expectedOptionKeys[] = $optionKey;

            $choice->options()->updateOrCreate(
                [
                    'key' => $optionKey,
                ],
                [
                    'option_type' => 'language',
                    'option_id' => $language->id,
                    'option_text' => null,
                    'value' => null,
                    'quantity' => 1,
                    'ancestry_key' => null,
                    'eligibility_condition' =>
                        'La lingua non deve essere già conosciuta '
                        . 'dal personaggio.',
                    'sort_order' => $language->sort_order,
                    'notes' => $language
                        ->requires_dm_permission
                            ? 'Richiede il permesso del Dungeon Master.'
                            : null,
                ]
            );
        }

        //Rimuove opzioni linguistiche non più disponibili
        $choice->options()
            ->whereNotIn('key', $expectedOptionKeys)
            ->delete();
    }

    //Recupera una precisa versione di una razza
    private function findRace(
        string $canonicalKey,
        string $versionKey
    ): Race {
        $race = Race::query()
            ->where('canonical_key', $canonicalKey)
            ->where('version_key', $versionKey)
            ->first();

        if ($race === null) {
            throw new RuntimeException(
                "Razza {$canonicalKey} nella versione "
                . "{$versionKey} non trovata."
            );
        }

        return $race;
    }

    //Recupera una precisa versione di una sottorazza
    private function findSubrace(
        string $canonicalKey,
        string $versionKey
    ): Subrace {
        return Subrace::query()
            ->where('canonical_key', $canonicalKey)
            ->where('version_key', $versionKey)
            ->firstOrFail();
    }
}
