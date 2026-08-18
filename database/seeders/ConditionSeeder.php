<?php

namespace Database\Seeders;

use App\Models\Condition;
use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    public function run(): void
    {
        //Recupera il regolamento D&D 5e 2014
        //al quale appartengono tutte le condizioni
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Definisce le quindici condizioni del regolamento
        $conditions = [
            [
                'key' => 'blinded',
                'name' => 'Accecato',
                'description' => 'La creatura non è in grado di vedere e fallisce le prove basate sulla vista.',
            ],
            [
                'key' => 'charmed',
                'name' => 'Affascinato',
                'description' => 'La creatura è influenzata magicamente da chi l’ha affascinata.',
            ],
            [
                'key' => 'grappled',
                'name' => 'Afferrato',
                'description' => 'La velocità della creatura diventa zero finché la presa non termina.',
            ],
            [
                'key' => 'deafened',
                'name' => 'Assordato',
                'description' => 'La creatura non è in grado di sentire e fallisce le prove basate sull’udito.',
            ],
            [
                'key' => 'poisoned',
                'name' => 'Avvelenato',
                'description' => 'La creatura subisce penalità mentre è sotto l’effetto del veleno.',
            ],
            [
                'key' => 'incapacitated',
                'name' => 'Incapacitato',
                'description' => 'La creatura non può compiere azioni o reazioni.',
            ],
            [
                'key' => 'restrained',
                'name' => 'Intralciato',
                'description' => 'I movimenti della creatura sono fortemente limitati.',
            ],
            [
                'key' => 'invisible',
                'name' => 'Invisibile',
                'description' => 'La creatura non può essere vista senza l’aiuto di magia o sensi speciali.',
            ],
            [
                'key' => 'paralyzed',
                'name' => 'Paralizzato',
                'description' => 'La creatura è incapacitata e non può muoversi o parlare.',
            ],
            [
                'key' => 'petrified',
                'name' => 'Pietrificato',
                'description' => 'La creatura e ciò che trasporta vengono trasformati in una sostanza solida inanimata.',
            ],
            [
                'key' => 'unconscious',
                'name' => 'Privo di Sensi',
                'description' => 'La creatura è inconsapevole dell’ambiente circostante e rimane incapacitata.',
            ],
            [
                'key' => 'prone',
                'name' => 'Prono',
                'description' => 'La creatura si trova a terra e i suoi movimenti e attacchi sono limitati.',
            ],
            [
                'key' => 'frightened',
                'name' => 'Spaventato',
                'description' => 'La creatura è condizionata dalla presenza della fonte della sua paura.',
            ],
            [
                'key' => 'stunned',
                'name' => 'Stordito',
                'description' => 'La creatura è incapacitata, non può muoversi e riesce a parlare solo in modo esitante.',
            ],
            [
                'key' => 'exhaustion',
                'name' => 'Sfinimento',
                'description' => 'Condizione progressiva articolata in sei livelli di gravità.',
                'is_level_based' => true,
                'maximum_level' => 6,
            ],
        ];

        //Crea o aggiorna ogni condizione senza produrre duplicati
        foreach ($conditions as $condition) {
            $ruleset->conditions()->updateOrCreate(
                [
                    'key' => $condition['key'],
                ],
                [
                    'name' => $condition['name'],
                    'description' => $condition['description'],
                    'is_level_based' =>
                        $condition['is_level_based'] ?? false,
                    'maximum_level' =>
                        $condition['maximum_level'] ?? null,
                ]
            );
        }

        //Recupera lo Sfinimento appena creato
        /** @var Condition $exhaustion */
        $exhaustion = $ruleset->conditions()
            ->where('key', 'exhaustion')
            ->firstOrFail();

        //Definisce gli effetti cumulativi dei sei livelli di Sfinimento
        $exhaustionLevels = [
            [
                'level' => 1,
                'name' => 'Livello 1',
                'description' => 'Svantaggio alle prove di caratteristica.',
            ],
            [
                'level' => 2,
                'name' => 'Livello 2',
                'description' => 'Velocità dimezzata.',
            ],
            [
                'level' => 3,
                'name' => 'Livello 3',
                'description' => 'Svantaggio ai tiri per colpire e ai tiri salvezza.',
            ],
            [
                'level' => 4,
                'name' => 'Livello 4',
                'description' => 'Massimo dei punti ferita dimezzato.',
            ],
            [
                'level' => 5,
                'name' => 'Livello 5',
                'description' => 'Velocità ridotta a zero.',
            ],
            [
                'level' => 6,
                'name' => 'Livello 6',
                'description' => 'Morte.',
                'is_terminal' => true,
            ],
        ];

        //Relazione uno-a-molti (HasMany):
        //una condizione di Sfinimento può possedere sei livelli
        foreach ($exhaustionLevels as $exhaustionLevel) {
            $exhaustion->levels()->updateOrCreate(
                [
                    'level' => $exhaustionLevel['level'],
                ],
                [
                    'name' => $exhaustionLevel['name'],
                    'description' => $exhaustionLevel['description'],
                    'is_terminal' =>
                        $exhaustionLevel['is_terminal'] ?? false,
                ]
            );
        }
    }
}
