<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\SourceBook;
use App\Models\Subrace;
use Illuminate\Database\Seeder;

class RaceSourceReferenceSeeder extends Seeder
{
    //Chiave tecnica condivisa dal riferimento principale
    private const REFERENCE_KEY =
        'phb_2014_it_primary_rules';

    //Collega razze e sottorazze alle pagine del manuale
    public function run(): void
    {
        //Crea prima le razze e il manuale richiesti
        //anche quando questo seeder viene eseguito singolarmente
        $this->call([
            RaceSeeder::class,
            SourceBookSeeder::class,
        ]);

        //Recupera l'edizione italiana del Manuale del Giocatore
        $playerHandbook = SourceBook::query()
            ->where('slug', 'phb-2014')
            ->firstOrFail();

        //Collega ogni razza alla sua sezione principale
        foreach (
            $this->raceReferences() as $raceKey => $referenceData
        ) {
            //Recupera esclusivamente la versione PHB 2014
            $race = Race::query()
                ->where('key', $raceKey)
                ->where('version_key', 'phb_2014')
                ->firstOrFail();

            //Crea o aggiorna il riferimento bibliografico
            $this->syncReference(
                $race,
                $playerHandbook,
                $referenceData
            );
        }

        //Collega ogni sottorazza alla sua sezione specifica
        foreach (
            $this->subraceReferences() as $subraceKey => $referenceData
        ) {
            //Recupera esclusivamente la versione PHB 2014
            $subrace = Subrace::query()
                ->where('key', $subraceKey)
                ->where('version_key', 'phb_2014')
                ->firstOrFail();

            //Crea o aggiorna il riferimento bibliografico
            $this->syncReference(
                $subrace,
                $playerHandbook,
                $referenceData
            );
        }
    }

    //Crea o aggiorna il riferimento di una razza o sottorazza
    private function syncReference(
        Race|Subrace $content,
        SourceBook $sourceBook,
        array $referenceData
    ): void {
        $content->sourceReferences()->updateOrCreate(
            [
                'key' => self::REFERENCE_KEY,
            ],
            [
                //Collega il contenuto al manuale corretto
                'source_book_id' => $sourceBook->id,

                //Indica che il manuale definisce completamente il contenuto
                'reference_type' => 'definition',

                //Registra l'intervallo delle pagine
                'page_start' => $referenceData['page_start'],
                'page_end' => $referenceData['page_end'],

                //Registra il nome della sezione
                'section' => $referenceData['section'],

                //Indica che questa è la fonte principale
                'is_primary' => true,
                'sort_order' => 10,

                //Il testo ufficiale non viene inserito nei seeder pubblici
                'official_text' => null,
                'notes' =>
                    'Riferimento bibliografico al Manuale '
                    . 'del Giocatore italiano del 2014.',
            ]
        );
    }

    //Restituisce le pagine delle razze principali
    private function raceReferences(): array
    {
        return [
            'dwarf' => [
                'page_start' => 26,
                'page_end' => 28,
                'section' => 'Capitolo 2: Razze - Nano',
            ],
            'elf' => [
                'page_start' => 18,
                'page_end' => 22,
                'section' => 'Capitolo 2: Razze - Elfo',
            ],
            'halfling' => [
                'page_start' => 23,
                'page_end' => 25,
                'section' => 'Capitolo 2: Razze - Halfling',
            ],
            'human' => [
                'page_start' => 29,
                'page_end' => 31,
                'section' => 'Capitolo 2: Razze - Umano',
            ],
            'dragonborn' => [
                'page_start' => 32,
                'page_end' => 34,
                'section' => 'Capitolo 2: Razze - Dragonide',
            ],
            'gnome' => [
                'page_start' => 35,
                'page_end' => 37,
                'section' => 'Capitolo 2: Razze - Gnomo',
            ],
            'half_elf' => [
                'page_start' => 38,
                'page_end' => 39,
                'section' => 'Capitolo 2: Razze - Mezzelfo',
            ],
            'half_orc' => [
                'page_start' => 40,
                'page_end' => 41,
                'section' => 'Capitolo 2: Razze - Mezzorco',
            ],
            'tiefling' => [
                'page_start' => 42,
                'page_end' => 43,
                'section' => 'Capitolo 2: Razze - Tiefling',
            ],
        ];
    }

    //Restituisce le pagine delle sottorazze e varianti
    private function subraceReferences(): array
    {
        return [
            'hill_dwarf' => [
                'page_start' => 28,
                'page_end' => 28,
                'section' =>
                    'Capitolo 2: Razze - Nano delle Colline',
            ],
            'mountain_dwarf' => [
                'page_start' => 28,
                'page_end' => 28,
                'section' =>
                    'Capitolo 2: Razze - Nano delle Montagne',
            ],
            'high_elf' => [
                'page_start' => 20,
                'page_end' => 21,
                'section' =>
                    'Capitolo 2: Razze - Elfo Alto',
            ],
            'wood_elf' => [
                'page_start' => 22,
                'page_end' => 22,
                'section' =>
                    'Capitolo 2: Razze - Elfo dei Boschi',
            ],
            'drow' => [
                'page_start' => 22,
                'page_end' => 22,
                'section' =>
                    'Capitolo 2: Razze - Elfo Oscuro (Drow)',
            ],
            'lightfoot_halfling' => [
                'page_start' => 25,
                'page_end' => 25,
                'section' =>
                    'Capitolo 2: Razze - Halfling Piedelesto',
            ],
            'stout_halfling' => [
                'page_start' => 25,
                'page_end' => 25,
                'section' =>
                    'Capitolo 2: Razze - Halfling Tozzo',
            ],
            'variant_human' => [
                'page_start' => 31,
                'page_end' => 31,
                'section' =>
                    'Capitolo 2: Razze - Tratti Umani Alternativi',
            ],
            'forest_gnome' => [
                'page_start' => 37,
                'page_end' => 37,
                'section' =>
                    'Capitolo 2: Razze - Gnomo delle Foreste',
            ],
            'rock_gnome' => [
                'page_start' => 37,
                'page_end' => 37,
                'section' =>
                    'Capitolo 2: Razze - Gnomo delle Rocce',
            ],
        ];
    }
}
