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
                'data/tcoe_2020_cantrips.php',
            ],
        ]);
    }
}
