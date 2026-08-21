<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use App\Models\Subrace;
use Illuminate\Database\Seeder;

class SwordCoastRaceFeatureSeeder extends Seeder
{
    //Inserisce le capacità delle sottorazze pubblicate nello SCAG
    public function run(): void
    {
        //Crea prima il catalogo PHB e le sottorazze SCAG
        $this->call([
            RaceFeatureSeeder::class,
            SwordCoastRaceSeeder::class,
        ]);

        //Recupera il regolamento a cui appartengono le capacità
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Crea le capacità e le assegna alle sottorazze
        foreach (
            $this->featuresBySubrace() as $subraceKey => $features
        ) {
            $subrace = Subrace::query()
                ->where('key', $subraceKey)
                ->firstOrFail();

            foreach ($features as $featureData) {
                //Crea o aggiorna la capacità nel catalogo condiviso
                $feature = $ruleset->features()->updateOrCreate(
                    [
                        'key' => $featureData['key'],
                    ],
                    [
                        'name' => $featureData['name'],
                        'type' => 'subrace',
                        'level' => 1,
                        'description' =>
                            $featureData['description'],
                        'max_uses' =>
                            $featureData['max_uses'] ?? null,
                        'recharge' =>
                            $featureData['recharge'] ?? null,
                        'notes' =>
                            $featureData['notes'] ?? null,
                    ]
                );

                //Assegna la capacità alla sottorazza
                $subrace->featureAssignments()->updateOrCreate(
                    [
                        'feature_id' => $feature->id,
                        'level' => 1,
                    ],
                    [
                        'sort_order' =>
                            $featureData['sort_order'],
                        'notes' => null,
                    ]
                );
            }
        }
    }

    //Restituisce le capacità raggruppate per sottorazza
    private function featuresBySubrace(): array
    {
        return [
            'duergar_scag_2015' => [
                [
                    'key' =>
                        'duergar_superior_darkvision_scag_2015',
                    'name' => 'Scurovisione Superiore',
                    'description' =>
                        'Il Duergar può vedere nell’oscurità entro '
                        . 'un raggio di 36 metri.',
                    'sort_order' => 10,
                    'notes' =>
                        'Il raggio sarà registrato anche nella '
                        . 'relazione strutturata dei sensi.',
                ],
                [
                    'key' => 'duergar_resilience_scag_2015',
                    'name' => 'Resilienza Duergar',
                    'description' =>
                        'Il Duergar è particolarmente resistente '
                        . 'alle illusioni e agli effetti che possono '
                        . 'affascinarlo o paralizzarlo.',
                    'sort_order' => 20,
                ],
                [
                    'key' => 'duergar_magic_scag_2015',
                    'name' => 'Magia Duergar',
                    'description' =>
                        'Dal 3° livello il Duergar può ingrandire '
                        . 'se stesso. Dal 5° livello può diventare '
                        . 'invisibile. Le capacità si recuperano '
                        . 'completando un riposo lungo e utilizzano '
                        . 'Intelligenza come caratteristica da '
                        . 'incantatore.',
                    'sort_order' => 30,
                    'notes' =>
                        'Gli incantesimi e i livelli di accesso '
                        . 'saranno registrati anche nelle relazioni '
                        . 'strutturate degli incantesimi razziali.',
                ],
                [
                    'key' =>
                        'duergar_sunlight_sensitivity_scag_2015',
                    'name' => 'Sensibilità alla Luce del Sole',
                    'description' =>
                        'Quando il Duergar, il suo bersaglio o ciò '
                        . 'che sta osservando si trovano alla luce '
                        . 'diretta del sole, la sua vista e i suoi '
                        . 'attacchi risultano penalizzati.',
                    'sort_order' => 40,
                ],
            ],

            'ghostwise_halfling_scag_2015' => [
                [
                    'key' =>
                        'ghostwise_silent_speech_scag_2015',
                    'name' => 'Linguaggio Silenzioso',
                    'description' =>
                        'L’Halfling può comunicare telepaticamente '
                        . 'con una creatura entro 9 metri. La '
                        . 'creatura deve comprendere almeno una '
                        . 'lingua condivisa e la comunicazione può '
                        . 'coinvolgere una sola creatura alla volta.',
                    'sort_order' => 10,
                ],
            ],
        ];
    }
}
