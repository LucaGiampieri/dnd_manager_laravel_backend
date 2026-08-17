<?php

namespace Database\Seeders;

use App\Models\MovementType;
use Illuminate\Database\Seeder;

class MovementTypeSeeder extends Seeder
{
    public function run(): void
    {
        $movementTypes = [
            [
                'name' => 'Terrestre',
                'description' => 'Movimento sul terreno. Può avvenire camminando, correndo o strisciando secondo l’anatomia della creatura.',
            ],
            [
                'name' => 'Scavare',
                'description' => 'Permette di muoversi attraverso materiali come sabbia, terra, fango o ghiaccio. Attraversare la roccia solida richiede una capacità specifica.',
            ],
            [
                'name' => 'Scalare',
                'description' => 'Permette di muoversi sulle superfici verticali senza il normale costo aggiuntivo della scalata.',
            ],
            [
                'name' => 'Volare',
                'description' => 'Permette di muoversi nell’aria. L’eventuale capacità di fluttuare viene registrata separatamente.',
            ],
            [
                'name' => 'Nuotare',
                'description' => 'Permette di muoversi nell’acqua senza il normale costo aggiuntivo del nuoto.',
            ],
        ];

        foreach ($movementTypes as $movementType) {
            MovementType::updateOrCreate(
                [
                    'name' => $movementType['name'],
                ],
                [
                    'description' => $movementType['description'],
                ]
            );
        }
    }
}
