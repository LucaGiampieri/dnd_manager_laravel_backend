<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Subrace;
use Illuminate\Database\Seeder;
use RuntimeException;

class SwordCoastRaceAbilityBonusSeeder extends Seeder
{
    //Inserisce i bonus fissi delle sottorazze pubblicate nello SCAG
    public function run(): void
    {
        //Crea prima caratteristiche, razze e sottorazze necessarie
        $this->call([
            AbilitySeeder::class,
            SwordCoastRaceSeeder::class,
        ]);

        //Recupera le caratteristiche attraverso le abbreviazioni
        $abilities = Ability::query()
            ->whereIn('short_name', [
                'FOR',
                'SAG',
            ])
            ->get()
            ->keyBy('short_name');

        //Inserisce i bonus di ogni sottorazza
        foreach ($this->bonuses() as $subraceKey => $bonuses) {
            //Recupera la sottorazza che concede i bonus
            $subrace = Subrace::query()
                ->where('key', $subraceKey)
                ->firstOrFail();

            //Crea o aggiorna ogni bonus senza duplicarlo
            foreach ($bonuses as $abilityShortName => $bonus) {
                $ability = $abilities->get($abilityShortName);

                //Interrompe il seeding se manca la caratteristica
                if ($ability === null) {
                    throw new RuntimeException(
                        "Caratteristica {$abilityShortName} non trovata."
                    );
                }

                $subrace->abilityBonuses()->updateOrCreate(
                    [
                        'ability_id' => $ability->id,
                    ],
                    [
                        'bonus' => $bonus,
                        'can_be_reassigned' => true,
                        'notes' =>
                            'Può essere riassegnato soltanto quando '
                            . 'è attiva una regola opzionale che lo consente.',
                    ]
                );
            }
        }
    }

    //Restituisce i bonus fissi previsti dallo SCAG
    private function bonuses(): array
    {
        return [
            //Il Duergar aumenta la Forza di 1
            'duergar_scag_2015' => [
                'FOR' => 1,
            ],

            //L'Halfling degli Spiriti aumenta la Saggezza di 1
            'ghostwise_halfling_scag_2015' => [
                'SAG' => 1,
            ],
        ];
    }
}
