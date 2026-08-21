<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Race;
use App\Models\RaceChoice;
use App\Models\Subrace;
use App\Models\SubraceChoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class RaceChoiceSeeder extends Seeder
{
    //Inserisce le scelte flessibili concesse da razze e sottorazze
    public function run(): void
    {
        //Recupera tutte le caratteristiche tramite l'abbreviazione
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

        //Inserisce la scelta dei due bonus del Mezzelfo
        $this->seedHalfElfChoice($abilities);

        //Inserisce la scelta dei due bonus dell'Umano Variante
        $this->seedVariantHumanChoice($abilities);
    }

    //Inserisce la scelta flessibile del Mezzelfo
    private function seedHalfElfChoice(
        Collection $abilities
    ): void {
        //Recupera la razza del Mezzelfo
        $halfElf = Race::query()
            ->where('key', 'half_elf')
            ->firstOrFail();

        //Crea o aggiorna la regola di scelta
        $choice = $halfElf->choices()->updateOrCreate(
            [
                'key' => 'flexible_ability_score_increases',
            ],
            [
                'name' => 'Incrementi flessibili di caratteristica',
                'choice_type' => 'ability',
                'choose' => 2,
                'level' => 1,
                'required' => true,
                'sort_order' => 10,
                'description' =>
                    'Aumenta di 1 due caratteristiche differenti '
                    . 'scelte tra quelle disponibili.',
                'notes' =>
                    'Carisma non è selezionabile perché il Mezzelfo '
                    . 'riceve già un incremento fisso di +2.',
            ]
        );

        //Definisce le caratteristiche selezionabili dal Mezzelfo
        $optionDefinitions = [
            'strength' => [
                'ability_short_name' => 'FOR',
                'sort_order' => 10,
            ],
            'dexterity' => [
                'ability_short_name' => 'DES',
                'sort_order' => 20,
            ],
            'constitution' => [
                'ability_short_name' => 'COS',
                'sort_order' => 30,
            ],
            'intelligence' => [
                'ability_short_name' => 'INT',
                'sort_order' => 40,
            ],
            'wisdom' => [
                'ability_short_name' => 'SAG',
                'sort_order' => 50,
            ],
        ];

        //Sincronizza le cinque opzioni disponibili
        $this->syncAbilityOptions(
            $choice,
            $abilities,
            $optionDefinitions
        );
    }

    //Inserisce la scelta flessibile dell'Umano Variante
    private function seedVariantHumanChoice(
        Collection $abilities
    ): void {
        //Recupera la sottorazza dell'Umano Variante
        $variantHuman = Subrace::query()
            ->where('key', 'variant_human')
            ->firstOrFail();

        //Crea o aggiorna la regola di scelta
        $choice = $variantHuman->choices()->updateOrCreate(
            [
                'key' => 'variant_ability_score_increases',
            ],
            [
                'name' => 'Incrementi di caratteristica della variante',
                'choice_type' => 'ability',
                'choose' => 2,
                'level' => 1,
                'required' => true,
                'sort_order' => 10,
                'description' =>
                    'Aumenta di 1 due caratteristiche differenti '
                    . 'scelte tra tutte quelle disponibili.',
                'notes' =>
                    'Questa scelta sostituisce gli incrementi '
                    . 'dell’Umano standard.',
            ]
        );

        //Definisce tutte le caratteristiche selezionabili
        $optionDefinitions = [
            'strength' => [
                'ability_short_name' => 'FOR',
                'sort_order' => 10,
            ],
            'dexterity' => [
                'ability_short_name' => 'DES',
                'sort_order' => 20,
            ],
            'constitution' => [
                'ability_short_name' => 'COS',
                'sort_order' => 30,
            ],
            'intelligence' => [
                'ability_short_name' => 'INT',
                'sort_order' => 40,
            ],
            'wisdom' => [
                'ability_short_name' => 'SAG',
                'sort_order' => 50,
            ],
            'charisma' => [
                'ability_short_name' => 'CAR',
                'sort_order' => 60,
            ],
        ];

        //Sincronizza le sei opzioni disponibili
        $this->syncAbilityOptions(
            $choice,
            $abilities,
            $optionDefinitions
        );
    }

    //Crea o aggiorna le opzioni di caratteristica di una scelta
    private function syncAbilityOptions(
        RaceChoice|SubraceChoice $choice,
        Collection $abilities,
        array $optionDefinitions
    ): void {
        //Memorizza le chiavi che devono rimanere nel catalogo
        $expectedKeys = array_keys($optionDefinitions);

        //Inserisce ogni caratteristica selezionabile
        foreach (
            $optionDefinitions as $key => $optionDefinition
        ) {
            //Recupera la caratteristica attraverso l'abbreviazione
            $abilityShortName =
                $optionDefinition['ability_short_name'];

            $ability = $abilities->get($abilityShortName);

            //Interrompe il seeding se manca una caratteristica
            if ($ability === null) {
                throw new RuntimeException(
                    "Caratteristica {$abilityShortName} non trovata."
                );
            }

            //Crea o aggiorna l'opzione senza duplicarla
            $choice->options()->updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'option_type' => 'ability',
                    'option_id' => $ability->id,
                    'option_text' => null,
                    'value' => 1,
                    'quantity' => 1,
                    'sort_order' =>
                        $optionDefinition['sort_order'],
                    'notes' => null,
                ]
            );
        }

        //Rimuove eventuali opzioni obsolete della stessa scelta
        $choice->options()
            ->whereNotIn('key', $expectedKeys)
            ->delete();
    }
}
