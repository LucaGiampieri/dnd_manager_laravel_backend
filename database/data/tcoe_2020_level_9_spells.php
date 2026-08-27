<?php

//Valori condivisi dagli incantesimi di 9° livello di Tasha
$defaults = [
    'level' => 9,
    'casting_time_value' => 1,
    'casting_time_type' => 'action',
    'casting_trigger' => null,
    'range_type' => 'distance',
    'range' => null,
    'verbal_component' => false,
    'somatic_component' => false,
    'material_component' => false,
    'material_description' => null,
    'material_consumed' => false,
    'material_cost' => null,
    'duration_type' => 'instantaneous',
    'duration_value' => null,
    'concentration' => false,
    'ritual' => false,
    'attack_type' => null,
    'saving_throw' => null,
    'save_success_damage' => null,
    'higher_levels' => null,
    'notes' => null,
    'effects' => [],
    'summons' => [],
];

//Completa i dati e il profilo del bersaglio di ogni incantesimo
$spell = function (array $data) use ($defaults): array {
    $data['target'] = array_replace([
        'target_type' => 'special',
        'target_count' => null,
        'area_shape' => null,
        'area_size_meters' => null,
        'area_secondary_size_meters' => null,
        'can_target_self' => false,
        'can_target_objects' => false,
        'requires_sight' => false,
        'notes' => null,
    ], $data['target'] ?? []);

    return array_replace($defaults, $data);
};

//Restituisce l'unico incantesimo di 9° livello introdotto da Tasha
return [
    //Lama del Disastro
    $spell([
        'key' => 'blade_of_disaster',
        'name' => 'Lama del Disastro',
        'school_key' => 'conjuration',
        'page' => 112,
        'casting_time_type' => 'bonus_action',
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'attack_type' => 'melee',
        'description' => 'Crea una spaccatura planare a forma di '
            . 'lama che può effettuare fino a due attacchi in '
            . 'mischia con incantesimo, infliggendo danni da forza '
            . 'e ottenendo un critico con 18–20.',
        'notes' => 'La lama può attraversare qualsiasi barriera, '
            . 'incluso un muro di forza.',
        'target' => [
            'target_type' => 'special',
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'La lama compare in uno spazio libero visibile. '
                . 'Ogni attacco può colpire una creatura, un oggetto '
                . 'non equipaggiato o una struttura entro 1,524 '
                . 'metri dalla lama.',
        ],
        'effects' => [
            //Danni normali e danni aggiuntivi del colpo critico
            [
                'key' => 'planar_blade_attacks',
                'name' => 'Attacchi della Lama Planare',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'description' => 'Al lancio, e dopo averla mossa nei '
                    . 'turni successivi, l’incantatore può effettuare '
                    . 'fino a due attacchi in mischia con incantesimo '
                    . 'contro bersagli entro 1,524 metri dalla lama.',
                'sort_order' => 1,
                'damages' => [
                    [
                        'key' => 'force_damage',
                        'damage_type' => 'Forza',
                        'dice_count' => 4,
                        'die_size' => 12,
                        'is_primary' => true,
                        'condition' => 'Quando un attacco della lama '
                            . 'colpisce.',
                        'sort_order' => 1,
                    ],
                    [
                        'key' => 'critical_force_damage',
                        'damage_type' => 'Forza',
                        'dice_count' => 8,
                        'die_size' => 12,
                        'is_primary' => false,
                        'condition' => 'Danno extra quando il tiro '
                            . 'per colpire ottiene 18, 19 o 20.',
                        'sort_order' => 2,
                        'notes' => 'Il totale del colpo critico è '
                            . '12d12 danni da forza.',
                    ],
                ],
            ],

            //Movimento della lama nei turni successivi
            [
                'key' => 'mobile_planar_blade',
                'name' => 'Movimento della Lama',
                'application_type' => 'manual',
                'target_scope' => 'special',
                'description' => 'Come azione bonus, l’incantatore '
                    . 'muove la lama fino a 9,144 metri verso uno '
                    . 'spazio libero visibile entro gittata e può '
                    . 'poi effettuare fino a due attacchi con essa.',
                'sort_order' => 2,
            ],
        ],
    ]),
];
