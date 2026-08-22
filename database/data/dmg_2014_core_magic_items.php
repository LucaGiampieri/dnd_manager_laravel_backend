<?php

//Definisce il primo catalogo strutturato di oggetti magici del DMG 2014
return [
    //Pozioni di guarigione con potenze differenti
    'healing_potions' => [
        [
            'key' => 'potion_of_healing',
            'name' => 'Pozione di Guarigione',
            'rarity' => 'common',
            'dice_count' => 2,
            'die_size' => 4,
            'flat_bonus' => 2,
            'average_healing' => 7,
            'description' =>
                'Quando viene bevuta, questa pozione permette '
                . 'di recuperare 2d4 + 2 punti ferita.',
            'sort_order' => 100,
            'page' => 187,
        ],
        [
            'key' => 'potion_of_greater_healing',
            'name' => 'Pozione di Guarigione Superiore',
            'rarity' => 'uncommon',
            'dice_count' => 4,
            'die_size' => 4,
            'flat_bonus' => 4,
            'average_healing' => 14,
            'description' =>
                'Quando viene bevuta, questa pozione permette '
                . 'di recuperare 4d4 + 4 punti ferita.',
            'sort_order' => 110,
            'page' => 187,
        ],
        [
            'key' => 'potion_of_superior_healing',
            'name' => 'Pozione di Guarigione Suprema',
            'rarity' => 'rare',
            'dice_count' => 8,
            'die_size' => 4,
            'flat_bonus' => 8,
            'average_healing' => 28,
            'description' =>
                'Quando viene bevuta, questa pozione permette '
                . 'di recuperare 8d4 + 8 punti ferita.',
            'sort_order' => 120,
            'page' => 187,
        ],
        [
            'key' => 'potion_of_supreme_healing',
            'name' => 'Pozione di Guarigione Massima',
            'rarity' => 'very_rare',
            'dice_count' => 10,
            'die_size' => 4,
            'flat_bonus' => 20,
            'average_healing' => 45,
            'description' =>
                'Quando viene bevuta, questa pozione permette '
                . 'di recuperare 10d4 + 20 punti ferita.',
            'sort_order' => 130,
            'page' => 187,
        ],
    ],

    //Modelli applicabili a qualsiasi arma non magica
    'magic_weapons' => [
        [
            'key' => 'weapon_plus_1',
            'name' => 'Arma +1',
            'rarity' => 'uncommon',
            'bonus' => 1,
            'description' =>
                'Questa arma magica concede un bonus di +1 '
                . 'ai tiri per colpire e ai tiri per i danni.',
            'sort_order' => 200,
            'page' => 213,
        ],
        [
            'key' => 'weapon_plus_2',
            'name' => 'Arma +2',
            'rarity' => 'rare',
            'bonus' => 2,
            'description' =>
                'Questa arma magica concede un bonus di +2 '
                . 'ai tiri per colpire e ai tiri per i danni.',
            'sort_order' => 210,
            'page' => 213,
        ],
        [
            'key' => 'weapon_plus_3',
            'name' => 'Arma +3',
            'rarity' => 'very_rare',
            'bonus' => 3,
            'description' =>
                'Questa arma magica concede un bonus di +3 '
                . 'ai tiri per colpire e ai tiri per i danni.',
            'sort_order' => 220,
            'page' => 213,
        ],
    ],

    //Contenitori magici extradimensionali
    'containers' => [
        [
            'key' => 'bag_of_holding',
            'name' => 'Borsa Conservante',
            'rarity' => 'uncommon',
            'weight_kg' => 6.804,
            'capacity_weight_kg' => 226.796,
            'capacity_volume_liters' => 1812.278,
            'description' =>
                'Questa borsa possiede uno spazio extradimensionale '
                . 'molto più ampio delle sue dimensioni esterne.',
            'dimensions' =>
                'L’apertura misura circa 60 centimetri di diametro '
                . 'e lo spazio interno è profondo circa 1,2 metri.',
            'living_creature_rules' =>
                'Le creature che respirano possono sopravvivere '
                . 'per un numero limitato di minuti, determinato '
                . 'dal numero di creature presenti.',
            'rupture_rules' =>
                'Se viene sovraccaricata, perforata o strappata, '
                . 'la borsa viene distrutta e il contenuto viene '
                . 'disperso nel Piano Astrale.',
            'nesting_rules' =>
                'Inserire la borsa in un altro spazio extradimensionale '
                . 'distrugge entrambi gli oggetti e apre temporaneamente '
                . 'un varco verso il Piano Astrale.',
            'sort_order' => 300,
            'page' => 153,
        ],
    ],
];
