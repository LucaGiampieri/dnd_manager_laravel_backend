<?php

namespace Database\Seeders;

use App\Models\SpellSchool;
use Illuminate\Database\Seeder;

class SpellSchoolSeeder extends Seeder
{
    //Crea tutte le scuole di magia del regolamento
    public function run(): void
    {
        //Definisce le otto scuole di magia
        $schools = [
            [
                'key' => 'abjuration',
                'name' => 'Abiurazione',
                'description' => 'Magie protettive che respingono, annullano o ostacolano effetti e creature.',
            ],
            [
                'key' => 'conjuration',
                'name' => 'Evocazione',
                'description' => 'Magie che trasportano o richiamano creature, oggetti ed energia.',
            ],
            [
                'key' => 'divination',
                'name' => 'Divinazione',
                'description' => 'Magie che rivelano informazioni, presagi e conoscenze nascoste.',
            ],
            [
                'key' => 'enchantment',
                'name' => 'Ammaliamento',
                'description' => 'Magie che influenzano la mente, le emozioni e il comportamento.',
            ],
            [
                'key' => 'evocation',
                'name' => 'Invocazione',
                'description' => 'Magie che manipolano direttamente l’energia magica.',
            ],
            [
                'key' => 'illusion',
                'name' => 'Illusione',
                'description' => 'Magie che ingannano i sensi e alterano la percezione.',
            ],
            [
                'key' => 'necromancy',
                'name' => 'Necromanzia',
                'description' => 'Magie che manipolano la vita, la morte e l’energia necrotica.',
            ],
            [
                'key' => 'transmutation',
                'name' => 'Trasmutazione',
                'description' => 'Magie che modificano le proprietà di creature, oggetti e materia.',
            ],
        ];

        //Crea o aggiorna ogni scuola tramite la chiave stabile
        foreach ($schools as $school) {
            SpellSchool::query()->updateOrCreate(
                [
                    'key' => $school['key'],
                ],
                [
                    'name' => $school['name'],
                    'description' => $school['description'],
                ]
            );
        }
    }
}
