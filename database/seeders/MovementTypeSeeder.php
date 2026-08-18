<?php

namespace Database\Seeders;

use App\Models\MovementType;
use Illuminate\Database\Seeder;

class MovementTypeSeeder extends Seeder
{
    //Inserisce i cinque tipi di velocità delle creature
    public function run(): void
    {
        //Definisce i tipi di movimento e il loro significato
        $movementTypes = [
            //Movimento terrestre
            [
                'name' => 'Terrestre',
                'description' => 'Movimento sul terreno. Può avvenire camminando, correndo o strisciando secondo l’anatomia della creatura.',
            ],

            //Movimento di scavo
            [
                'name' => 'Scavare',
                'description' => 'Permette di muoversi attraverso materiali come sabbia, terra, fango o ghiaccio. Attraversare la roccia solida richiede una capacità specifica.',
            ],

            //Movimento di scalata
            [
                'name' => 'Scalare',
                'description' => 'Permette di muoversi sulle superfici verticali senza il normale costo aggiuntivo della scalata.',
            ],

            //Movimento di volo
            [
                'name' => 'Volare',
                'description' => 'Permette di muoversi nell’aria. L’eventuale capacità di fluttuare viene registrata separatamente.',
            ],

            //Movimento di nuoto
            [
                'name' => 'Nuotare',
                'description' => 'Permette di muoversi nell’acqua senza il normale costo aggiuntivo del nuoto.',
            ],
        ];

        //Inserisce o aggiorna ogni tipo di movimento
        foreach ($movementTypes as $movementType) {
            MovementType::updateOrCreate(
                //Identifica il tipo di movimento tramite il nome
                [
                    'name' => $movementType['name'],
                ],

                //Inserisce o aggiorna la descrizione
                [
                    'description' => $movementType['description'],
                ]
            );
        }
    }
}
