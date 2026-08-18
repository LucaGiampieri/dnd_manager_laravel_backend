<?php

namespace Database\Seeders;

use App\Models\LanguageScript;
use Illuminate\Database\Seeder;

class LanguageScriptSeeder extends Seeder
{
    public function run(): void
    {
        $scripts = [
            [
                'key' => 'common',
                'name' => 'Alfabeto Comune',
                'description' => 'Alfabeto utilizzato principalmente dalle lingue Comune e Halfling.',
                'sort_order' => 1,
            ],
            [
                'key' => 'dwarvish',
                'name' => 'Alfabeto Nanico',
                'description' => 'Alfabeto runico utilizzato da numerose lingue, tra cui Nanico, Gigante, Gnomesco, Goblin, Orchesco e Primordiale.',
                'sort_order' => 2,
            ],
            [
                'key' => 'elvish',
                'name' => 'Alfabeto Elfico',
                'description' => 'Alfabeto utilizzato dalle lingue Elfico, Silvano e Sottocomune.',
                'sort_order' => 3,
            ],
            [
                'key' => 'infernal',
                'name' => 'Alfabeto Infernale',
                'description' => 'Alfabeto utilizzato dalle lingue Infernale e Abissale.',
                'sort_order' => 4,
            ],
            [
                'key' => 'celestial',
                'name' => 'Alfabeto Celestiale',
                'description' => 'Alfabeto associato alla lingua Celestiale.',
                'sort_order' => 5,
            ],
            [
                'key' => 'draconic',
                'name' => 'Alfabeto Draconico',
                'description' => 'Alfabeto associato alla lingua Draconica.',
                'sort_order' => 6,
            ],
        ];

        foreach ($scripts as $script) {
            LanguageScript::updateOrCreate(
                [
                    'key' => $script['key'],
                ],
                [
                    'name' => $script['name'],
                    'description' => $script['description'],
                    'sort_order' => $script['sort_order'],
                ]
            );
        }
    }
}
