<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\LanguageScript;
use Illuminate\Database\Seeder;
use RuntimeException;

class LanguageSeeder extends Seeder
{
    //Crea il catalogo completo delle lingue del regolamento
    public function run(): void
    {
        //Crea prima gli alfabeti utilizzati dalle lingue
        $this->call(LanguageScriptSeeder::class);

        //Indicizza gli alfabeti tramite la loro chiave tecnica
        $scriptIds = LanguageScript::query()
            ->pluck('id', 'key');

        //Recupera tutte le definizioni linguistiche
        $languages = $this->languages();

        //Crea o aggiorna ogni lingua senza produrre duplicati
        foreach ($languages as $language) {
            $scriptId = null;

            //Recupera l'alfabeto quando è stato specificato
            if ($language['script_key'] !== null) {
                if (! $scriptIds->has($language['script_key'])) {
                    throw new RuntimeException(
                        "Alfabeto {$language['script_key']} non trovato."
                    );
                }

                $scriptId = $scriptIds->get(
                    $language['script_key']
                );
            }

            Language::query()->updateOrCreate(
                [
                    'key' => $language['key'],
                ],
                [
                    'name' => $language['name'],
                    'family' => $language['family'],
                    'category' => $language['category'],
                    'language_script_id' => $scriptId,
                    'parent_language_id' => null,
                    'typical_speakers' =>
                        $language['typical_speakers'],
                    'common' => $language['common'],
                    'selectable' => $language['selectable'],
                    'requires_dm_permission' =>
                        $language['requires_dm_permission'],
                    'sort_order' => $language['sort_order'],
                    'description' => $language['description'],
                ]
            );
        }

        //Indicizza le lingue appena create
        $languageIds = Language::query()
            ->pluck('id', 'key');

        //Collega i dialetti alla lingua principale
        foreach ($languages as $language) {
            if ($language['parent_key'] === null) {
                continue;
            }

            if (! $languageIds->has($language['parent_key'])) {
                throw new RuntimeException(
                    "Lingua principale "
                    . "{$language['parent_key']} non trovata."
                );
            }

            Language::query()
                ->where('key', $language['key'])
                ->update([
                    'parent_language_id' => $languageIds->get(
                        $language['parent_key']
                    ),
                ]);
        }
    }

    //Restituisce tutte le lingue nel loro ordine di visualizzazione
    private function languages(): array
    {
        $languages = array_merge(
            $this->standardLanguages(),
            $this->exoticLanguages(),
            $this->secretLanguages(),
            $this->primordialDialects(),
            $this->specialLanguages()
        );

        //Assegna automaticamente un ordine progressivo
        foreach ($languages as $index => $language) {
            $languages[$index] = array_merge(
                [
                    'family' => null,
                    'script_key' => null,
                    'parent_key' => null,
                    'common' => false,
                    'selectable' => false,
                    'requires_dm_permission' => true,
                ],
                $language,
                [
                    'sort_order' => $index + 1,
                ]
            );
        }

        return $languages;
    }

    //Restituisce le otto lingue standard
    private function standardLanguages(): array
    {
        return [
            [
                'key' => 'common',
                'name' => 'Comune',
                'category' => 'standard',
                'script_key' => 'common',
                'typical_speakers' =>
                    'Umani e numerosi popoli civilizzati.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'description' =>
                    'Lingua ampiamente utilizzata per comunicare '
                    . 'tra popoli e culture differenti.',
            ],
            [
                'key' => 'dwarvish',
                'name' => 'Nanico',
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'typical_speakers' => 'Nani.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'description' =>
                    'Lingua tradizionale dei nani, normalmente '
                    . 'scritta usando l’alfabeto Nanico.',
            ],
            [
                'key' => 'elvish',
                'name' => 'Elfico',
                'category' => 'standard',
                'script_key' => 'elvish',
                'typical_speakers' => 'Elfi.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'description' =>
                    'Lingua tradizionale degli elfi, normalmente '
                    . 'scritta usando l’alfabeto Elfico.',
            ],
            [
                'key' => 'giant',
                'name' => 'Gigante',
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'typical_speakers' => 'Giganti e ogre.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'description' =>
                    'Lingua impiegata dai giganti e dai popoli '
                    . 'culturalmente legati a essi.',
            ],
            [
                'key' => 'gnomish',
                'name' => 'Gnomesco',
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'typical_speakers' => 'Gnomi.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'description' =>
                    'Lingua degli gnomi, normalmente scritta '
                    . 'usando l’alfabeto Nanico.',
            ],
            [
                'key' => 'goblin',
                'name' => 'Goblin',
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'typical_speakers' => 'Goblinoidi.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'description' =>
                    'Lingua condivisa da numerosi popoli '
                    . 'appartenenti alle culture goblinoidi.',
            ],
            [
                'key' => 'halfling',
                'name' => 'Halfling',
                'category' => 'standard',
                'script_key' => 'common',
                'typical_speakers' => 'Halfling.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'description' =>
                    'Lingua tradizionale degli halfling, scritta '
                    . 'normalmente usando l’alfabeto Comune.',
            ],
            [
                'key' => 'orc',
                'name' => 'Orchesco',
                'category' => 'standard',
                'script_key' => 'dwarvish',
                'typical_speakers' => 'Orchi.',
                'common' => true,
                'selectable' => true,
                'requires_dm_permission' => false,
                'description' =>
                    'Lingua tradizionalmente utilizzata dagli '
                    . 'orchi e da alcune culture affini.',
            ],
        ];
    }

    //Restituisce le otto lingue esotiche normalmente selezionabili
    private function exoticLanguages(): array
    {
        return [
            [
                'key' => 'abyssal',
                'name' => 'Abissale',
                'category' => 'exotic',
                'script_key' => 'infernal',
                'typical_speakers' => 'Demoni.',
                'selectable' => true,
                'description' =>
                    'Lingua delle creature demoniache legate '
                    . 'all’Abisso.',
            ],
            [
                'key' => 'celestial',
                'name' => 'Celestiale',
                'category' => 'exotic',
                'script_key' => 'celestial',
                'typical_speakers' => 'Celestiali.',
                'selectable' => true,
                'description' =>
                    'Lingua associata agli esseri originari '
                    . 'dei Piani Superiori.',
            ],
            [
                'key' => 'draconic',
                'name' => 'Draconico',
                'category' => 'exotic',
                'script_key' => 'draconic',
                'typical_speakers' => 'Draghi e dragonidi.',
                'selectable' => true,
                'description' =>
                    'Antica lingua associata ai draghi e alle '
                    . 'creature di discendenza draconica.',
            ],
            [
                'key' => 'deep_speech',
                'name' => 'Gergo delle Profondità',
                'category' => 'exotic',
                'typical_speakers' =>
                    'Aboleth e aberrazioni delle profondità.',
                'selectable' => true,
                'description' =>
                    'Lingua aliena impiegata da alcune aberrazioni '
                    . 'e creature provenienti da luoghi remoti.',
            ],
            [
                'key' => 'infernal',
                'name' => 'Infernale',
                'category' => 'exotic',
                'script_key' => 'infernal',
                'typical_speakers' => 'Diavoli.',
                'selectable' => true,
                'description' =>
                    'Lingua formale e strutturata associata ai '
                    . 'diavoli e ai Nove Inferi.',
            ],
            [
                'key' => 'primordial',
                'name' => 'Primordiale',
                'family' => 'Primordiale',
                'category' => 'exotic',
                'script_key' => 'dwarvish',
                'typical_speakers' => 'Elementali.',
                'selectable' => true,
                'description' =>
                    'Famiglia linguistica degli elementali che '
                    . 'comprende Auran, Aquan, Ignan e Terran.',
            ],
            [
                'key' => 'sylvan',
                'name' => 'Silvano',
                'category' => 'exotic',
                'script_key' => 'elvish',
                'typical_speakers' => 'Creature fatate.',
                'selectable' => true,
                'description' =>
                    'Lingua delle creature fatate, normalmente '
                    . 'scritta usando l’alfabeto Elfico.',
            ],
            [
                'key' => 'undercommon',
                'name' => 'Sottocomune',
                'category' => 'exotic',
                'script_key' => 'elvish',
                'typical_speakers' =>
                    'Mercanti e popoli del Sottosuolo.',
                'selectable' => true,
                'description' =>
                    'Lingua commerciale diffusa nel Sottosuolo '
                    . 'tra popoli di origini differenti.',
            ],
        ];
    }

    //Restituisce le lingue segrete legate alle classi
    private function secretLanguages(): array
    {
        return [
            [
                'key' => 'druidic',
                'name' => 'Druidico',
                'category' => 'secret',
                'typical_speakers' => 'Druidi.',
                'description' =>
                    'Lingua segreta tramandata tra i druidi e '
                    . 'utilizzata anche per lasciare messaggi nascosti.',
            ],
            [
                'key' => 'thieves_cant',
                'name' => 'Gergo Ladresco',
                'category' => 'secret',
                'typical_speakers' =>
                    'Ladri addestrati nel gergo.',
                'description' =>
                    'Sistema di parole, segni e simboli utilizzato '
                    . 'per nascondere informazioni nelle conversazioni.',
            ],
        ];
    }

    //Restituisce i quattro dialetti del Primordiale
    private function primordialDialects(): array
    {
        return [
            [
                'key' => 'auran',
                'name' => 'Auran',
                'family' => 'Primordiale',
                'category' => 'dialect',
                'script_key' => 'dwarvish',
                'parent_key' => 'primordial',
                'typical_speakers' =>
                    'Creature legate all’elemento dell’aria.',
                'description' =>
                    'Dialetto del Primordiale associato '
                    . 'all’elemento dell’aria.',
            ],
            [
                'key' => 'aquan',
                'name' => 'Aquan',
                'family' => 'Primordiale',
                'category' => 'dialect',
                'script_key' => 'dwarvish',
                'parent_key' => 'primordial',
                'typical_speakers' =>
                    'Creature legate all’elemento dell’acqua.',
                'description' =>
                    'Dialetto del Primordiale associato '
                    . 'all’elemento dell’acqua.',
            ],
            [
                'key' => 'ignan',
                'name' => 'Ignan',
                'family' => 'Primordiale',
                'category' => 'dialect',
                'script_key' => 'dwarvish',
                'parent_key' => 'primordial',
                'typical_speakers' =>
                    'Creature legate all’elemento del fuoco.',
                'description' =>
                    'Dialetto del Primordiale associato '
                    . 'all’elemento del fuoco.',
            ],
            [
                'key' => 'terran',
                'name' => 'Terran',
                'family' => 'Primordiale',
                'category' => 'dialect',
                'script_key' => 'dwarvish',
                'parent_key' => 'primordial',
                'typical_speakers' =>
                    'Creature legate all’elemento della terra.',
                'description' =>
                    'Dialetto del Primordiale associato '
                    . 'all’elemento della terra.',
            ],
        ];
    }

    //Restituisce le lingue speciali di razze e creature
    private function specialLanguages(): array
    {
        $definitions = [
            [
                'aarakocra',
                'Aarakocra',
                'Aarakocra.',
                'Lingua propria del popolo degli aarakocra.',
            ],
            [
                'blink_dog',
                'Cane Intermittente',
                'Cani intermittenti.',
                'Lingua utilizzata dai cani intermittenti.',
            ],
            [
                'bullywug',
                'Bullywug',
                'Bullywug.',
                'Lingua utilizzata dalle comunità bullywug.',
            ],
            [
                'deep_crow',
                'Corvo delle Profondità',
                'Corvi delle profondità.',
                'Lingua delle creature note come corvi delle profondità.',
            ],
            [
                'giant_eagle',
                'Aquila Gigante',
                'Aquile giganti.',
                'Lingua impiegata dalle aquile giganti.',
            ],
            [
                'giant_elk',
                'Alce Gigante',
                'Alci giganti.',
                'Lingua impiegata dagli alci giganti.',
            ],
            [
                'giant_owl',
                'Gufo Gigante',
                'Gufi giganti.',
                'Lingua impiegata dai gufi giganti.',
            ],
            [
                'gith',
                'Gith',
                'Githyanki e githzerai.',
                'Lingua condivisa dai principali popoli gith.',
            ],
            [
                'gnoll',
                'Gnoll',
                'Gnoll.',
                'Lingua utilizzata dalle tribù e dai clan gnoll.',
            ],
            [
                'grell',
                'Grell',
                'Grell.',
                'Lingua aliena utilizzata dai grell.',
            ],
            [
                'grung',
                'Grung',
                'Grung.',
                'Lingua impiegata dal popolo dei grung.',
            ],
            [
                'hook_horror',
                'Orrore Uncinato',
                'Orrori uncinati.',
                'Sistema linguistico degli orrori uncinati.',
            ],
            [
                'ice_toad',
                'Rospo dei Ghiacci',
                'Rospi dei ghiacci.',
                'Lingua utilizzata dai rospi dei ghiacci.',
            ],
            [
                'ixitxachitl',
                'Ixitxachitl',
                'Ixitxachitl.',
                'Lingua acquatica degli ixitxachitl.',
            ],
            [
                'kruthik',
                'Kruthik',
                'Kruthik.',
                'Lingua o sistema comunicativo dei kruthik.',
            ],
            [
                'leonin',
                'Leonin',
                'Leonin.',
                'Lingua propria delle comunità leonin.',
            ],
            [
                'loxodon',
                'Loxodon',
                'Loxodon.',
                'Lingua propria del popolo dei loxodon.',
            ],
            [
                'minotaur',
                'Minotauro',
                'Minotauri.',
                'Lingua utilizzata dalle culture dei minotauri.',
            ],
            [
                'modron',
                'Modron',
                'Modron.',
                'Lingua ordinata delle creature di Mechanus.',
            ],
            [
                'otyugh',
                'Otyugh',
                'Otyugh.',
                'Lingua utilizzata dagli otyugh.',
            ],
            [
                'quori',
                'Quori',
                'Quori, kalashtar e ispirati.',
                'Lingua associata ai quori e ai loro ospiti.',
            ],
            [
                'sahuagin',
                'Sahuagin',
                'Sahuagin.',
                'Lingua delle comunità sahuagin.',
            ],
            [
                'slaad',
                'Slaad',
                'Slaad.',
                'Lingua utilizzata dalle creature slaad.',
            ],
            [
                'sphinx',
                'Sfinge',
                'Sfingi.',
                'Lingua antica impiegata dalle sfingi.',
            ],
            [
                'thri_kreen',
                'Thri-kreen',
                'Thri-kreen.',
                'Lingua tradizionale del popolo thri-kreen.',
            ],
            [
                'tlincalli',
                'Tlincalli',
                'Tlincalli.',
                'Lingua utilizzata dalle creature tlincalli.',
            ],
            [
                'troglodyte',
                'Troglodita',
                'Trogloditi.',
                'Lingua utilizzata dalle comunità troglodite.',
            ],
            [
                'umber_hulk',
                'Umber Hulk',
                'Umber hulk.',
                'Lingua propria degli umber hulk.',
            ],
            [
                'vegepygmy',
                'Vegepigmeo',
                'Vegepigmei.',
                'Sistema linguistico delle comunità vegepigmee.',
            ],
            [
                'vedalken',
                'Vedalken',
                'Vedalken.',
                'Lingua propria del popolo vedalken.',
            ],
            [
                'winter_wolf',
                'Lupo Invernale',
                'Lupi invernali.',
                'Lingua utilizzata dai lupi invernali.',
            ],
            [
                'worg',
                'Worg',
                'Worg.',
                'Lingua utilizzata dai worg.',
            ],
            [
                'yikaria',
                'Yikaria',
                'Yakfolk.',
                'Lingua tradizionale del popolo degli yakfolk.',
            ],
            [
                'yeti',
                'Yeti',
                'Yeti.',
                'Lingua utilizzata dagli yeti.',
            ],
        ];

        $languages = [];

        foreach ($definitions as [
            $key,
            $name,
            $typicalSpeakers,
            $description,
        ]) {
            $languages[] = [
                'key' => $key,
                'name' => $name,
                'category' => 'exotic',
                'typical_speakers' => $typicalSpeakers,
                'selectable' => false,
                'description' => $description,
            ];
        }

        return $languages;
    }
}
