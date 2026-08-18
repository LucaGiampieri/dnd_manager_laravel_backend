<?php

namespace Database\Seeders;

use App\Models\SpellSchool;
use Illuminate\Database\Seeder;

class SpellSchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            [
                'name' => 'Abiurazione',
                'description' => 'Comprende magie protettive che contrastano, annullano o allontanano effetti e creature.',
            ],
            [
                'name' => 'Ammaliamento',
                'description' => 'Influenza la mente, le emozioni e il comportamento delle creature.',
            ],
            [
                'name' => 'Divinazione',
                'description' => 'Permette di ottenere informazioni, percepire ciò che è nascosto o conoscere possibili eventi.',
            ],
            [
                'name' => 'Evocazione',
                'description' => 'Trasporta o richiama creature, oggetti e sostanze, anche attraverso grandi distanze o piani differenti.',
            ],
            [
                'name' => 'Illusione',
                'description' => 'Inganna i sensi o la mente creando percezioni false o nascondendo la realtà.',
            ],
            [
                'name' => 'Invocazione',
                'description' => 'Manipola l’energia magica per generare effetti, spesso elementali, distruttivi o curativi.',
            ],
            [
                'name' => 'Necromanzia',
                'description' => 'Interagisce con l’energia vitale, la morte e le forze che animano i non morti.',
            ],
            [
                'name' => 'Trasmutazione',
                'description' => 'Modifica le proprietà, la forma o la natura di creature, oggetti e materiali.',
            ],
        ];

        foreach ($schools as $school) {
            SpellSchool::updateOrCreate(
                [
                    'name' => $school['name'],
                ],
                [
                    'description' => $school['description'],
                ]
            );
        }
    }
}
