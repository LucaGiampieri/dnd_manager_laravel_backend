<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\SourceBook;
use App\Models\Subrace;
use Illuminate\Database\Seeder;
use RuntimeException;

class ElementalEvilRaceSourceReferenceSeeder extends Seeder
{
    //Chiave condivisa dal riferimento principale
    //alla versione italiana dell'EEPC
    private const REFERENCE_KEY =
        'eepc_2015_it_primary_rules';

    //Collega razze e sottorazze alle pagine del manuale
    public function run(): void
    {
        //Crea prima il manuale e i contenuti da collegare
        $this->call([
            //Il manuale deve essere collegato a un regolamento esistente
            RulesetSeeder::class,

            //Crea il manuale EEPC
            SourceBookSeeder::class,

            //Crea le razze da collegare al manuale
            ElementalEvilRaceSeeder::class,
]);

        //Recupera il Compendio del Giocatore
        //del Male Elementale
        $sourceBook = SourceBook::query()
            ->where('slug', 'eepc-2015')
            ->firstOrFail();

        //Collega le tre razze principali
        foreach (
            $this->raceReferences() as $raceKey => $reference
        ) {
            //Recupera la versione EEPC della razza
            $race = Race::query()
                ->where('key', $raceKey)
                ->firstOrFail();

            //Crea o aggiorna il riferimento bibliografico
            $this->syncReference(
                $race,
                $sourceBook,
                $reference
            );
        }

        //Collega le cinque sottorazze
        foreach (
            $this->subraceReferences() as $subraceKey => $reference
        ) {
            //Recupera la versione EEPC della sottorazza
            $subrace = Subrace::query()
                ->where('key', $subraceKey)
                ->firstOrFail();

            //Crea o aggiorna il riferimento bibliografico
            $this->syncReference(
                $subrace,
                $sourceBook,
                $reference
            );
        }
    }

    //Crea o aggiorna il riferimento di un contenuto
    private function syncReference(
        Race|Subrace $content,
        SourceBook $sourceBook,
        array $reference
    ): void {
        //Verifica che l'intervallo delle pagine sia valido
        if (
            $reference['page_end']
            < $reference['page_start']
        ) {
            throw new RuntimeException(
                'La pagina finale del riferimento non può '
                . 'precedere quella iniziale.'
            );
        }

        //Registra la fonte senza duplicarla
        $content->sourceReferences()->updateOrCreate(
            [
                'key' => self::REFERENCE_KEY,
            ],
            [
                'source_book_id' => $sourceBook->id,

                //Il manuale definisce direttamente il contenuto
                'reference_type' => 'definition',

                'page_start' => $reference['page_start'],
                'page_end' => $reference['page_end'],
                'section' => $reference['section'],
                'is_primary' => true,
                'sort_order' => 10,

                //Il testo ufficiale non viene copiato nel database
                'official_text' => null,

                'notes' =>
                    'Riferimento bibliografico alla versione '
                    . 'italiana del Compendio del Giocatore '
                    . 'del Male Elementale.',
            ]
        );
    }

    //Restituisce le pagine delle razze principali
    private function raceReferences(): array
    {
        return [
            //La descrizione e i tratti occupano le pagine 3-5
            'aarakocra_eepc_2015' => [
                'page_start' => 3,
                'page_end' => 5,
                'section' =>
                    'Capitolo 1: Razze - Aarakocra',
            ],

            //La razza e le quattro varianti elementali
            //sono descritte tra le pagine 5 e 8
            'genasi_eepc_2015' => [
                'page_start' => 5,
                'page_end' => 8,
                'section' =>
                    'Capitolo 1: Razze - Genasi',
            ],

            //La descrizione e i tratti continuano
            //dalla pagina 10 alla pagina 11
            'goliath_eepc_2015' => [
                'page_start' => 10,
                'page_end' => 11,
                'section' =>
                    'Capitolo 1: Razze - Goliath',
            ],
        ];
    }

    //Restituisce le pagine delle sottorazze
    private function subraceReferences(): array
    {
        return [
            //Il titolo appare a pagina 6
            //e i tratti proseguono a pagina 7
            'water_genasi_eepc_2015' => [
                'page_start' => 6,
                'page_end' => 7,
                'section' =>
                    'Capitolo 1: Razze - Genasi dell’Acqua',
            ],

            //Tutti i tratti principali sono a pagina 7
            'air_genasi_eepc_2015' => [
                'page_start' => 7,
                'page_end' => 7,
                'section' =>
                    'Capitolo 1: Razze - Genasi dell’Aria',
            ],

            //Tutti i tratti principali sono a pagina 7
            'fire_genasi_eepc_2015' => [
                'page_start' => 7,
                'page_end' => 7,
                'section' =>
                    'Capitolo 1: Razze - Genasi del Fuoco',
            ],

            //La descrizione inizia a pagina 7
            //e i tratti terminano a pagina 8
            'earth_genasi_eepc_2015' => [
                'page_start' => 7,
                'page_end' => 8,
                'section' =>
                    'Capitolo 1: Razze - Genasi della Terra',
            ],

            //Descrizione e tratti occupano le pagine 8-10
            'deep_gnome_eepc_2015' => [
                'page_start' => 8,
                'page_end' => 10,
                'section' =>
                    'Capitolo 1: Razze - Gnomo delle Profondità',
            ],
        ];
    }
}
