<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use App\Models\SourceBook;
use Illuminate\Database\Seeder;

class OptionalRuleSeeder extends Seeder
{
    //Inserisce le regole opzionali supportate dall'applicazione
    public function run(): void
    {
        //Recupera il regolamento a cui appartiene la regola
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera il manuale che introduce la regola
        $tashasCauldron = SourceBook::query()
            ->where('slug', 'tcoe-2020')
            ->firstOrFail();

        //Crea o aggiorna la personalizzazione dell'origine
        $customizeOrigin = $ruleset
            ->optionalRules()
            ->updateOrCreate(
                //Identifica la regola tramite la chiave stabile
                [
                    'key' => 'customize_origin',
                ],

                //Inserisce o aggiorna i dati della regola
                [
                    'name' =>
                        'Personalizzazione dell’origine',
                    'category' => 'character_creation',
                    'description' =>
                        'Permette di assegnare gli incrementi '
                        . 'di caratteristica razziali a caratteristiche '
                        . 'differenti, conservando il valore di ogni '
                        . 'incremento e utilizzando destinazioni distinte.',
                    'default_enabled' => false,
                    'is_active' => true,
                    'sort_order' => 10,
                    'notes' =>
                        'La regola deve essere abilitata dalla campagna. '
                        . 'Non permette di superare il punteggio massimo '
                        . 'previsto dal regolamento.',
                ]
            );

        //Collega la regola al manuale di Tasha
        $customizeOrigin
            ->sourceReferences()
            ->updateOrCreate(
                //Identifica stabilmente il riferimento
                [
                    'key' =>
                        'tcoe_2020_customize_origin',
                ],

                //Inserisce o aggiorna i dati del riferimento
                [
                    'source_book_id' => $tashasCauldron->id,
                    'reference_type' => 'definition',
                    'page_start' => 7,
                    'page_end' => 8,
                    'section' =>
                        'Capitolo 1: Opzioni per i personaggi — '
                        . 'Personalizzare la propria origine',
                    'notes' =>
                        'La numerazione delle pagine fa riferimento '
                        . 'all’edizione originale del manuale.',
                    'is_primary' => true,
                    'sort_order' => 1,

                    //Il testo ufficiale non viene inserito nei seeder
                    'official_text' => null,
                ]
            );
    }
}
