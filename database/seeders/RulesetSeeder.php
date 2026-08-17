<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class RulesetSeeder extends Seeder
{
    public function run(): void
    {
        Ruleset::updateOrCreate(
            [
                'key' => 'dnd5e_2014',
            ],
            [
                'name' => 'D&D 5e 2014',
                'edition' => '5e',
                'revision' => '2014',
                'description' => 'Regolamento della quinta edizione di Dungeons & Dragons pubblicato nel 2014.',
                'is_active' => true,
            ]
        );
    }
}
