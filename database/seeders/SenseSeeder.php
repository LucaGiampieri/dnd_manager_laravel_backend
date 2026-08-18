<?php

namespace Database\Seeders;

use App\Models\Sense;
use Illuminate\Database\Seeder;

class SenseSeeder extends Seeder
{
    public function run(): void
    {
        $senses = [
            [
                'key' => 'blindsight',
                'name' => 'Vista Cieca',
                'sort_order' => 1,
                'description' => 'Permette di percepire l’ambiente entro un raggio specifico senza affidarsi alla vista.',
            ],
            [
                'key' => 'darkvision',
                'name' => 'Scurovisione',
                'sort_order' => 2,
                'description' => 'Entro il raggio indicato, permette di vedere nella luce fioca come se fosse luce intensa e nell’oscurità non magica come se fosse luce fioca. Nell’oscurità distingue soltanto tonalità di grigio.',
            ],
            [
                'key' => 'tremorsense',
                'name' => 'Percezione Tellurica',
                'sort_order' => 3,
                'description' => 'Permette di rilevare e localizzare le vibrazioni entro un raggio specifico, purché la creatura e la fonte delle vibrazioni siano a contatto con la stessa superficie o sostanza. Normalmente non rileva creature volanti o incorporee.',
            ],
            [
                'key' => 'truesight',
                'name' => 'Vista Pura',
                'sort_order' => 4,
                'description' => 'Entro il raggio indicato, permette di vedere nell’oscurità normale e magica, percepire creature e oggetti invisibili, riconoscere automaticamente le illusioni visive, vedere la forma originale delle creature trasformate e osservare il Piano Etereo.',
            ],
        ];

        foreach ($senses as $sense) {
            Sense::updateOrCreate(
                [
                    'key' => $sense['key'],
                ],
                [
                    'name' => $sense['name'],
                    'sort_order' => $sense['sort_order'],
                    'description' => $sense['description'],
                ]
            );
        }
    }
}
