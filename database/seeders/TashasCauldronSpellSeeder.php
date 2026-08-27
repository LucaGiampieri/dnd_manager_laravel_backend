<?php

namespace Database\Seeders;

class TashasCauldronSpellSeeder extends OfficialSpellSeeder
{
    //Inserisce gli incantesimi del Calderone Omnicomprensivo di Tasha
    public function run(): void
    {
        $this->seedOfficialSpellCatalog([
            'source_book_slug' => 'tcoe-2020',
            'version_key' => 'tcoe_2020',
            'reference_key' => 'tcoe_2020_it_spell_definition',
            'section' => 'Capitolo 3: Selezione Magica',
            'source_notes' => 'Riferimento bibliografico alla '
                . 'versione italiana del Calderone '
                . 'Omnicomprensivo di Tasha.',
            'data_files' => [
                //Carica i 5 trucchetti introdotti da Tasha
                'data/tcoe_2020_cantrips.php',

                //Carica l'unico incantesimo di 1° livello
                'data/tcoe_2020_level_1_spells.php',

                //Carica i 2 incantesimi di 2° livello
                'data/tcoe_2020_level_2_spells.php',

                //Carica i 5 incantesimi di 3° livello
                'data/tcoe_2020_level_3_spells.php',

                //Carica i 3 incantesimi di 4° livello
                'data/tcoe_2020_level_4_spells.php',

                //Carica l'unico incantesimo di 5° livello
                'data/tcoe_2020_level_5_spells.php',

                //Carica i 2 incantesimi di 6° livello
                'data/tcoe_2020_level_6_spells.php',

                //Carica l'unico incantesimo di 7° livello
                'data/tcoe_2020_level_7_spells.php',

                //Tasha non introduce incantesimi di 8° livello

                //Carica l'unico incantesimo di 9° livello
                'data/tcoe_2020_level_9_spells.php',
            ],
        ]);
    }
}
