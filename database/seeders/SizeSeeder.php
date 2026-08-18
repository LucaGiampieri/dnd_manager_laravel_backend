<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    //Inserisce le sei categorie di taglia
    public function run(): void
    {
        //Definisce l'ordine e lo spazio controllato da ogni taglia
        $sizes = [
            //Minuscola
            [
                'name' => 'Minuscola',
                'sort_order' => 1,
                'space_side_meters' => '0.750',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 0,75 metri o inferiore.',
            ],

            //Piccola
            [
                'name' => 'Piccola',
                'sort_order' => 2,
                'space_side_meters' => '1.500',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 1,5 metri.',
            ],

            //Media
            [
                'name' => 'Media',
                'sort_order' => 3,
                'space_side_meters' => '1.500',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 1,5 metri.',
            ],

            //Grande
            [
                'name' => 'Grande',
                'sort_order' => 4,
                'space_side_meters' => '3.000',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 3 metri.',
            ],

            //Enorme
            [
                'name' => 'Enorme',
                'sort_order' => 5,
                'space_side_meters' => '4.500',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 4,5 metri.',
            ],

            //Mastodontica
            [
                'name' => 'Mastodontica',
                'sort_order' => 6,
                'space_side_meters' => '6.000',
                'description' => 'Controlla uno spazio quadrato con lato di almeno 6 metri.',
            ],
        ];

        //Inserisce o aggiorna ogni taglia
        foreach ($sizes as $size) {
            Size::updateOrCreate(
                //Identifica la taglia tramite il nome
                [
                    'name' => $size['name'],
                ],

                //Inserisce o aggiorna i dati della taglia
                [
                    'sort_order' => $size['sort_order'],
                    'space_side_meters' => $size['space_side_meters'],
                    'description' => $size['description'],
                ]
            );
        }
    }
}
