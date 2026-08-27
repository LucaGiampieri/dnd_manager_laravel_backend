<?php

namespace Database\Seeders;

class PlayerHandbookSpellSeeder extends OfficialSpellSeeder
{
    //Mantiene identità, versione e riferimenti del catalogo PHB 2014.
    //L'importatore condiviso salva anche effects e le relative formule.
    public function run(): void
    {
        $this->seedOfficialSpellCatalog([
            'source_book_slug' => 'phb-2014',
            'version_key' => 'phb_2014',
            'reference_key' => 'phb_2014_it_spell_definition',
            'section' => 'Capitolo 11: Incantesimi',
            'source_notes' => 'Riferimento bibliografico alla '
                . 'versione italiana del Manuale del Giocatore 2014.',
            'data_files' => [
                //Trucchetti
                'data/phb_2014_cantrips.php',
                //Incantesimi di 1° livello
                'data/phb_2014_level_1_spells.php',
                //Incantesimi di 2° livello
                'data/phb_2014_level_2_spells.php',
                //Incantesimi di 3° livello
                'data/phb_2014_level_3_spells.php',
                //Incantesimi di 4° livello
                'data/phb_2014_level_4_spells.php',
                //Incantesimi di 5° livello
                'data/phb_2014_level_5_spells.php',
                //Incantesimi di 6° livello
                'data/phb_2014_level_6_spells.php',
                //Incantesimi di 7° livello
                'data/phb_2014_level_7_spells.php',
                //Incantesimi di 8° livello
                'data/phb_2014_level_8_spells.php',
                //Incantesimi di 9° livello
                'data/phb_2014_level_9_spells.php',
            ],
        ]);
    }
}
