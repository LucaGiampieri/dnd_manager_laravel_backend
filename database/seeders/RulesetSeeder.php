<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class RulesetSeeder extends Seeder
{
    //Inserisce il regolamento D&D 5e 2014
    public function run(): void
    {
        //Crea il regolamento oppure aggiorna quello con la stessa chiave
        Ruleset::updateOrCreate(
            //Identifica univocamente il regolamento
            [
                'key' => 'dnd5e_2014',
            ],

            //Inserisce o aggiorna i dati del regolamento
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
