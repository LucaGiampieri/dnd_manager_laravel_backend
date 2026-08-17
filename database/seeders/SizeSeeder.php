<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            [
                'name' => 'Minuscola',
                'sort_order' => 1,
                'space_side_meters' => '0.750',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 0,75 metri o inferiore.',
            ],
            [
                'name' => 'Piccola',
                'sort_order' => 2,
                'space_side_meters' => '1.500',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 1,5 metri.',
            ],
            [
                'name' => 'Media',
                'sort_order' => 3,
                'space_side_meters' => '1.500',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 1,5 metri.',
            ],
            [
                'name' => 'Grande',
                'sort_order' => 4,
                'space_side_meters' => '3.000',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 3 metri.',
            ],
            [
                'name' => 'Enorme',
                'sort_order' => 5,
                'space_side_meters' => '4.500',
                'description' => 'Controlla normalmente uno spazio quadrato con lato di 4,5 metri.',
            ],
            [
                'name' => 'Mastodontica',
                'sort_order' => 6,
                'space_side_meters' => '6.000',
                'description' => 'Controlla uno spazio quadrato con lato di almeno 6 metri.',
            ],
        ];

        foreach ($sizes as $size) {
            Size::updateOrCreate(
                [
                    'name' => $size['name'],
                ],
                [
                    'sort_order' => $size['sort_order'],
                    'space_side_meters' => $size['space_side_meters'],
                    'description' => $size['description'],
                ]
            );
        }
    }
}
