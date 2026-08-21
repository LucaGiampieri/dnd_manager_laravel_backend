<?php

namespace Database\Seeders;

use App\Models\Race;
use Illuminate\Database\Seeder;
use RuntimeException;

class SwordCoastRaceSeeder extends Seeder
{
    //Identifica la versione pubblicata nello SCAG
    private const VERSION_KEY = 'scag_2015';

    //Crea le sottorazze giocabili introdotte nello SCAG
    public function run(): void
    {
        //Crea prima le razze principali del PHB
        $this->call(RaceSeeder::class);

        //Inserisce le sottorazze sotto la razza corretta
        foreach (
            $this->subracesByRace() as $raceKey => $subraces
        ) {
            //Recupera la razza principale
            $race = Race::query()
                ->where('key', $raceKey)
                ->first();

            //Interrompe il seeding se manca la razza richiesta
            if ($race === null) {
                throw new RuntimeException(
                    "Razza principale {$raceKey} non trovata."
                );
            }

            //Crea o aggiorna ogni sottorazza
            foreach ($subraces as $subraceData) {
                $race->subraces()->updateOrCreate(
                    [
                        'key' => $subraceData['key'],
                    ],
                    [
                        'canonical_key' =>
                            $subraceData['canonical_key'],
                        'version_key' => self::VERSION_KEY,
                        'is_legacy' => false,
                        'name' => $subraceData['name'],
                        'typical_alignment' =>
                            $subraceData['typical_alignment'],
                        'is_variant' => false,
                        'replaces_race_ability_bonuses' => false,
                        'selectable' => true,
                        'requires_dm_permission' => true,
                        'sort_order' =>
                            $subraceData['sort_order'],
                        'description' =>
                            $subraceData['description'],
                        'notes' =>
                            $subraceData['notes'],
                    ]
                );
            }
        }
    }

    //Restituisce le sottorazze raggruppate per razza principale
    private function subracesByRace(): array
    {
        return [
            //Il Duergar è una sottorazza del Nano
            'dwarf' => [
                [
                    'key' => 'duergar_scag_2015',
                    'canonical_key' => 'duergar',
                    'name' => 'Duergar',
                    'typical_alignment' =>
                        'La società duergar tende verso la legge '
                        . 'e presenta spesso inclinazioni malvagie.',
                    'sort_order' => 30,
                    'description' =>
                        'Nani grigi originari dell’Underdark, '
                        . 'resistenti alla magia mentale e dotati '
                        . 'di capacità magiche innate.',
                    'notes' =>
                        'Eredita dal Nano la taglia Media e la '
                        . 'velocità terrestre di 7,5 metri.',
                ],
            ],

            //L'Halfling degli Spiriti è una sottorazza dell'Halfling
            'halfling' => [
                [
                    'key' => 'ghostwise_halfling_scag_2015',
                    'canonical_key' => 'ghostwise_halfling',
                    'name' => 'Halfling degli Spiriti',
                    'typical_alignment' =>
                        'Vive generalmente in comunità isolate '
                        . 'e fortemente legate al proprio territorio.',
                    'sort_order' => 30,
                    'description' =>
                        'Halfling riservato e legato alla natura, '
                        . 'capace di comunicare mentalmente con '
                        . 'le creature vicine.',
                    'notes' =>
                        'Eredita dall’Halfling la taglia Piccola '
                        . 'e la velocità terrestre di 7,5 metri.',
                ],
            ],
        ];
    }
}
