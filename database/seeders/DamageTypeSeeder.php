<?php

namespace Database\Seeders;

use App\Models\DamageType;
use Illuminate\Database\Seeder;

class DamageTypeSeeder extends Seeder
{
    public function run(): void
    {
        $damageTypes = [
            [
                'name' => 'Acido',
                'description' => 'Danno provocato da sostanze corrosive.',
            ],
            [
                'name' => 'Contundente',
                'description' => 'Danno provocato da impatti, cadute e schiacciamenti.',
            ],
            [
                'name' => 'Freddo',
                'description' => 'Danno provocato da temperature estremamente basse.',
            ],
            [
                'name' => 'Fuoco',
                'description' => 'Danno provocato da fiamme e calore intenso.',
            ],
            [
                'name' => 'Forza',
                'description' => 'Danno provocato da energia magica pura.',
            ],
            [
                'name' => 'Fulmine',
                'description' => 'Danno provocato da scariche ed energia elettrica.',
            ],
            [
                'name' => 'Necrotico',
                'description' => 'Danno provocato da energia che consuma la materia vivente.',
            ],
            [
                'name' => 'Perforante',
                'description' => 'Danno provocato da punte, proiettili e perforazioni.',
            ],
            [
                'name' => 'Psichico',
                'description' => 'Danno che colpisce direttamente la mente.',
            ],
            [
                'name' => 'Radioso',
                'description' => 'Danno provocato da energia luminosa o sacra.',
            ],
            [
                'name' => 'Tagliente',
                'description' => 'Danno provocato da lame, artigli e tagli.',
            ],
            [
                'name' => 'Tuono',
                'description' => 'Danno provocato da onde sonore e forza concussiva.',
            ],
            [
                'name' => 'Veleno',
                'description' => 'Danno provocato da sostanze tossiche.',
            ],
        ];

        foreach ($damageTypes as $damageType) {
            DamageType::updateOrCreate(
                [
                    'name' => $damageType['name'],
                ],
                $damageType
            );
        }
    }
}
