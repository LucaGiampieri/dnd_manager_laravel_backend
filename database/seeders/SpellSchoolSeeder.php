<?php

namespace Database\Seeders;

use App\Models\SpellSchool;
use Illuminate\Database\Seeder;

class SpellSchoolSeeder extends Seeder
{
    //Inserisce le otto scuole di magia
    public function run(): void
    {
        //Definisce il nome e il significato generale di ogni scuola
        $schools = [
            //Abiurazione
            [
                'name' => 'Abiurazione',
                'description' => 'Comprende magie protettive che contrastano, annullano o allontanano effetti e creature.',
            ],

            //Ammaliamento
            [
                'name' => 'Ammaliamento',
                'description' => 'Influenza la mente, le emozioni e il comportamento delle creature.',
            ],

            //Divinazione
            [
                'name' => 'Divinazione',
                'description' => 'Permette di ottenere informazioni, percepire ciò che è nascosto o conoscere possibili eventi.',
            ],

            //Evocazione
            [
                'name' => 'Evocazione',
                'description' => 'Trasporta o richiama creature, oggetti e sostanze, anche attraverso grandi distanze o piani differenti.',
            ],

            //Illusione
            [
                'name' => 'Illusione',
                'description' => 'Inganna i sensi o la mente creando percezioni false o nascondendo la realtà.',
            ],

            //Invocazione
            [
                'name' => 'Invocazione',
                'description' => 'Manipola l’energia magica per generare effetti, spesso elementali, distruttivi o curativi.',
            ],

            //Necromanzia
            [
                'name' => 'Necromanzia',
                'description' => 'Interagisce con l’energia vitale, la morte e le forze che animano i non morti.',
            ],

            //Trasmutazione
            [
                'name' => 'Trasmutazione',
                'description' => 'Modifica le proprietà, la forma o la natura di creature, oggetti e materiali.',
            ],
        ];

        //Inserisce o aggiorna ogni scuola di magia
        foreach ($schools as $school) {
            SpellSchool::updateOrCreate(
                //Identifica la scuola tramite il nome
                [
                    'name' => $school['name'],
                ],

                //Inserisce o aggiorna la descrizione
                [
                    'description' => $school['description'],
                ]
            );
        }
    }
}
