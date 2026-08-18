<?php

namespace Database\Seeders;

use App\Models\Ability;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    //Inserisce le diciotto abilità e le collega alle caratteristiche
    public function run(): void
    {
        //Raggruppa le abilità tramite l'abbreviazione della caratteristica
        $skillsByAbility = [
            //Abilità basate sulla Forza
            'FOR' => [
                [
                    'name' => 'Atletica',
                    'description' => 'Rappresenta la capacità di compiere sforzi fisici come scalare, saltare e nuotare.',
                ],
            ],

            //Abilità basate sulla Destrezza
            'DES' => [
                [
                    'name' => 'Acrobazia',
                    'description' => 'Rappresenta equilibrio, agilità e movimenti acrobatici.',
                ],
                [
                    'name' => 'Furtività',
                    'description' => 'Rappresenta la capacità di muoversi e nascondersi senza essere notati.',
                ],
                [
                    'name' => 'Rapidità di Mano',
                    'description' => 'Rappresenta destrezza manuale, borseggio e manipolazioni discrete.',
                ],
            ],

            //Abilità basate sull'Intelligenza
            'INT' => [
                [
                    'name' => 'Arcano',
                    'description' => 'Rappresenta conoscenze su magia, incantesimi, piani e tradizioni arcane.',
                ],
                [
                    'name' => 'Indagare',
                    'description' => 'Rappresenta la capacità di trovare indizi e ricavare conclusioni logiche.',
                ],
                [
                    'name' => 'Natura',
                    'description' => 'Rappresenta conoscenze su terreno, piante, animali e fenomeni naturali.',
                ],
                [
                    'name' => 'Religione',
                    'description' => 'Rappresenta conoscenze su divinità, culti, riti e tradizioni religiose.',
                ],
                [
                    'name' => 'Storia',
                    'description' => 'Rappresenta conoscenze su eventi, popoli, luoghi e civiltà del passato.',
                ],
            ],

            //Abilità basate sulla Saggezza
            'SAG' => [
                [
                    'name' => 'Addestrare Animali',
                    'description' => 'Rappresenta la capacità di comprendere, calmare e guidare gli animali.',
                ],
                [
                    'name' => 'Intuizione',
                    'description' => 'Rappresenta la capacità di comprendere intenzioni, emozioni e menzogne.',
                ],
                [
                    'name' => 'Medicina',
                    'description' => 'Rappresenta la capacità di stabilizzare e riconoscere ferite o malattie.',
                ],
                [
                    'name' => 'Percezione',
                    'description' => 'Rappresenta la capacità di notare suoni, movimenti e dettagli nell’ambiente.',
                ],
                [
                    'name' => 'Sopravvivenza',
                    'description' => 'Rappresenta la capacità di orientarsi, seguire tracce e vivere nella natura.',
                ],
            ],

            //Abilità basate sul Carisma
            'CAR' => [
                [
                    'name' => 'Inganno',
                    'description' => 'Rappresenta la capacità di mentire o nascondere le proprie intenzioni.',
                ],
                [
                    'name' => 'Intimidire',
                    'description' => 'Rappresenta la capacità di influenzare qualcuno tramite minacce o pressione.',
                ],
                [
                    'name' => 'Intrattenere',
                    'description' => 'Rappresenta la capacità di coinvolgere un pubblico con un’esibizione.',
                ],
                [
                    'name' => 'Persuasione',
                    'description' => 'Rappresenta la capacità di influenzare gli altri con tatto e argomentazioni.',
                ],
            ],
        ];

        //Esamina ogni gruppo di abilità
        foreach ($skillsByAbility as $shortName => $skills) {
            //Recupera la caratteristica tramite l'abbreviazione
            $ability = Ability::query()
                ->where('short_name', $shortName)
                ->firstOrFail();

            //Inserisce o aggiorna le abilità della caratteristica
            foreach ($skills as $skill) {
                $ability->skills()->updateOrCreate(
                    //Identifica l'abilità tramite il nome
                    [
                        'name' => $skill['name'],
                    ],

                    //Inserisce o aggiorna tutti i dati
                    $skill
                );
            }
        }
    }
}
