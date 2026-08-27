<?php

//Valori condivisi dai trucchetti di Xanathar
$defaults = [
    'level' => 0,
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

//Restituisce i 12 trucchetti della Guida di Xanathar
return [
    $spell([
        'key' => 'control_flames',
        'name' => 'Controllare Fiamme',
        'school_key' => 'transmutation',
        'page' => 153,
        'range' => 18.288,
        'somatic_component' => true,
        'duration_type' => 'special',
        'description' => 'Permette di espandere, estinguere o '
            . 'modificare una fiamma non magica visibile contenuta '
            . 'in un piccolo cubo.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Influenza una fiamma non magica; alcuni '
                . 'effetti sono istantanei e altri durano 1 ora.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Controllare Fiamme',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Permette di espandere, estinguere o modificare una fiamma non magica visibile contenuta in un piccolo cubo.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'until_source_ends',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'create_bonfire',
        'name' => 'Creare Falò',
        'school_key' => 'conjuration',
        'page' => 154,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Crea su un punto del terreno un falò '
            . 'magico che danneggia le creature nel suo spazio e '
            . 'può incendiare oggetti infiammabili.',
        'higher_levels' => 'Il danno aumenta a 2d8 al 5° livello, '
            . '3d8 all’11° e 4d8 al 17°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'Il falò occupa un cubo appoggiato sul terreno.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Creare Falò',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea su un punto del terreno un falò magico che danneggia le creature nel suo spazio e può incendiare oggetti infiammabili.',
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
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'character_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 10,
                                'multiplier' => 0,
                                'flat_value' => 2,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'character_level_11_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 11,
                                'maximum_source' => 16,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'character_level_17_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 17,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 3,
                            ],
                        ],
                    ],
                ],
                'condition' => 'Alla comparsa del falò, prima entrata nel suo spazio in un turno o fine del turno al suo interno: TS Destrezza, nessun danno se riesce.',
            ],
        ],
    ]),

    $spell([
        'key' => 'primal_savagery',
        'name' => 'Ferocia Primordiale',
        'school_key' => 'transmutation',
        'page' => 158,
        'range_type' => 'self',
        'somatic_component' => true,
        'attack_type' => 'melee',
        'description' => 'Sviluppa temporaneamente denti o unghie '
            . 'magiche e consente un attacco in mischia che infligge '
            . 'danni da acido.',
        'higher_levels' => 'Il danno aumenta a 2d10 al 5° livello, '
            . '3d10 all’11° e 4d10 al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'La creatura deve trovarsi entro 1,524 metri '
                . 'dall’incantatore.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Ferocia Primordiale',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Sviluppa temporaneamente denti o unghie magiche e consente un attacco in mischia che infligge danni da acido.',
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
                        'key' => 'damage_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'character_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 10,
                                'multiplier' => 0,
                                'flat_value' => 2,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'character_level_11_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 11,
                                'maximum_source' => 16,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'character_level_17_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 17,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 3,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'gust',
        'name' => 'Folata',
        'school_key' => 'transmutation',
        'page' => 158,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Genera una folata capace di spingere una '
            . 'creatura, muovere un piccolo oggetto oppure produrre '
            . 'un innocuo effetto d’aria.',
        'target' => [
            'target_type' => 'special',
            'target_count' => 1,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'Può influenzare una creatura Media o più '
                . 'piccola, un oggetto leggero o un punto visibile.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Folata',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Genera una folata capace di spingere una creatura, muovere un piccolo oggetto oppure produrre un innocuo effetto d’aria.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'forced_movements' => [
                    [
                        'key' => 'movement_1',
                        'movement_type' => 'push',
                        'origin_type' => 'source',
                        'direction_type' => 'away_from_origin',
                        'distance' => 1.5,
                        'up_to_distance' => false,
                        'straight_line' => true,
                        'stops_at_obstacle' => true,
                        'opportunity_attack_rule' => 'does_not_provoke',
                        'sort_order' => 1,
                        'condition' => 'Solo creatura Media o inferiore e TS Forza fallito.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'infestation',
        'name' => 'Infestazione',
        'school_key' => 'conjuration',
        'page' => 160,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una pulce viva.',
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Circonda brevemente una creatura visibile '
            . 'con parassiti magici, infliggendo danni da veleno e '
            . 'potendo provocare un piccolo movimento casuale.',
        'higher_levels' => 'Il danno aumenta a 2d6 al 5° livello, '
            . '3d6 all’11° e 4d6 al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Infestazione',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Circonda brevemente una creatura visibile con parassiti magici, infliggendo danni da veleno e potendo provocare un piccolo movimento casuale.',
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
                        'key' => 'damage_poison',
                        'damage_type' => 'Veleno',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'character_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 10,
                                'multiplier' => 0,
                                'flat_value' => 2,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'character_level_11_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 11,
                                'maximum_source' => 16,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'character_level_17_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 17,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 3,
                            ],
                        ],
                    ],
                ],
                'forced_movements' => [
                    [
                        'key' => 'movement_1',
                        'movement_type' => 'move',
                        'origin_type' => 'target',
                        'direction_type' => 'random_direction',
                        'distance' => 1.5,
                        'up_to_distance' => false,
                        'straight_line' => true,
                        'stops_at_obstacle' => true,
                        'opportunity_attack_rule' => 'does_not_provoke',
                        'sort_order' => 1,
                        'condition' => 'Solo TS Costituzione fallito: tirare 1d4 per nord, sud, est o ovest; se non può muoversi non si sposta. Non provoca attacchi di opportunità.',
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'other',
                        'modifier_type' => 'special',
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 4,
                        'notes' => '1 nord, 2 sud, 3 est, 4 ovest; direzione del movimento, non danno.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'shape_water',
        'name' => 'Modellare Acqua',
        'school_key' => 'transmutation',
        'page' => 162,
        'range' => 9.144,
        'somatic_component' => true,
        'duration_type' => 'special',
        'description' => 'Muove, modella, colora, rende opaca o '
            . 'congela una piccola quantità d’acqua visibile.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Influenza l’acqua contenuta nel cubo; alcuni '
                . 'effetti sono istantanei e altri durano 1 ora.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Modellare Acqua',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Muove, modella, colora, rende opaca o congela una piccola quantità d’acqua visibile.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'until_source_ends',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'mold_earth',
        'name' => 'Modellare Terra',
        'school_key' => 'transmutation',
        'page' => 162,
        'range' => 9.144,
        'somatic_component' => true,
        'duration_type' => 'special',
        'description' => 'Scava terra smossa, traccia forme su terra '
            . 'o pietra oppure modifica temporaneamente la difficoltà '
            . 'del terreno.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'Influenza terra o pietra nel cubo; alcuni '
                . 'effetti sono istantanei e altri durano 1 ora.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Modellare Terra',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Scava terra smossa, traccia forme su terra o pietra oppure modifica temporaneamente la difficoltà del terreno.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'until_source_ends',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'frostbite',
        'name' => 'Morsa del Gelo',
        'school_key' => 'evocation',
        'page' => 162,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Ricopre una creatura visibile di gelo, '
            . 'infliggendo danni da freddo e ostacolando il suo '
            . 'successivo attacco con un’arma.',
        'higher_levels' => 'Il danno aumenta a 2d6 al 5° livello, '
            . '3d6 all’11° e 4d6 al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Morsa del Gelo',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Ricopre una creatura visibile di gelo, infliggendo danni da freddo e ostacolando il suo successivo attacco con un’arma.',
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
                        'key' => 'damage_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'character_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 10,
                                'multiplier' => 0,
                                'flat_value' => 2,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'character_level_11_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 11,
                                'maximum_source' => 16,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'character_level_17_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 17,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 3,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'spell_effect_2',
                'name' => 'Attacco ostacolato',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Ricopre una creatura visibile di gelo, infliggendo danni da freddo e ostacolando il suo successivo attacco con un’arma.',
                'sort_order' => 2,
                'condition' => 'Solo il prossimo attacco con arma del bersaglio prima della fine del suo turno successivo, dopo il TS fallito.',
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 1,
                        'condition' => 'Solo il prossimo attacco con arma del bersaglio prima della fine del suo turno successivo, dopo il TS fallito.',
                    ],
                ],
                'durations' => [
                    [
                        'key' => 'end_next_turn',
                        'duration_type' => 'until_end_turn',
                        'turn_reference' => 'target',
                        'condition' => 'Fine del prossimo turno del bersaglio oppure consumo sul primo tiro ammesso.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'word_of_radiance',
        'name' => 'Parola Radiosa',
        'school_key' => 'evocation',
        'page' => 164,
        'range' => 1.524,
        'verbal_component' => true,
        'material_component' => true,
        'material_description' => 'Un simbolo sacro.',
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Emette un bagliore divino che infligge '
            . 'danni radiosi alle creature visibili scelte '
            . 'dall’incantatore nelle vicinanze.',
        'higher_levels' => 'Il danno aumenta a 2d6 al 5° livello, '
            . '3d6 all’11° e 4d6 al 17°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Colpisce soltanto le creature scelte '
                . 'dall’incantatore entro la gittata.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Parola Radiosa',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Emette un bagliore divino che infligge danni radiosi alle creature visibili scelte dall’incantatore nelle vicinanze.',
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
                        'key' => 'damage_radiant',
                        'damage_type' => 'Radioso',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'character_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 10,
                                'multiplier' => 0,
                                'flat_value' => 2,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'character_level_11_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 11,
                                'maximum_source' => 16,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'character_level_17_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 17,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 3,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'magic_stone',
        'name' => 'Pietra Magica',
        'school_key' => 'transmutation',
        'page' => 165,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'description' => 'Infunde magia in un massimo di tre sassolini '
            . 'che possono essere scagliati a mano o con una fionda.',
        'target' => [
            'target_type' => 'objects',
            'target_count' => 3,
            'can_target_objects' => true,
            'notes' => 'Bersaglia da uno a tre sassolini toccati.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Pietra Magica',
                'application_type' => 'automatic',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Infunde magia in un massimo di tre sassolini che possono essere scagliati a mano o con una fionda.',
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
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'modifier_source_type' => 'caster_ability_modifier',
                        'notes' => 'Il modificatore è quello dell’incantatore che ha incantato i sassolini, anche se li scaglia un’altra creatura.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'toll_the_dead',
        'name' => 'Rintocco dei Morti',
        'school_key' => 'necromancy',
        'page' => 166,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Fa risuonare una campana funebre attorno '
            . 'a una creatura visibile, infliggendo più danni '
            . 'necrotici se è già ferita.',
        'higher_levels' => 'Aumenta di un dado al 5° livello, di due '
            . 'all’11° e di tre al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Rintocco dei Morti',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Fa risuonare una campana funebre attorno a una creatura visibile, infliggendo più danni necrotici se è già ferita.',
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
                        'key' => 'damage_necrotic',
                        'damage_type' => 'Necrotico',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'character_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 10,
                                'multiplier' => 0,
                                'flat_value' => 2,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'character_level_11_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 11,
                                'maximum_source' => 16,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'character_level_17_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 17,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 3,
                            ],
                        ],
                        'condition' => 'Solo se il bersaglio ha tutti i suoi PF.',
                    ],
                    [
                        'key' => 'wounded_target',
                        'damage_type' => 'Necrotico',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 1,
                        'die_size' => 12,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se il bersaglio ha meno PF dei suoi PF massimi: sostituisce il d8, non si somma.',
                        'scalings' => [
                            [
                                'key' => 'character_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 10,
                                'multiplier' => 0,
                                'flat_value' => 2,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'character_level_11_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 11,
                                'maximum_source' => 16,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'character_level_17_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 17,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 3,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'thunderclap',
        'name' => 'Rombo di Tuono',
        'school_key' => 'evocation',
        'page' => 166,
        'range' => 1.524,
        'somatic_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Genera un’esplosione tonante udibile a '
            . 'distanza che danneggia le creature vicine, escluso '
            . 'l’incantatore.',
        'higher_levels' => 'Il danno aumenta a 2d6 al 5° livello, '
            . '3d6 all’11° e 4d6 al 17°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 1.524,
            'notes' => 'L’emanazione esclude sempre l’incantatore.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Rombo di Tuono',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Genera un’esplosione tonante udibile a distanza che danneggia le creature vicine, escluso l’incantatore.',
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
                        'key' => 'damage_thunder',
                        'damage_type' => 'Tuono',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'character_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 10,
                                'multiplier' => 0,
                                'flat_value' => 2,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'character_level_11_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 11,
                                'maximum_source' => 16,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'character_level_17_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'character_level',
                                'operation' => 'set',
                                'minimum_source' => 17,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 3,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),
];
