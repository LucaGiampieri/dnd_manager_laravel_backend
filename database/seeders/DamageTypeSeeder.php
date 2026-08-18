<?php

namespace Database\Seeders;

use App\Models\DamageType;
use Illuminate\Database\Seeder;

class DamageTypeSeeder extends Seeder
{
    //Inserisce i tredici tipi di danno del regolamento
    public function run(): void
    {
        //Definisce il nome e il significato di ogni tipo di danno
        $damageTypes = [
            //Acido
            [
                'name' => 'Acido',
                'description' => 'Danno provocato da sostanze corrosive.',
            ],

            //Contundente
            [
                'name' => 'Contundente',
                'description' => 'Danno provocato da impatti, cadute e schiacciamenti.',
            ],

            //Freddo
            [
                'name' => 'Freddo',
                'description' => 'Danno provocato da temperature estremamente basse.',
            ],

            //Fuoco
            [
                'name' => 'Fuoco',
                'description' => 'Danno provocato da fiamme e calore intenso.',
            ],

            //Forza
            [
                'name' => 'Forza',
                'description' => 'Danno provocato da energia magica pura.',
            ],

            //Fulmine
            [
                'name' => 'Fulmine',
                'description' => 'Danno provocato da scariche ed energia elettrica.',
            ],

            //Necrotico
            [
                'name' => 'Necrotico',
                'description' => 'Danno provocato da energia che consuma la materia vivente.',
            ],

            //Perforante
            [
                'name' => 'Perforante',
                'description' => 'Danno provocato da punte, proiettili e perforazioni.',
            ],

            //Psichico
            [
                'name' => 'Psichico',
                'description' => 'Danno che colpisce direttamente la mente.',
            ],

            //Radioso
            [
                'name' => 'Radioso',
                'description' => 'Danno provocato da energia luminosa o sacra.',
            ],

            //Tagliente
            [
                'name' => 'Tagliente',
                'description' => 'Danno provocato da lame, artigli e tagli.',
            ],

            //Tuono
            [
                'name' => 'Tuono',
                'description' => 'Danno provocato da onde sonore e forza concussiva.',
            ],

            //Veleno
            [
                'name' => 'Veleno',
                'description' => 'Danno provocato da sostanze tossiche.',
            ],
        ];

        //Inserisce o aggiorna ogni tipo di danno
        foreach ($damageTypes as $damageType) {
            DamageType::updateOrCreate(
                //Identifica il tipo di danno tramite il nome
                [
                    'name' => $damageType['name'],
                ],

                //Inserisce o aggiorna tutti i dati
                $damageType
            );
        }
    }
}
