<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    //Inserisce le cinque monete del regolamento
    public function run(): void
    {
        //Definisce valore, peso e diffusione di ogni moneta
        $currencies = [
            //Moneta di rame
            [
                'name' => 'Rame',
                'code' => 'mr',
                'value_in_copper_pieces' => 1,
                'sort_order' => 1,
                'coin_weight_kg' => '0.0100',
                'is_common' => true,
                'description' => 'La moneta di rame è la denominazione di valore più basso ed è comune nelle piccole transazioni.',
            ],

            //Moneta d'argento
            [
                'name' => 'Argento',
                'code' => 'ma',
                'value_in_copper_pieces' => 10,
                'sort_order' => 2,
                'coin_weight_kg' => '0.0100',
                'is_common' => true,
                'description' => 'La moneta d’argento vale dieci monete di rame ed è molto diffusa nelle transazioni quotidiane.',
            ],

            //Moneta di electrum
            [
                'name' => 'Electrum',
                'code' => 'me',
                'value_in_copper_pieces' => 50,
                'sort_order' => 3,
                'coin_weight_kg' => '0.0100',
                'is_common' => false,
                'description' => 'La moneta di electrum vale cinque monete d’argento ed è meno comune delle principali denominazioni.',
            ],

            //Moneta d'oro
            [
                'name' => 'Oro',
                'code' => 'mo',
                'value_in_copper_pieces' => 100,
                'sort_order' => 4,
                'coin_weight_kg' => '0.0100',
                'is_common' => true,
                'description' => 'La moneta d’oro vale dieci monete d’argento ed è l’unità di riferimento più usata per indicare prezzi e ricchezza.',
            ],

            //Moneta di platino
            [
                'name' => 'Platino',
                'code' => 'mp',
                'value_in_copper_pieces' => 1000,
                'sort_order' => 5,
                'coin_weight_kg' => '0.0100',
                'is_common' => false,
                'description' => 'La moneta di platino vale dieci monete d’oro ed è una denominazione rara e di grande valore.',
            ],
        ];

        //Inserisce o aggiorna ogni valuta
        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                //Identifica la valuta tramite il codice
                [
                    'code' => $currency['code'],
                ],

                //Inserisce o aggiorna tutti i dati
                [
                    'name' => $currency['name'],
                    'value_in_copper_pieces' =>
                        $currency['value_in_copper_pieces'],
                    'sort_order' => $currency['sort_order'],
                    'coin_weight_kg' =>
                        $currency['coin_weight_kg'],
                    'is_common' => $currency['is_common'],
                    'description' => $currency['description'],
                ]
            );
        }
    }
}
