<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Race;
use App\Models\Subrace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class ElementalEvilRaceAbilityBonusSeeder extends Seeder
{
    //Inserisce i bonus di caratteristica delle versioni EEPC
    public function run(): void
    {
        //Crea prima le razze e il catalogo delle caratteristiche
        $this->call([
            ElementalEvilRaceSeeder::class,
            AbilitySeeder::class,
            RaceAbilityBonusSeeder::class,
        ]);

        //Recupera le sei caratteristiche attraverso l'abbreviazione
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

        //Inserisce i bonus delle razze principali
        foreach (
            $this->raceBonuses() as $raceKey => $bonuses
        ) {
            //Recupera la versione EEPC della razza
            $race = Race::query()
                ->where('key', $raceKey)
                ->firstOrFail();

            //Sincronizza tutti i bonus della razza
            $this->syncBonuses(
                $race,
                $abilities,
                $bonuses
            );
        }

        //Inserisce i bonus delle sottorazze
        foreach (
            $this->subraceBonuses() as $subraceKey => $bonuses
        ) {
            //Recupera la versione EEPC della sottorazza
            $subrace = Subrace::query()
                ->where('key', $subraceKey)
                ->firstOrFail();

            //Sincronizza tutti i bonus della sottorazza
            $this->syncBonuses(
                $subrace,
                $abilities,
                $bonuses
            );
        }
    }

    //Crea o aggiorna i bonus di una razza o sottorazza
    private function syncBonuses(
        Race|Subrace $owner,
        Collection $abilities,
        array $bonusDefinitions
    ): void {
        //Memorizza le caratteristiche che devono rimanere
        $expectedAbilityIds = [];

        foreach (
            $bonusDefinitions as $abilityShortName => $bonus
        ) {
            //Recupera la caratteristica richiesta
            $ability = $abilities->get($abilityShortName);

            //Interrompe il seeding se la caratteristica non esiste
            if ($ability === null) {
                throw new RuntimeException(
                    "Caratteristica {$abilityShortName} non trovata."
                );
            }

            $expectedAbilityIds[] = $ability->id;

            //Registra il bonus senza creare duplicati
            $owner->abilityBonuses()->updateOrCreate(
                [
                    'ability_id' => $ability->id,
                ],
                [
                    'bonus' => $bonus,

                    //La riassegnazione è disponibile soltanto
                    //quando la campagna abilita la regola di Tasha
                    'can_be_reassigned' => true,

                    'notes' =>
                        'Può essere riassegnato quando la campagna '
                        . 'abilita la regola opzionale '
                        . 'Personalizzazione dell’origine.',
                ]
            );
        }

        //Rimuove eventuali bonus obsoleti
        $owner->abilityBonuses()
            ->whereNotIn(
                'ability_id',
                $expectedAbilityIds
            )
            ->delete();
    }

    //Restituisce i bonus delle razze principali
    private function raceBonuses(): array
    {
        return [
            //L'Aarakocra ottiene Destrezza +2 e Saggezza +1
            'aarakocra_eepc_2015' => [
                'DES' => 2,
                'SAG' => 1,
            ],

            //Il Genasi ottiene Costituzione +2
            'genasi_eepc_2015' => [
                'COS' => 2,
            ],

            //Il Goliath ottiene Forza +2 e Costituzione +1
            'goliath_eepc_2015' => [
                'FOR' => 2,
                'COS' => 1,
            ],
        ];
    }

    //Restituisce i bonus delle sottorazze
    private function subraceBonuses(): array
    {
        return [
            //Il Genasi dell'Acqua ottiene Saggezza +1
            'water_genasi_eepc_2015' => [
                'SAG' => 1,
            ],

            //Il Genasi dell'Aria ottiene Destrezza +1
            'air_genasi_eepc_2015' => [
                'DES' => 1,
            ],

            //Il Genasi del Fuoco ottiene Intelligenza +1
            'fire_genasi_eepc_2015' => [
                'INT' => 1,
            ],

            //Il Genasi della Terra ottiene Forza +1
            'earth_genasi_eepc_2015' => [
                'FOR' => 1,
            ],

            //Lo Gnomo delle Profondità eredita Intelligenza +2
            //dalla razza Gnomo e aggiunge Destrezza +1
            'deep_gnome_eepc_2015' => [
                'DES' => 1,
            ],
        ];
    }
}
