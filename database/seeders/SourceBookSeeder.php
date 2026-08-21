<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class SourceBookSeeder extends Seeder
{
    //Inserisce i principali manuali utilizzati dal regolamento
    public function run(): void
    {
        //Recupera il regolamento a cui appartengono i manuali
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Definisce i valori condivisi dalle fonti ufficiali
        $commonData = [
            'edition' => '5e',
            'language' => 'it',
            'publisher' => 'Wizards of the Coast',
            'is_official' => true,
            'is_playtest' => false,
            'is_active' => true,
        ];

        //Definisce i principali manuali del regolamento 2014
        $sourceBooks = [
            /*
             * Manuali fondamentali
             */

            //Manuale del Giocatore
            [
                'title' => 'Manuale del Giocatore',
                'original_title' => 'Player’s Handbook',
                'slug' => 'phb-2014',
                'abbreviation' => 'PHB',
                'type' => 'core_rulebook',
                'notes' =>
                    'Contiene le regole principali per la creazione '
                    . 'e la gestione dei personaggi.',
            ],

            //Guida del Dungeon Master
            [
                'title' => 'Guida del Dungeon Master',
                'original_title' => 'Dungeon Master’s Guide',
                'slug' => 'dmg-2014',
                'abbreviation' => 'DMG',
                'type' => 'core_rulebook',
                'notes' =>
                    'Contiene strumenti, regole opzionali e indicazioni '
                    . 'per la gestione delle campagne.',
            ],

            //Manuale dei Mostri
            [
                'title' => 'Manuale dei Mostri',
                'original_title' => 'Monster Manual',
                'slug' => 'mm-2014',
                'abbreviation' => 'MM',
                'type' => 'core_rulebook',
                'notes' =>
                    'Contiene creature e blocchi statistiche '
                    . 'utilizzabili nelle avventure.',
            ],

            /*
             * Supplementi generali e raccolte di regole
             */

            //Guida degli Avventurieri alla Costa della Spada
            [
                'title' =>
                    'Guida degli Avventurieri alla Costa della Spada',
                'original_title' =>
                    'Sword Coast Adventurer’s Guide',
                'slug' => 'scag-2015',
                'abbreviation' => 'SCAG',
                'type' => 'setting',
                'notes' =>
                    'Introduce opzioni per razze, sottoclassi, '
                    . 'background e ambientazione.',
            ],

            //Guida ai Mostri di Volo
            [
                'title' => 'Guida ai Mostri di Volo',
                'original_title' => 'Volo’s Guide to Monsters',
                'slug' => 'vgm-2016',
                'abbreviation' => 'VGM',
                'type' => 'supplement',
                'notes' =>
                    'Contiene creature, approfondimenti e razze '
                    . 'giocabili successivamente aggiornate.',
            ],

            //Guida Omnicomprensiva di Xanathar
            [
                'title' => 'Guida Omnicomprensiva di Xanathar',
                'original_title' =>
                    'Xanathar’s Guide to Everything',
                'slug' => 'xgte-2017',
                'abbreviation' => 'XGtE',
                'type' => 'supplement',
                'notes' =>
                    'Contiene sottoclassi, incantesimi, talenti '
                    . 'e regole opzionali.',
            ],

            //Il Tomo dei Nemici di Mordenkainen
            [
                'title' =>
                    'Il Tomo dei Nemici di Mordenkainen',
                'original_title' =>
                    'Mordenkainen’s Tome of Foes',
                'slug' => 'mtf-2018',
                'abbreviation' => 'MTF',
                'type' => 'supplement',
                'notes' =>
                    'Contiene creature, razze e approfondimenti '
                    . 'successivamente aggiornati.',
            ],

            //Il Calderone Omnicomprensivo di Tasha
            [
                'title' =>
                    'Il Calderone Omnicomprensivo di Tasha',
                'original_title' =>
                    'Tasha’s Cauldron of Everything',
                'slug' => 'tcoe-2020',
                'abbreviation' => 'TCoE',
                'type' => 'supplement',
                'notes' =>
                    'Contiene opzioni aggiuntive e regole opzionali, '
                    . 'tra cui la personalizzazione dell’origine.',
            ],

            //Guida di Van Richten a Ravenloft
            [
                'title' => 'Guida di Van Richten a Ravenloft',
                'original_title' =>
                    'Van Richten’s Guide to Ravenloft',
                'slug' => 'vrgtr-2021',
                'abbreviation' => 'VRGtR',
                'type' => 'setting',
                'notes' =>
                    'Contiene opzioni per personaggi, creature '
                    . 'e regole legate ai Domini del Terrore.',
            ],

            //Il Tesoro dei Draghi di Fizban
            [
                'title' => 'Il Tesoro dei Draghi di Fizban',
                'original_title' =>
                    'Fizban’s Treasury of Dragons',
                'slug' => 'ftod-2021',
                'abbreviation' => 'FToD',
                'type' => 'supplement',
                'notes' =>
                    'Contiene opzioni draconiche, talenti, incantesimi '
                    . 'e creature legate ai draghi.',
            ],

            //Mostri del Multiverso
            [
                'title' =>
                    'Mordenkainen presenta: Mostri del Multiverso',
                'original_title' =>
                    'Mordenkainen Presents: Monsters of the Multiverse',
                'slug' => 'mpmm-2022',
                'abbreviation' => 'MPMM',
                'type' => 'supplement',
                'notes' =>
                    'Raccoglie e aggiorna numerose razze giocabili '
                    . 'e creature pubblicate in precedenza.',
            ],

            //La Gloria dei Giganti
            [
                'title' =>
                    'Bigby presenta: La Gloria dei Giganti',
                'original_title' =>
                    'Bigby Presents: Glory of the Giants',
                'slug' => 'bgg-2023',
                'abbreviation' => 'BGG',
                'type' => 'supplement',
                'notes' =>
                    'Contiene opzioni, oggetti, ambientazioni '
                    . 'e creature legate ai giganti.',
            ],

            //Il Libro delle Molte Cose
            [
                'title' => 'Il Libro delle Molte Cose',
                'original_title' => 'The Book of Many Things',
                'slug' => 'bmt-2023',
                'abbreviation' => 'BMT',
                'type' => 'supplement',
                'notes' =>
                    'Contiene opzioni e strumenti ispirati '
                    . 'al Mazzo delle Molte Cose.',
            ],

            /*
             * Manuali di ambientazione con opzioni per i personaggi
             */

            //Eberron
            [
                'title' =>
                    'Eberron: Rinascita dopo l’Ultima Guerra',
                'original_title' =>
                    'Eberron: Rising from the Last War',
                'slug' => 'erlw-2019',
                'abbreviation' => 'ERLW',
                'type' => 'setting',
                'notes' =>
                    'Contiene razze, Marchi del Drago, oggetti, '
                    . 'creature e l’ambientazione di Eberron.',
            ],

            //Wildemount
            [
                'title' =>
                    'Guida dell’Esploratore a Wildemount',
                'original_title' =>
                    'Explorer’s Guide to Wildemount',
                'slug' => 'egtw-2020',
                'abbreviation' => 'EGtW',
                'type' => 'setting',
                'notes' =>
                    'Contiene razze, sottoclassi, incantesimi, '
                    . 'creature e l’ambientazione di Wildemount.',
            ],

            //Ravnica
            [
                'title' =>
                    'Guida dei Guildmaster a Ravnica',
                'original_title' =>
                    'Guildmasters’ Guide to Ravnica',
                'slug' => 'ggtr-2018',
                'abbreviation' => 'GGtR',
                'type' => 'setting',
                'notes' =>
                    'Contiene razze, background, oggetti, '
                    . 'creature e regole legate alle gilde.',
            ],

            //Theros
            [
                'title' => 'Odissee Mitiche di Theros',
                'original_title' =>
                    'Mythic Odysseys of Theros',
                'slug' => 'moot-2020',
                'abbreviation' => 'MOoT',
                'type' => 'setting',
                'notes' =>
                    'Contiene razze, sottoclassi, doni soprannaturali '
                    . 'e regole legate alle divinità.',
            ],

            //Strixhaven
            [
                'title' => 'Strixhaven: Un Curriculum di Caos',
                'original_title' =>
                    'Strixhaven: A Curriculum of Chaos',
                'slug' => 'scc-2021',
                'abbreviation' => 'SCC',
                'type' => 'setting',
                'notes' =>
                    'Contiene background, talenti, incantesimi '
                    . 'e regole per un’accademia magica.',
            ],

            //Spelljammer
            [
                'title' =>
                    'Spelljammer: Avventure nello Spazio',
                'original_title' =>
                    'Spelljammer: Adventures in Space',
                'slug' => 'sais-2022',
                'abbreviation' => 'SAiS',
                'type' => 'setting',
                'notes' =>
                    'Contiene razze, incantesimi, oggetti, navi '
                    . 'e creature per avventure nello spazio.',
            ],

            //Planescape
            [
                'title' =>
                    'Planescape: Avventure nel Multiverso',
                'original_title' =>
                    'Planescape: Adventures in the Multiverse',
                'slug' => 'paitm-2023',
                'abbreviation' => 'PAitM',
                'type' => 'setting',
                'notes' =>
                    'Contiene opzioni, creature e regole '
                    . 'per le avventure attraverso i piani.',
            ],

            //Dragonlance
            [
                'title' =>
                    'Dragonlance: L’Ombra della Regina dei Draghi',
                'original_title' =>
                    'Dragonlance: Shadow of the Dragon Queen',
                'slug' => 'dsotdq-2022',
                'abbreviation' => 'DSotDQ',
                'type' => 'adventure',
                'notes' =>
                    'Contiene background, talenti e opzioni '
                    . 'utilizzabili nell’ambientazione Dragonlance.',
            ],

            /*
             * Accessori con contenuti per i personaggi
             */

            //Compendio del Giocatore del Male Elementale
            [
                'title' =>
                    'Compendio del Giocatore del Male Elementale',
                'original_title' =>
                    'Elemental Evil Player’s Companion',
                'slug' => 'eepc-2015',
                'abbreviation' => 'EEPC',
                'type' => 'accessory',
                'notes' =>
                    'Contiene razze e incantesimi collegati '
                    . 'al tema del Male Elementale.',
            ],
        ];

        //Inserisce o aggiorna ogni manuale usando lo slug stabile
        foreach ($sourceBooks as $sourceBook) {
            //Aggiunge i valori comuni ai dati specifici del manuale
            $sourceBookData = array_merge(
                $commonData,
                $sourceBook
            );

            //Inserisce o aggiorna il manuale
            $ruleset->sourceBooks()->updateOrCreate(
                //Identifica univocamente il manuale
                [
                    'slug' => $sourceBook['slug'],
                ],

                //Inserisce o aggiorna tutti i dati del manuale
                $sourceBookData
            );
        }
    }
}
