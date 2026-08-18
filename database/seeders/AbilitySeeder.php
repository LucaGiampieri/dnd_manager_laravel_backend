<?php

namespace Database\Seeders;

use App\Models\Ability;
use Illuminate\Database\Seeder;

class AbilitySeeder extends Seeder
{
    //Inserisce le sei caratteristiche fondamentali
    public function run(): void
    {
        //Definisce nomi, abbreviazioni e descrizioni
        $abilities = [
            //Forza
            [
                'name' => 'Forza',
                'short_name' => 'FOR',
                'description' => 'Misura la potenza fisica e la capacità di esercitare forza.',
            ],

            //Destrezza
            [
                'name' => 'Destrezza',
                'short_name' => 'DES',
                'description' => 'Misura agilità, riflessi ed equilibrio.',
            ],

            //Costituzione
            [
                'name' => 'Costituzione',
                'short_name' => 'COS',
                'description' => 'Misura salute, resistenza e vigore fisico.',
            ],

            //Intelligenza
            [
                'name' => 'Intelligenza',
                'short_name' => 'INT',
                'description' => 'Misura memoria, ragionamento e capacità analitica.',
            ],

            //Saggezza
            [
                'name' => 'Saggezza',
                'short_name' => 'SAG',
                'description' => 'Misura percezione, intuito e consapevolezza.',
            ],

            //Carisma
            [
                'name' => 'Carisma',
                'short_name' => 'CAR',
                'description' => 'Misura personalità, eloquenza e sicurezza.',
            ],
        ];

        //Inserisce o aggiorna ogni caratteristica
        foreach ($abilities as $ability) {
            Ability::updateOrCreate(
                //Identifica la caratteristica tramite l'abbreviazione
                [
                    'short_name' => $ability['short_name'],
                ],

                //Inserisce o aggiorna tutti i dati
                $ability
            );
        }
    }
}
