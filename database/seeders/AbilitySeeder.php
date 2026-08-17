<?php

namespace Database\Seeders;

use App\Models\Ability;
use Illuminate\Database\Seeder;

class AbilitySeeder extends Seeder
{
    public function run(): void
    {
        $abilities = [
            [
                'name' => 'Forza',
                'short_name' => 'FOR',
                'description' => 'Misura la potenza fisica e la capacità di esercitare forza.',
            ],
            [
                'name' => 'Destrezza',
                'short_name' => 'DES',
                'description' => 'Misura agilità, riflessi ed equilibrio.',
            ],
            [
                'name' => 'Costituzione',
                'short_name' => 'COS',
                'description' => 'Misura salute, resistenza e vigore fisico.',
            ],
            [
                'name' => 'Intelligenza',
                'short_name' => 'INT',
                'description' => 'Misura memoria, ragionamento e capacità analitica.',
            ],
            [
                'name' => 'Saggezza',
                'short_name' => 'SAG',
                'description' => 'Misura percezione, intuito e consapevolezza.',
            ],
            [
                'name' => 'Carisma',
                'short_name' => 'CAR',
                'description' => 'Misura personalità, eloquenza e sicurezza.',
            ],
        ];

        foreach ($abilities as $ability) {
            Ability::updateOrCreate(
                [
                    'short_name' => $ability['short_name'],
                ],
                $ability
            );
        }
    }
}
