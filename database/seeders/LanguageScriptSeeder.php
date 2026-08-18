<?php

namespace Database\Seeders;

use App\Models\LanguageScript;
use Illuminate\Database\Seeder;

class LanguageScriptSeeder extends Seeder
{
    //Inserisce i sei alfabeti utilizzati dalle lingue
    public function run(): void
    {
        //Definisce nome, descrizione e ordine di ogni alfabeto
        $scripts = [
            //Alfabeto Comune
            [
                'key' => 'common',
                'name' => 'Alfabeto Comune',
                'description' => 'Alfabeto utilizzato principalmente dalle lingue Comune e Halfling.',
                'sort_order' => 1,
            ],

            //Alfabeto Nanico
            [
                'key' => 'dwarvish',
                'name' => 'Alfabeto Nanico',
                'description' => 'Alfabeto runico utilizzato da numerose lingue, tra cui Nanico, Gigante, Gnomesco, Goblin, Orchesco e Primordiale.',
                'sort_order' => 2,
            ],

            //Alfabeto Elfico
            [
                'key' => 'elvish',
                'name' => 'Alfabeto Elfico',
                'description' => 'Alfabeto utilizzato dalle lingue Elfico, Silvano e Sottocomune.',
                'sort_order' => 3,
            ],

            //Alfabeto Infernale
            [
                'key' => 'infernal',
                'name' => 'Alfabeto Infernale',
                'description' => 'Alfabeto utilizzato dalle lingue Infernale e Abissale.',
                'sort_order' => 4,
            ],

            //Alfabeto Celestiale
            [
                'key' => 'celestial',
                'name' => 'Alfabeto Celestiale',
                'description' => 'Alfabeto associato alla lingua Celestiale.',
                'sort_order' => 5,
            ],

            //Alfabeto Draconico
            [
                'key' => 'draconic',
                'name' => 'Alfabeto Draconico',
                'description' => 'Alfabeto associato alla lingua Draconica.',
                'sort_order' => 6,
            ],
        ];

        //Inserisce o aggiorna ogni alfabeto
        foreach ($scripts as $script) {
            LanguageScript::updateOrCreate(
                //Identifica l'alfabeto tramite la chiave stabile
                [
                    'key' => $script['key'],
                ],

                //Inserisce o aggiorna tutti i dati
                [
                    'name' => $script['name'],
                    'description' => $script['description'],
                    'sort_order' => $script['sort_order'],
                ]
            );
        }
    }
}
