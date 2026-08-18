<?php

namespace Database\Seeders;

use App\Models\Sense;
use Illuminate\Database\Seeder;

class SenseSeeder extends Seeder
{
    //Inserisce i quattro sensi speciali
    public function run(): void
    {
        //Definisce nome, descrizione e ordine di ogni senso
        $senses = [
            //Vista Cieca
            [
                'key' => 'blindsight',
                'name' => 'Vista Cieca',
                'sort_order' => 1,
                'description' => 'Permette di percepire l’ambiente entro un raggio specifico senza affidarsi alla vista.',
            ],

            //Scurovisione
            [
                'key' => 'darkvision',
                'name' => 'Scurovisione',
                'sort_order' => 2,
                'description' => 'Entro il raggio indicato, permette di vedere nella luce fioca come se fosse luce intensa e nell’oscurità non magica come se fosse luce fioca. Nell’oscurità distingue soltanto tonalità di grigio.',
            ],

            //Percezione Tellurica
            [
                'key' => 'tremorsense',
                'name' => 'Percezione Tellurica',
                'sort_order' => 3,
                'description' => 'Permette di rilevare e localizzare le vibrazioni entro un raggio specifico, purché la creatura e la fonte delle vibrazioni siano a contatto con la stessa superficie o sostanza. Normalmente non rileva creature volanti o incorporee.',
            ],

            //Vista Pura
            [
                'key' => 'truesight',
                'name' => 'Vista Pura',
                'sort_order' => 4,
                'description' => 'Entro il raggio indicato, permette di vedere nell’oscurità normale e magica, percepire creature e oggetti invisibili, riconoscere automaticamente le illusioni visive, vedere la forma originale delle creature trasformate e osservare il Piano Etereo.',
            ],
        ];

        //Inserisce o aggiorna ogni senso
        foreach ($senses as $sense) {
            Sense::updateOrCreate(
                //Identifica il senso tramite la chiave stabile
                [
                    'key' => $sense['key'],
                ],

                //Inserisce o aggiorna tutti i dati
                [
                    'name' => $sense['name'],
                    'sort_order' => $sense['sort_order'],
                    'description' => $sense['description'],
                ]
            );
        }
    }
}
