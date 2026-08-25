<?php

namespace Database\Seeders;

class XanatharsGuideSpellSeeder extends OfficialSpellSeeder
{
    //Inserisce gli incantesimi della Guida Omnicomprensiva di Xanathar
    public function run(): void
    {
        $this->seedOfficialSpellCatalog([
            'source_book_slug' => 'xgte-2017',
            'version_key' => 'xgte_2017',
            'reference_key' => 'xgte_2017_it_spell_definition',
            'section' => 'Capitolo 3: Incantesimi',
            'source_notes' => 'Riferimento bibliografico alla '
                . 'versione italiana della Guida Omnicomprensiva '
                . 'di Xanathar.',
            'data_files' => [
                //Carica i trucchetti di Xanathar
                'data/xgte_2017_cantrips.php',

                //Carica gli incantesimi di 1° livello
                'data/xgte_2017_level_1_spells.php',

                //Carica gli incantesimi di 2° livello
                'data/xgte_2017_level_2_spells.php',

                //Carica gli incantesimi di 3° livello
                'data/xgte_2017_level_3_spells.php',

                //Carica gli incantesimi di 4° livello
                'data/xgte_2017_level_4_spells.php',

                //Carica gli incantesimi di 5° livello
                'data/xgte_2017_level_5_spells.php',

                //Carica gli incantesimi di 6° livello
                'data/xgte_2017_level_6_spells.php',

                //Carica gli incantesimi di 7° livello
                'data/xgte_2017_level_7_spells.php',

                //Carica gli incantesimi di 8° livello
                'data/xgte_2017_level_8_spells.php',

                //Carica gli incantesimi di 9° livello
                'data/xgte_2017_level_9_spells.php',
            ],
        ]);
    }
}
