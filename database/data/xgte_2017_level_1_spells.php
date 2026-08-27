<?php

//Valori condivisi dagli incantesimi di 1° livello di Xanathar
$defaults = [
    'level' => 1,
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
];

//Completa i dati e il profilo del bersaglio di ogni incantesimo
$spell = function (array $data) use ($defaults): array {
    $target = array_replace([
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

    $data['target'] = $target;

    return array_replace($defaults, $data);
};

//Restituisce i 10 incantesimi di 1° livello di Xanathar
return [
    $spell([
        'key' => 'absorb_elements',
        'name' => 'Assorbire Elementi',
        'school_key' => 'abjuration',
        'page' => 151,
        'casting_time_type' => 'reaction',
        'casting_trigger' => 'Quando l’incantatore subisce danni da '
            . 'acido, freddo, fulmine, fuoco o tuono.',
        'range_type' => 'self',
        'somatic_component' => true,
        'duration_type' => 'round',
        'duration_value' => 1,
        'description' => 'Assorbe parte dell’energia elementale '
            . 'subita, conferendo resistenza e potenziando il '
            . 'successivo attacco in mischia.',
        'higher_levels' => 'Il danno extra aumenta di 1d6 per ogni '
            . 'slot di livello superiore al 1°.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Assorbire Elementi',
                'application_type' => 'on_hit',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Assorbe parte dell’energia elementale subita, conferendo resistenza e potenziando il successivo attacco in mischia.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 1,
                        'duration_unit' => 'round',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo del danno che ha attivato la reazione, al primo colpo in mischia nel turno successivo. Tipo: Acido.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'damage_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo del danno che ha attivato la reazione, al primo colpo in mischia nel turno successivo. Tipo: Freddo.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => false,
                        'sort_order' => 3,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo del danno che ha attivato la reazione, al primo colpo in mischia nel turno successivo. Tipo: Fuoco.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'damage_lightning',
                        'damage_type' => 'Fulmine',
                        'is_primary' => false,
                        'sort_order' => 4,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo del danno che ha attivato la reazione, al primo colpo in mischia nel turno successivo. Tipo: Fulmine.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'damage_thunder',
                        'damage_type' => 'Tuono',
                        'is_primary' => false,
                        'sort_order' => 5,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo del danno che ha attivato la reazione, al primo colpo in mischia nel turno successivo. Tipo: Tuono.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'catapult',
        'name' => 'Catapulta',
        'school_key' => 'transmutation',
        'page' => 152,
        'range' => 18.288,
        'somatic_component' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Scaglia un piccolo oggetto non trasportato '
            . 'lungo una linea, danneggiando l’oggetto e ciò contro '
            . 'cui impatta.',
        'higher_levels' => 'Il peso massimo aumenta di 2,5 kg e il '
            . 'danno di 1d8 per ogni slot oltre il 1°.',
        'target' => [
            'target_type' => 'object',
            'target_count' => 1,
            'can_target_objects' => true,
            'notes' => 'L’oggetto vola fino a 27,432 metri e può '
                . 'colpire una creatura o una superficie.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Catapulta',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Scaglia un piccolo oggetto non trasportato lungo una linea, danneggiando l’oggetto e ciò contro cui impatta.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 3,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'ceremony',
        'name' => 'Cerimonia',
        'school_key' => 'abjuration',
        'page' => 152,
        'casting_time_type' => 'hour',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Polvere argentata del valore di '
            . '25 mo, che l’incantesimo consuma.',
        'material_consumed' => true,
        'material_cost' => 25,
        'ritual' => true,
        'description' => 'Celebra un rito religioso magico, come '
            . 'benedire acqua, compiere un’espiazione, una dedizione, '
            . 'un matrimonio o un rito funebre.',
        'target' => [
            'target_type' => 'special',
            'can_target_self' => true,
            'can_target_objects' => true,
            'notes' => 'Il bersaglio varia in base al rito scelto e '
                . 'deve restare entro 3,048 metri durante il lancio.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Cerimonia',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Celebra un rito religioso magico, come benedire acqua, compiere un’espiazione, una dedizione, un matrimonio o un rito funebre.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'ability_check',
                        'modifier_type' => 'bonus',
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 4,
                        'condition' => 'Prove: solo rito Raggiungimento della Maggiore Età. Salvezze: solo rito Dedizione. Dura 24 ore e un beneficiario può ricevere ciascun rito una sola volta.',
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'bonus',
                        'sort_order' => 2,
                        'dice_count' => 1,
                        'die_size' => 4,
                        'condition' => 'Prove: solo rito Raggiungimento della Maggiore Età. Salvezze: solo rito Dedizione. Dura 24 ore e un beneficiario può ricevere ciascun rito una sola volta.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'zephyr_strike',
        'name' => 'Colpo dello Zefiro',
        'school_key' => 'transmutation',
        'page' => 153,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Impedisce al movimento dell’incantatore di '
            . 'provocare attacchi di opportunità e potenzia una volta '
            . 'un attacco con un’arma e la sua velocità.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Colpo dello Zefiro',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Impedisce al movimento dell’incantatore di provocare attacchi di opportunità e potenzia una volta un attacco con un’arma e la sua velocità.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 1,
                        'duration_unit' => 'minute',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_force',
                        'damage_type' => 'Forza',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Una sola volta durante la durata: prima di tirare si sceglie l’attacco con arma con vantaggio; il danno extra si applica se colpisce.',
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                        'condition' => 'Una sola volta durante la durata, su un attacco con arma scelto prima del tiro.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'ice_knife',
        'name' => 'Coltello di Ghiaccio',
        'school_key' => 'conjuration',
        'page' => 153,
        'range' => 18.288,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia d’acqua o un frammento '
            . 'di ghiaccio.',
        'attack_type' => 'ranged',
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Scaglia un frammento di ghiaccio contro una '
            . 'creatura; dopo l’attacco il frammento esplode e può '
            . 'danneggiare le creature vicine.',
        'higher_levels' => 'Il danno da freddo dell’esplosione aumenta '
            . 'di 1d6 per ogni slot oltre il 1°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 1.524,
            'notes' => 'La sfera è centrata sulla creatura bersaglio '
                . 'dell’attacco con incantesimo a distanza.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Coltello di Ghiaccio',
                'application_type' => 'on_hit',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Scaglia un frammento di ghiaccio contro una creatura; dopo l’attacco il frammento esplode e può danneggiare le creature vicine.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_piercing',
                        'damage_type' => 'Perforante',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                    ],
                    [
                        'key' => 'ice_explosion',
                        'damage_type' => 'Freddo',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Dopo l’attacco, anche se manca: bersaglio e creature entro 1,5 metri, TS Destrezza, nessun danno da freddo se riesce.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'chaos_bolt',
        'name' => 'Dardo di Caos',
        'school_key' => 'evocation',
        'page' => 155,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'attack_type' => 'ranged',
        'description' => 'Scaglia energia caotica contro una creatura; '
            . 'il tipo di danno è casuale e l’energia può balzare '
            . 'verso un altro bersaglio.',
        'higher_levels' => 'Il danno aumenta di 1d6 per ogni slot di '
            . 'livello superiore al 1°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'notes' => 'Può balzare verso creature differenti entro '
                . '9,144 metri dal bersaglio precedente.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Dardo di Caos',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Scaglia energia caotica contro una creatura; il tipo di danno è casuale e l’energia può balzare verso un altro bersaglio.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'chaos_d8_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 1 (Acido) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                    ],
                    [
                        'key' => 'chaos_d6_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 1 (Acido) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'chaos_d8_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => false,
                        'sort_order' => 3,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 2 (Freddo) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                    ],
                    [
                        'key' => 'chaos_d6_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => false,
                        'sort_order' => 4,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 2 (Freddo) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'chaos_d8_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => false,
                        'sort_order' => 5,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 3 (Fuoco) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                    ],
                    [
                        'key' => 'chaos_d6_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => false,
                        'sort_order' => 6,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 3 (Fuoco) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'chaos_d8_force',
                        'damage_type' => 'Forza',
                        'is_primary' => false,
                        'sort_order' => 7,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 4 (Forza) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                    ],
                    [
                        'key' => 'chaos_d6_force',
                        'damage_type' => 'Forza',
                        'is_primary' => false,
                        'sort_order' => 8,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 4 (Forza) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'chaos_d8_lightning',
                        'damage_type' => 'Fulmine',
                        'is_primary' => false,
                        'sort_order' => 9,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 5 (Fulmine) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                    ],
                    [
                        'key' => 'chaos_d6_lightning',
                        'damage_type' => 'Fulmine',
                        'is_primary' => false,
                        'sort_order' => 10,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 5 (Fulmine) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'chaos_d8_poison',
                        'damage_type' => 'Veleno',
                        'is_primary' => false,
                        'sort_order' => 11,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 6 (Veleno) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                    ],
                    [
                        'key' => 'chaos_d6_poison',
                        'damage_type' => 'Veleno',
                        'is_primary' => false,
                        'sort_order' => 12,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 6 (Veleno) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'chaos_d8_psychic',
                        'damage_type' => 'Psichico',
                        'is_primary' => false,
                        'sort_order' => 13,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 7 (Psichico) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                    ],
                    [
                        'key' => 'chaos_d6_psychic',
                        'damage_type' => 'Psichico',
                        'is_primary' => false,
                        'sort_order' => 14,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 7 (Psichico) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'chaos_d8_thunder',
                        'damage_type' => 'Tuono',
                        'is_primary' => false,
                        'sort_order' => 15,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 8 (Tuono) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                    ],
                    [
                        'key' => 'chaos_d6_thunder',
                        'damage_type' => 'Tuono',
                        'is_primary' => false,
                        'sort_order' => 16,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se si sceglie il tipo 8 (Tuono) indicato da uno dei due d8 del danno. Tutte le componenti usano lo stesso tipo; gli altri tipi non si sommano.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                ],
                'notes' => 'Si tirano solo 2d8 e 1d6, più l’incremento dello slot. I d8 determinano anche il tipo. Se mostrano lo stesso numero, si può effettuare un nuovo attacco contro un altro bersaglio entro 9 metri; ciascuna creatura può essere bersagliata una sola volta per lancio.',
            ],
        ],
    ]),

    $spell([
        'key' => 'cause_fear',
        'name' => 'Incuti Paura',
        'school_key' => 'necromancy',
        'page' => 160,
        'range' => 18.288,
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Costringe una creatura visibile a confrontarsi '
            . 'con la propria mortalità, potendo renderla spaventata.',
        'higher_levels' => 'Può bersagliare una creatura aggiuntiva per '
            . 'ogni slot di livello superiore al 1°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Costrutti e non morti sono immuni.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Incuti Paura',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Costringe una creatura visibile a confrontarsi con la propria mortalità, potendo renderla spaventata.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 1,
                        'duration_unit' => 'minute',
                        'sort_order' => 1,
                    ],
                ],
                'scalings' => [
                    [
                        'key' => 'base_target_count',
                        'target_field' => 'target_count',
                        'source_type' => 'fixed',
                        'fixed_value' => 1,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_target_count',
                        'target_field' => 'target_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 2,
                        'source_offset' => -1,
                        'multiplier' => 1,
                        'divisor' => 1,
                        'rounding' => 'none',
                        'sort_order' => 1,
                        'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'beast_bond',
        'name' => 'Legame con le Bestie',
        'school_key' => 'divination',
        'page' => 161,
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un ciuffo di pelliccia avvolto in '
            . 'un pezzo di stoffa.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Stabilisce un legame telepatico con una '
            . 'bestia amichevole o affascinata e ne migliora gli '
            . 'attacchi contro i nemici vicini all’incantatore.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'La bestia deve avere Intelligenza inferiore a '
                . '4 e il legame richiede linea di vista reciproca.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Legame con le Bestie',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Stabilisce un legame telepatico con una bestia amichevole o affascinata e ne migliora gli attacchi contro i nemici vicini all’incantatore.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 10,
                        'duration_unit' => 'minute',
                        'sort_order' => 1,
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                        'condition' => 'Solo attacchi della bestia contro una creatura entro 1,5 metri dall’incantatore che egli sia in grado di vedere.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'earth_tremor',
        'name' => 'Scossa Tellurica',
        'school_key' => 'evocation',
        'page' => 167,
        'range' => 3.048,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Scuote il terreno attorno all’incantatore, '
            . 'danneggiando e facendo cadere le creature e rendendo '
            . 'difficile il terreno smosso.',
        'higher_levels' => 'Il danno aumenta di 1d6 per ogni slot di '
            . 'livello superiore al 1°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 3.048,
            'can_target_objects' => true,
            'notes' => 'L’area esclude l’incantatore e influenza il '
                . 'terreno di pietra o terriccio smosso.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Scossa Tellurica',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Scuote il terreno attorno all’incantatore, danneggiando e facendo cadere le creature e rendendo difficile il terreno smosso.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'snare',
        'name' => 'Trabocchetto',
        'school_key' => 'abjuration',
        'page' => 170,
        'casting_time_type' => 'minute',
        'range_type' => 'touch',
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => '7,5 metri di corda, che '
            . 'l’incantesimo consuma.',
        'material_consumed' => true,
        'duration_type' => 'hour',
        'duration_value' => 8,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Trasforma una corda in una trappola magica '
            . 'quasi invisibile che solleva e trattiene una creatura '
            . 'che entra nella sua area.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'circle',
            'area_size_meters' => 1.524,
            'can_target_objects' => true,
            'notes' => 'Il cerchio viene tracciato sul terreno o sul '
                . 'pavimento e si attiva una sola volta.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Trabocchetto',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Trasforma una corda in una trappola magica quasi invisibile che solleva e trattiene una creatura che entra nella sua area.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 8,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),
];
