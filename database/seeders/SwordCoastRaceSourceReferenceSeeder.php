<?php

namespace Database\Seeders;

use App\Models\SourceBook;
use App\Models\Subrace;
use Illuminate\Database\Seeder;

class SwordCoastRaceSourceReferenceSeeder extends Seeder
{
    //Collega le sottorazze alle pagine dello SCAG
    public function run(): void
    {
        //Crea tutti i contenuti richiesti dai riferimenti
        $this->call([
            RulesetSeeder::class,
            SourceBookSeeder::class,
            ElementalEvilRaceSeeder::class,
            SwordCoastRaceSeeder::class,
        ]);

        //Recupera la Guida degli Avventurieri alla Costa della Spada
        $sourceBook = SourceBook::query()
            ->where('slug', 'scag-2015')
            ->firstOrFail();

        //Crea o aggiorna ogni riferimento bibliografico
        foreach ($this->references() as $referenceData) {
            $subrace = Subrace::query()
                ->where('key', $referenceData['subrace_key'])
                ->firstOrFail();

            $subrace->sourceReferences()->updateOrCreate(
                [
                    'key' => $referenceData['key'],
                ],
                [
                    'source_book_id' => $sourceBook->id,
                    'reference_type' =>
                        $referenceData['reference_type'],
                    'page_start' => $referenceData['page'],
                    'page_end' => $referenceData['page'],
                    'section' => $referenceData['section'],
                    'is_primary' =>
                        $referenceData['is_primary'],
                    'sort_order' =>
                        $referenceData['sort_order'],
                    'official_text' => null,
                    'notes' => $referenceData['notes'],
                ]
            );
        }
    }

    //Restituisce le pagine verificate nel manuale italiano
    private function references(): array
    {
        return [
            [
                'subrace_key' => 'duergar_scag_2015',
                'key' => 'scag_2015_it_primary_rules',
                'reference_type' => 'definition',
                'page' => 109,
                'section' =>
                    'Capitolo 3: Razze dei Reami - Duergar',
                'is_primary' => true,
                'sort_order' => 10,
                'notes' =>
                    'Definizione della sottorazza e dei suoi tratti.',
            ],
            [
                'subrace_key' =>
                    'ghostwise_halfling_scag_2015',
                'key' => 'scag_2015_it_primary_rules',
                'reference_type' => 'definition',
                'page' => 107,
                'section' =>
                    'Capitolo 3: Razze dei Reami - '
                    . 'Halfling degli Spiriti',
                'is_primary' => true,
                'sort_order' => 10,
                'notes' =>
                    'Definizione della sottorazza e dei suoi tratti.',
            ],
            [
                'subrace_key' => 'deep_gnome_eepc_2015',
                'key' => 'scag_2015_it_reprint',
                'reference_type' => 'reprint',
                'page' => 115,
                'section' =>
                    'Capitolo 3: Razze dei Reami - Svirfneblin',
                'is_primary' => false,
                'sort_order' => 20,
                'notes' =>
                    'Ristampa della sottorazza già pubblicata '
                    . 'nel Compendio del Giocatore del Male '
                    . 'Elementale.',
            ],
        ];
    }
}
