<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\LanguageScript;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    //Inserisce lingue, lingue segrete e dialetti
    public function run(): void
    {
        //Garantisce che gli alfabeti esistano prima delle lingue
        $this->call(LanguageScriptSeeder::class);

        //Crea una mappa tra chiave tecnica e ID degli alfabeti
        $scriptIds = LanguageScript::query()
            ->pluck('id', 'key');

        //Definisce tutte le lingue e i dialetti
        $languages = [
            //Lingue standard

            //Comune
            [
                'key' => 'common',
                'name' => 'Comune',
                'family' => null,
                'category' => 'standard',
                'script_key' => 'common',
                'parent_key' => null,
                'typical_speakers' => 'Umani e numerosi popoli civilizzati.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'sort_order' => 1,
                'description' => 'Lingua ampiamente utilizzata per comunicare tra popoli e culture differenti.',
            ],

            //Nanico
            [
                'key' => 'dwarvish',
                'name' => 'Nanico',
                'family' => null,
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'parent_key' => null,
                'typical_speakers' => 'Nani.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'sort_order' => 2,
                'description' => 'Lingua tradizionalmente parlata dai nani e scritta usando l’alfabeto Nanico.',
            ],

            //Elfico
            [
                'key' => 'elvish',
                'name' => 'Elfico',
                'family' => null,
                'category' => 'standard',
                'script_key' => 'elvish',
                'parent_key' => null,
                'typical_speakers' => 'Elfi.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'sort_order' => 3,
                'description' => 'Lingua tradizionalmente parlata dagli elfi e scritta usando l’alfabeto Elfico.',
            ],

            //Gigante
            [
                'key' => 'giant',
                'name' => 'Gigante',
                'family' => null,
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'parent_key' => null,
                'typical_speakers' => 'Ogre e giganti.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'sort_order' => 4,
                'description' => 'Lingua utilizzata dai giganti e da creature culturalmente legate a essi.',
            ],

            //Gnomesco
            [
                'key' => 'gnomish',
                'name' => 'Gnomesco',
                'family' => null,
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'parent_key' => null,
                'typical_speakers' => 'Gnomi.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'sort_order' => 5,
                'description' => 'Lingua tradizionalmente parlata dagli gnomi e scritta usando l’alfabeto Nanico.',
            ],

            //Goblin
            [
                'key' => 'goblin',
                'name' => 'Goblin',
                'family' => null,
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'parent_key' => null,
                'typical_speakers' => 'Goblinoidi.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'sort_order' => 6,
                'description' => 'Lingua condivisa da numerosi popoli goblinoidi.',
            ],

            //Halfling
            [
                'key' => 'halfling',
                'name' => 'Halfling',
                'family' => null,
                'category' => 'standard',
                'script_key' => 'common',
                'parent_key' => null,
                'typical_speakers' => 'Halfling.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'sort_order' => 7,
                'description' => 'Lingua tradizionale degli halfling, scritta usando l’alfabeto Comune.',
            ],

            //Orchesco
            [
                'key' => 'orc',
                'name' => 'Orchesco',
                'family' => null,
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'parent_key' => null,
                'typical_speakers' => 'Orchi.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'sort_order' => 8,
                'description' => 'Lingua tradizionalmente parlata dagli orchi e scritta usando l’alfabeto Nanico.',
            ],

            //Lingue esotiche

            //Abissale
            [
                'key' => 'abyssal',
                'name' => 'Abissale',
                'family' => null,
                'category' => 'exotic',
                'script_key' => 'infernal',
                'parent_key' => null,
                'typical_speakers' => 'Demoni.',
                'common' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 9,
                'description' => 'Lingua delle creature demoniache dell’Abisso, scritta usando l’alfabeto Infernale.',
            ],

            //Celestiale
            [
                'key' => 'celestial',
                'name' => 'Celestiale',
                'family' => null,
                'category' => 'exotic',
                'script_key' => 'celestial',
                'parent_key' => null,
                'typical_speakers' => 'Celestiali.',
                'common' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 10,
                'description' => 'Lingua associata alle creature originarie dei Piani Superiori.',
            ],

            //Draconico
            [
                'key' => 'draconic',
                'name' => 'Draconico',
                'family' => null,
                'category' => 'exotic',
                'script_key' => 'draconic',
                'parent_key' => null,
                'typical_speakers' => 'Draghi e dragonidi.',
                'common' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 11,
                'description' => 'Antica lingua associata ai draghi e alle creature di discendenza draconica.',
            ],

            //Gergo delle Profondità
            [
                'key' => 'deep_speech',
                'name' => 'Gergo delle Profondità',
                'family' => null,
                'category' => 'exotic',
                'script_key' => null,
                'parent_key' => null,
                'typical_speakers' => 'Aboleth e altre aberrazioni delle profondità.',
                'common' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 12,
                'description' => 'Lingua aliena utilizzata da alcune aberrazioni. Non possiede un alfabeto indicato nella tabella delle lingue.',
            ],

            //Infernale
            [
                'key' => 'infernal',
                'name' => 'Infernale',
                'family' => null,
                'category' => 'exotic',
                'script_key' => 'infernal',
                'parent_key' => null,
                'typical_speakers' => 'Diavoli.',
                'common' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 13,
                'description' => 'Lingua ordinata e rituale associata ai diavoli e ai Nove Inferi.',
            ],

            //Primordiale
            [
                'key' => 'primordial',
                'name' => 'Primordiale',
                'family' => 'Primordiale',
                'category' => 'exotic',
                'script_key' => 'dwarvish',
                'parent_key' => null,
                'typical_speakers' => 'Elementali.',
                'common' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 14,
                'description' => 'Famiglia linguistica degli elementali che comprende i dialetti Auran, Aquan, Ignan e Terran.',
            ],

            //Silvano
            [
                'key' => 'sylvan',
                'name' => 'Silvano',
                'family' => null,
                'category' => 'exotic',
                'script_key' => 'elvish',
                'parent_key' => null,
                'typical_speakers' => 'Creature fatate.',
                'common' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 15,
                'description' => 'Lingua delle creature fatate, scritta usando l’alfabeto Elfico.',
            ],

            //Sottocomune
            [
                'key' => 'undercommon',
                'name' => 'Sottocomune',
                'family' => null,
                'category' => 'exotic',
                'script_key' => 'elvish',
                'parent_key' => null,
                'typical_speakers' => 'Mercanti e popoli del Sottosuolo.',
                'common' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 16,
                'description' => 'Lingua commerciale diffusa nel Sottosuolo e scritta usando l’alfabeto Elfico.',
            ],

            //Lingue segrete

            //Druidico
            [
                'key' => 'druidic',
                'name' => 'Druidico',
                'family' => null,
                'category' => 'secret',
                'script_key' => null,
                'parent_key' => null,
                'typical_speakers' => 'Druidi.',
                'common' => false,
                'selectable' => false,
                'requires_dm_permission' => true,
                'sort_order' => 17,
                'description' => 'Lingua segreta dei druidi, utilizzata anche per lasciare messaggi nascosti riconoscibili da altri druidi.',
            ],

            //Gergo Ladresco
            [
                'key' => 'thieves_cant',
                'name' => 'Gergo Ladresco',
                'family' => null,
                'category' => 'secret',
                'script_key' => null,
                'parent_key' => null,
                'typical_speakers' => 'Ladri addestrati nel gergo.',
                'common' => false,
                'selectable' => false,
                'requires_dm_permission' => true,
                'sort_order' => 18,
                'description' => 'Sistema segreto di gergo, dialetti, segni e simboli usato per nascondere messaggi all’interno di conversazioni apparentemente normali.',
            ],

            //Dialetti del Primordiale

            //Auran
            [
                'key' => 'auran',
                'name' => 'Auran',
                'family' => 'Primordiale',
                'category' => 'dialect',
                'script_key' => 'dwarvish',
                'parent_key' => 'primordial',
                'typical_speakers' => 'Creature legate al Piano Elementale dell’Aria.',
                'common' => false,
                'selectable' => false,
                'requires_dm_permission' => true,
                'sort_order' => 19,
                'description' => 'Dialetto del Primordiale associato all’elemento dell’aria.',
            ],

            //Aquan
            [
                'key' => 'aquan',
                'name' => 'Aquan',
                'family' => 'Primordiale',
                'category' => 'dialect',
                'script_key' => 'dwarvish',
                'parent_key' => 'primordial',
                'typical_speakers' => 'Creature legate al Piano Elementale dell’Acqua.',
                'common' => false,
                'selectable' => false,
                'requires_dm_permission' => true,
                'sort_order' => 20,
                'description' => 'Dialetto del Primordiale associato all’elemento dell’acqua.',
            ],

            //Ignan
            [
                'key' => 'ignan',
                'name' => 'Ignan',
                'family' => 'Primordiale',
                'category' => 'dialect',
                'script_key' => 'dwarvish',
                'parent_key' => 'primordial',
                'typical_speakers' => 'Creature legate al Piano Elementale del Fuoco.',
                'common' => false,
                'selectable' => false,
                'requires_dm_permission' => true,
                'sort_order' => 21,
                'description' => 'Dialetto del Primordiale associato all’elemento del fuoco.',
            ],

            //Terran
            [
                'key' => 'terran',
                'name' => 'Terran',
                'family' => 'Primordiale',
                'category' => 'dialect',
                'script_key' => 'dwarvish',
                'parent_key' => 'primordial',
                'typical_speakers' => 'Creature legate al Piano Elementale della Terra.',
                'common' => false,
                'selectable' => false,
                'requires_dm_permission' => true,
                'sort_order' => 22,
                'description' => 'Dialetto del Primordiale associato all’elemento della terra.',
            ],
        ];

        //Prima fase: inserisce tutte le lingue senza collegare i dialetti
        foreach ($languages as $language) {
            Language::updateOrCreate(
                //Identifica la lingua tramite la chiave stabile
                [
                    'key' => $language['key'],
                ],

                //Inserisce o aggiorna tutti i dati della lingua
                [
                    'name' => $language['name'],
                    'family' => $language['family'],
                    'common' => $language['common'],
                    'selectable' => $language['selectable'],
                    'description' => $language['description'],
                    'category' => $language['category'],
                    'parent_language_id' => null,
                    'language_script_id' =>
                        $language['script_key'] === null
                            ? null
                            : $scriptIds->get(
                                $language['script_key']
                            ),
                    'typical_speakers' =>
                        $language['typical_speakers'],
                    'requires_dm_permission' =>
                        $language['requires_dm_permission'],
                    'sort_order' => $language['sort_order'],
                ]
            );
        }

        //Crea una mappa tra chiave tecnica e ID delle lingue
        $languageIds = Language::query()
            ->pluck('id', 'key');

        //Seconda fase: collega ogni dialetto alla lingua principale
        foreach ($languages as $language) {
            //Ignora le lingue che non sono dialetti
            if ($language['parent_key'] === null) {
                continue;
            }

            //Aggiorna il collegamento del dialetto
            Language::query()
                ->where('key', $language['key'])
                ->update([
                    'parent_language_id' => $languageIds->get(
                        $language['parent_key']
                    ),
                ]);
        }
    }
}
