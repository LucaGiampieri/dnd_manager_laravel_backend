<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use App\Models\WeaponProperty;
use Illuminate\Database\Seeder;

class WeaponPropertySeeder extends Seeder
{
    //Inserisce le proprietà ufficiali delle armi del regolamento 2014
    public function run(): void
    {
        //Garantisce la presenza del regolamento richiesto
        $this->call(RulesetSeeder::class);

        //Recupera il regolamento D&D 5e del 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Definisce tutte le proprietà base delle armi
        $properties = [
            'ammunition' => [
                'name' => 'Munizioni',
                'description' =>
                    'L’arma richiede una munizione per effettuare '
                    . 'normalmente un attacco a distanza. Ogni attacco '
                    . 'consuma una singola munizione.',
            ],
            'finesse' => [
                'name' => 'Accurata',
                'description' =>
                    'Il personaggio può utilizzare Forza oppure '
                    . 'Destrezza per i tiri per colpire e per i danni, '
                    . 'usando la stessa caratteristica per entrambi.',
            ],
            'heavy' => [
                'name' => 'Pesante',
                'description' =>
                    'Le creature Piccole o Minuscole subiscono '
                    . 'svantaggio ai tiri per colpire effettuati '
                    . 'con questa arma.',
            ],
            'light' => [
                'name' => 'Leggera',
                'description' =>
                    'L’arma è piccola e maneggevole, risultando adatta '
                    . 'al combattimento con due armi.',
            ],
            'loading' => [
                'name' => 'Ricarica',
                'description' =>
                    'Il tempo necessario per ricaricare limita l’arma '
                    . 'a una sola munizione per ogni azione, azione bonus '
                    . 'o reazione utilizzata per attaccare.',
            ],
            'range' => [
                'name' => 'Gittata',
                'description' =>
                    'L’arma indica una gittata normale e una gittata '
                    . 'lunga. Oltre la gittata normale l’attacco subisce '
                    . 'svantaggio; oltre quella lunga non è possibile '
                    . 'attaccare.',
            ],
            'reach' => [
                'name' => 'Portata',
                'description' =>
                    'L’arma aumenta di 1,5 metri la portata del '
                    . 'personaggio durante gli attacchi e nel determinare '
                    . 'la portata degli attacchi di opportunità.',
            ],
            'special' => [
                'name' => 'Speciale',
                'description' =>
                    'L’arma utilizza regole particolari descritte '
                    . 'nel proprio profilo o nelle proprie note.',
            ],
            'thrown' => [
                'name' => 'Da lancio',
                'description' =>
                    'L’arma può essere lanciata per effettuare un attacco '
                    . 'a distanza. Se è un’arma da mischia, utilizza la '
                    . 'stessa caratteristica prevista per il suo attacco '
                    . 'in mischia.',
            ],
            'two_handed' => [
                'name' => 'A due mani',
                'description' =>
                    'L’arma richiede l’utilizzo di due mani '
                    . 'quando viene effettuato un attacco.',
            ],
            'versatile' => [
                'name' => 'Versatile',
                'description' =>
                    'L’arma può essere utilizzata con una o due mani. '
                    . 'Quando viene impugnata con due mani utilizza '
                    . 'il danno alternativo indicato nel suo profilo.',
            ],
        ];

        //Prepara il primo valore dell'ordinamento
        $sortOrder = 10;

        //Inserisce o aggiorna ogni proprietà senza duplicarla
        foreach ($properties as $key => $property) {
            WeaponProperty::query()->updateOrCreate(
                [
                    'ruleset_id' => $ruleset->id,
                    'key' => $key,
                ],
                [
                    'name' => $property['name'],
                    'description' => $property['description'],
                    'notes' => null,
                    'sort_order' => $sortOrder,
                ]
            );

            //Prepara l'ordinamento della proprietà successiva
            $sortOrder += 10;
        }
    }
}
