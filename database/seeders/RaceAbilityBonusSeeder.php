<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Race;
use App\Models\Subrace;
use Illuminate\Database\Seeder;

class RaceAbilityBonusSeeder extends Seeder
{
    //Inserisce i bonus fissi concessi da razze e sottorazze
    public function run(): void
    {
        //Recupera le caratteristiche utilizzando le abbreviazioni
        $abilities = Ability::query()
            ->whereIn('short_name', [
                'FOR',
                'DES',
                'COS',
                'INT',
                'SAG',
                'CAR',
            ])
            ->get()
            ->keyBy('short_name');

        //Inserisce i bonus concessi dalle razze principali
        foreach ($this->raceBonuses() as $raceKey => $bonuses) {
            //Recupera la razza che concede i bonus
            $race = Race::query()
                ->where('key', $raceKey)
                ->firstOrFail();

            //Inserisce ogni bonus senza creare duplicati
            foreach ($bonuses as $abilityShortName => $bonus) {
                //Recupera la caratteristica interessata
                $ability = $abilities->get($abilityShortName);

                //Interrompe il seeding se manca una caratteristica
                if ($ability === null) {
                    throw new \RuntimeException(
                        "Caratteristica {$abilityShortName} non trovata."
                    );
                }

                //Registra il bonus automatico della razza
                $race->abilityBonuses()->updateOrCreate(
                    //Identifica il bonus tramite la caratteristica
                    [
                        'ability_id' => $ability->id,
                    ],

                    //Inserisce o aggiorna i dati del bonus
                    [
                        //Conserva il valore previsto dalla razza
                        'bonus' => $bonus,

                        //Permette di riassegnare il bonus usando
                        //la regola opzionale introdotta da Tasha
                        'can_be_reassigned' => true,

                        //Ricorda l'eccezione dell'Umano Variante
                        'notes' => $raceKey === 'human'
                            ? 'Non si applica quando viene scelta '
                                . 'la variante dell’Umano.'
                            : null,
                    ]
                );
            }
        }

        //Inserisce i bonus concessi dalle sottorazze
        foreach (
            $this->subraceBonuses() as $subraceKey => $bonuses
        ) {
            //Recupera la sottorazza che concede i bonus
            $subrace = Subrace::query()
                ->where('key', $subraceKey)
                ->firstOrFail();

            //Inserisce ogni bonus senza creare duplicati
            foreach ($bonuses as $abilityShortName => $bonus) {
                //Recupera la caratteristica interessata
                $ability = $abilities->get($abilityShortName);

                //Interrompe il seeding se manca una caratteristica
                if ($ability === null) {
                    throw new \RuntimeException(
                        "Caratteristica {$abilityShortName} non trovata."
                    );
                }

                //Registra il bonus automatico della sottorazza
                $subrace->abilityBonuses()->updateOrCreate(
                    //Identifica il bonus tramite la caratteristica
                    [
                        'ability_id' => $ability->id,
                    ],

                    //Inserisce o aggiorna i dati del bonus
                    [
                        //Conserva il valore previsto dalla sottorazza
                        'bonus' => $bonus,

                        //Permette di riassegnare il bonus usando
                        //la regola opzionale introdotta da Tasha
                        'can_be_reassigned' => true,

                        //Non sono necessarie note aggiuntive
                        'notes' => null,
                    ]
                );
            }
        }
    }

    //Restituisce i bonus fissi delle razze principali
    private function raceBonuses(): array
    {
        return [
            //Il Nano ottiene Costituzione +2
            'dwarf' => [
                'COS' => 2,
            ],

            //L'Elfo ottiene Destrezza +2
            'elf' => [
                'DES' => 2,
            ],

            //L'Halfling ottiene Destrezza +2
            'halfling' => [
                'DES' => 2,
            ],

            //L'Umano standard ottiene +1 a tutte le caratteristiche
            'human' => [
                'FOR' => 1,
                'DES' => 1,
                'COS' => 1,
                'INT' => 1,
                'SAG' => 1,
                'CAR' => 1,
            ],

            //Il Dragonide ottiene Forza +2 e Carisma +1
            'dragonborn' => [
                'FOR' => 2,
                'CAR' => 1,
            ],

            //Lo Gnomo ottiene Intelligenza +2
            'gnome' => [
                'INT' => 2,
            ],

            //Il Mezzelfo ottiene Carisma +2
            //e sceglierà separatamente altri due bonus
            'half_elf' => [
                'CAR' => 2,
            ],

            //Il Mezzorco ottiene Forza +2 e Costituzione +1
            'half_orc' => [
                'FOR' => 2,
                'COS' => 1,
            ],

            //Il Tiefling ottiene Intelligenza +1 e Carisma +2
            'tiefling' => [
                'INT' => 1,
                'CAR' => 2,
            ],
        ];
    }

    //Restituisce i bonus fissi delle sottorazze
    private function subraceBonuses(): array
    {
        return [
            //Il Nano delle Colline ottiene Saggezza +1
            'hill_dwarf' => [
                'SAG' => 1,
            ],

            //Il Nano delle Montagne ottiene Forza +2
            'mountain_dwarf' => [
                'FOR' => 2,
            ],

            //L'Elfo Alto ottiene Intelligenza +1
            'high_elf' => [
                'INT' => 1,
            ],

            //L'Elfo dei Boschi ottiene Saggezza +1
            'wood_elf' => [
                'SAG' => 1,
            ],

            //Il Drow ottiene Carisma +1
            'drow' => [
                'CAR' => 1,
            ],

            //L'Halfling Piedelesto ottiene Carisma +1
            'lightfoot_halfling' => [
                'CAR' => 1,
            ],

            //L'Halfling Tozzo ottiene Costituzione +1
            'stout_halfling' => [
                'COS' => 1,
            ],

            //Lo Gnomo delle Foreste ottiene Destrezza +1
            'forest_gnome' => [
                'DES' => 1,
            ],

            //Lo Gnomo delle Rocce ottiene Costituzione +1
            'rock_gnome' => [
                'COS' => 1,
            ],

            //L'Umano Variante non riceve bonus fissi:
            //sceglierà due caratteristiche differenti in seguito
            'variant_human' => [],
        ];
    }
}
