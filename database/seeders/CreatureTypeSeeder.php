<?php

namespace Database\Seeders;

use App\Models\CreatureType;
use Illuminate\Database\Seeder;

class CreatureTypeSeeder extends Seeder
{
    public function run(): void
    {
        $creatureTypes = [
            [
                'key' => 'aberration',
                'name' => 'Aberrazione',
                'description' => 'Creatura dalla natura profondamente aliena, spesso dotata di anatomia, mente o capacità estranee al mondo naturale.',
                'sort_order' => 1,
            ],
            [
                'key' => 'beast',
                'name' => 'Bestia',
                'description' => 'Creatura non umanoide appartenente al mondo naturale, come un animale comune, un dinosauro o una sua variante gigante.',
                'sort_order' => 2,
            ],
            [
                'key' => 'celestial',
                'name' => 'Celestiale',
                'description' => 'Creatura originaria dei Piani Superiori, spesso legata a divinità benevole o a poteri cosmici del bene.',
                'sort_order' => 3,
            ],
            [
                'key' => 'construct',
                'name' => 'Costrutto',
                'description' => 'Creatura costruita o animata artificialmente invece di essere nata, spesso attraverso magia, artigianato o altri procedimenti.',
                'sort_order' => 4,
            ],
            [
                'key' => 'dragon',
                'name' => 'Drago',
                'description' => 'Creatura rettiliforme antica e innatamente magica, comprendente draghi veri e altre creature di natura draconica.',
                'sort_order' => 5,
            ],
            [
                'key' => 'elemental',
                'name' => 'Elementale',
                'description' => 'Creatura originaria dei Piani Elementali o costituita principalmente dalle forze fondamentali di aria, acqua, fuoco o terra.',
                'sort_order' => 6,
            ],
            [
                'key' => 'fey',
                'name' => 'Folletto',
                'description' => 'Creatura magica strettamente legata alla natura, alle emozioni e alla Selva Fatata.',
                'sort_order' => 7,
            ],
            [
                'key' => 'fiend',
                'name' => 'Immondo',
                'description' => 'Creatura malvagia originaria dei Piani Inferiori, come un demone, un diavolo o uno yugoloth.',
                'sort_order' => 8,
            ],
            [
                'key' => 'giant',
                'name' => 'Gigante',
                'description' => 'Creatura umanoide di dimensioni imponenti appartenente ai diversi popoli e lignaggi dei giganti.',
                'sort_order' => 9,
            ],
            [
                'key' => 'humanoid',
                'name' => 'Umanoide',
                'description' => 'Creatura dotata generalmente di forma umanoide, società, cultura, linguaggio e capacità di utilizzare strumenti.',
                'sort_order' => 10,
            ],
            [
                'key' => 'monstrosity',
                'name' => 'Mostruosità',
                'description' => 'Creatura innaturale o terrificante che non appartiene pienamente alle normali categorie del mondo naturale.',
                'sort_order' => 11,
            ],
            [
                'key' => 'ooze',
                'name' => 'Melma',
                'description' => 'Creatura gelatinosa, normalmente priva di una forma anatomica stabile, che si muove e si nutre in modo elementare.',
                'sort_order' => 12,
            ],
            [
                'key' => 'plant',
                'name' => 'Vegetale',
                'description' => 'Creatura di natura vegetale o fungina dotata di capacità sufficienti per agire come una creatura, distinta dalle piante comuni.',
                'sort_order' => 13,
            ],
            [
                'key' => 'undead',
                'name' => 'Non Morto',
                'description' => 'Creatura un tempo vivente oppure corpo o spirito animato da energia necromantica, maledizioni o altri poteri legati alla morte.',
                'sort_order' => 14,
            ],
        ];

        foreach ($creatureTypes as $creatureType) {
            CreatureType::updateOrCreate(
                [
                    'key' => $creatureType['key'],
                ],
                [
                    'name' => $creatureType['name'],
                    'description' => $creatureType['description'],
                    'sort_order' => $creatureType['sort_order'],
                    'notes' => null,
                ]
            );
        }
    }
}
