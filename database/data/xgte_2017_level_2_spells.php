<?php

//Valori condivisi dagli incantesimi di 2° livello di Xanathar
$defaults = [
    'level' => 2,
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

//Restituisce i 12 incantesimi di 2° livello di Xanathar
return [
    $spell([
        'key' => 'mind_spike',
        'name' => 'Aculeo Mentale',
        'school_key' => 'divination',
        'page' => 151,
        'range' => 18.288,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'half',
        'description' => 'Penetra nella mente di una creatura '
            . 'visibile, infligge danni psichici e, se il tiro '
            . 'fallisce, permette di seguirne la posizione.',
        'higher_levels' => 'Il danno aumenta di 1d8 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Aculeo Mentale',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Penetra nella mente di una creatura visibile, infligge danni psichici e, se il tiro fallisce, permette di seguirne la posizione.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 1,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_psychic',
                        'damage_type' => 'Psichico',
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
                                'minimum_source' => 3,
                                'source_offset' => -2,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'dust_devil',
        'name' => 'Diavoletto di Polvere',
        'school_key' => 'conjuration',
        'page' => 156,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un pizzico di polvere.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'half',
        'description' => 'Evoca un piccolo vortice mobile che '
            . 'danneggia e spinge le creature vicine e può sollevare '
            . 'una nube di detriti.',
        'higher_levels' => 'Il danno aumenta di 1d8 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Il vortice influenza le creature che terminano '
                . 'il turno entro 1,524 metri da esso.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Diavoletto di Polvere',
                'application_type' => 'on_end_turn',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Evoca un piccolo vortice mobile che danneggia e spinge le creature vicine e può sollevare una nube di detriti.',
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
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 3,
                                'source_offset' => -2,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Creatura che termina il turno entro 1,5 metri dal vortice: TS Forza, metà del danno e nessuna spinta se riesce.',
                'forced_movements' => [
                    [
                        'key' => 'movement_1',
                        'movement_type' => 'push',
                        'origin_type' => 'area_center',
                        'direction_type' => 'away_from_origin',
                        'distance' => 3,
                        'up_to_distance' => false,
                        'straight_line' => true,
                        'stops_at_obstacle' => true,
                        'opportunity_attack_rule' => 'does_not_provoke',
                        'sort_order' => 1,
                        'condition' => 'Solo TS Forza fallito a fine turno; allontanamento dal vortice, non dall’incantatore.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'shadow_blade',
        'name' => 'Lama d’Ombra',
        'school_key' => 'illusion',
        'page' => 161,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Crea nella mano dell’incantatore una spada '
            . 'magica di ombra solida che infligge danni psichici ed '
            . 'è più efficace nella luce fioca o nell’oscurità.',
        'higher_levels' => 'Il danno diventa 3d8 con slot di 3° o 4°, '
            . '4d8 con slot di 5° o 6° e 5d8 dal 7°.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'La lama può essere lanciata e fatta ricomparire '
                . 'nella mano con un’azione bonus.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Lama d’Ombra',
                'application_type' => 'automatic',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Crea nella mano dell’incantatore una spada magica di ombra solida che infligge danni psichici ed è più efficace nella luce fioca o nell’oscurità.',
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
                        'key' => 'damage_psychic',
                        'damage_type' => 'Psichico',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'modifier_source_type' => 'source_ability_modifier',
                        'notes' => 'È un’arma con proprietà accurata: al danno si aggiunge Forza o Destrezza del suo utilizzatore.',
                        'scalings' => [
                            [
                                'key' => 'spell_slot_level_3_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'set',
                                'minimum_source' => 3,
                                'maximum_source' => 4,
                                'multiplier' => 0,
                                'flat_value' => 3,
                                'sort_order' => 1,
                            ],
                            [
                                'key' => 'spell_slot_level_5_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'set',
                                'minimum_source' => 5,
                                'maximum_source' => 6,
                                'multiplier' => 0,
                                'flat_value' => 4,
                                'sort_order' => 2,
                            ],
                            [
                                'key' => 'spell_slot_level_7_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'set',
                                'minimum_source' => 7,
                                'maximum_source' => null,
                                'multiplier' => 0,
                                'flat_value' => 5,
                                'sort_order' => 3,
                            ],
                        ],
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                        'condition' => 'Solo attacchi con la lama contro un bersaglio in luce fioca o oscurità.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'pyrotechnics',
        'name' => 'Pirotecnica',
        'school_key' => 'transmutation',
        'page' => 165,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Estingue una fiamma non magica visibile per '
            . 'generare fuochi d’artificio accecanti oppure una '
            . 'nube di fumo oscurante.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'special',
            'requires_sight' => true,
            'notes' => 'Bersaglia una fiamma in un cubo di 1,524 metri; '
                . 'crea un lampo entro 3,048 metri oppure fumo in una '
                . 'sfera del raggio di 6,096 metri.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Pirotecnica',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Estingue una fiamma non magica visibile per generare fuochi d’artificio accecanti oppure una nube di fumo oscurante.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'snillocs_snowball_swarm',
        'name' => 'Sciame di Palle di Neve di Snilloc',
        'school_key' => 'evocation',
        'page' => 166,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un pezzo di ghiaccio o una '
            . 'scheggia di pietra bianca.',
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Fa esplodere una raffica di palle di neve '
            . 'magiche in un punto entro gittata, infliggendo danni '
            . 'da freddo alle creature vicine.',
        'higher_levels' => 'Il danno aumenta di 1d6 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 1.524,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Sciame di Palle di Neve di Snilloc',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Fa esplodere una raffica di palle di neve magiche in un punto entro gittata, infliggendo danni da freddo alle creature vicine.',
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
                        'dice_count' => 3,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 3,
                                'source_offset' => -2,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'skywrite',
        'name' => 'Scritta Celeste',
        'school_key' => 'transmutation',
        'page' => 167,
        'range_type' => 'sight',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'ritual' => true,
        'description' => 'Forma nel cielo fino a dieci parole composte '
            . 'da nuvole, visibili finché l’incantesimo permane o un '
            . 'vento forte le disperde.',
        'target' => [
            'target_type' => 'special',
            'requires_sight' => true,
            'notes' => 'Bersaglia una parte visibile del cielo.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Scritta Celeste',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Forma nel cielo fino a dieci parole composte da nuvole, visibili finché l’incantesimo permane o un vento forte le disperde.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 1,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'dragons_breath',
        'name' => 'Soffio del Drago',
        'school_key' => 'transmutation',
        'page' => 169,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un peperoncino.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Conferisce a una creatura consenziente la '
            . 'capacità di esalare ripetutamente un cono di energia '
            . 'elementale scelto dall’incantatore.',
        'higher_levels' => 'Il danno aumenta di 1d6 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'La creatura toccata può produrre un cono di '
                . '4,572 metri usando la propria azione.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Soffio del Drago',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Conferisce a una creatura consenziente la capacità di esalare ripetutamente un cono di energia elementale scelto dall’incantatore.',
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
                        'key' => 'damage_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 3,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio. Il beneficiario usa un’azione per esalare; TS Destrezza riuscito: metà. Tipo: Acido.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 3,
                                'source_offset' => -2,
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
                        'dice_count' => 3,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio. Il beneficiario usa un’azione per esalare; TS Destrezza riuscito: metà. Tipo: Freddo.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 3,
                                'source_offset' => -2,
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
                        'dice_count' => 3,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio. Il beneficiario usa un’azione per esalare; TS Destrezza riuscito: metà. Tipo: Fuoco.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 3,
                                'source_offset' => -2,
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
                        'dice_count' => 3,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio. Il beneficiario usa un’azione per esalare; TS Destrezza riuscito: metà. Tipo: Fulmine.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 3,
                                'source_offset' => -2,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'damage_poison',
                        'damage_type' => 'Veleno',
                        'is_primary' => false,
                        'sort_order' => 5,
                        'dice_count' => 3,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio. Il beneficiario usa un’azione per esalare; TS Destrezza riuscito: metà. Tipo: Veleno.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 3,
                                'source_offset' => -2,
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
        'key' => 'healing_spirit',
        'name' => 'Spirito Guaritore',
        'school_key' => 'conjuration',
        'page' => 169,
        'casting_time_type' => 'bonus_action',
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Evoca uno spirito naturale mobile che cura '
            . 'le creature quando entrano nel suo spazio o vi '
            . 'iniziano il turno.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Lo spirito non può curare costrutti o non morti '
                . 'e può essere mosso di 9,144 metri.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Spirito Guaritore',
                'application_type' => 'automatic',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Evoca uno spirito naturale mobile che cura le creature quando entrano nel suo spazio o vi iniziano il turno.',
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
                'healings' => [
                    [
                        'key' => 'healing',
                        'healing_type' => 'hit_points',
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
                                'minimum_source' => 3,
                                'source_offset' => -2,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'condition' => 'Prima entrata nello spazio dello spirito in un turno o inizio del turno al suo interno, se l’incantatore sceglie di curare. Esclusi costrutti e non morti.',
                    ],
                ],
                'notes' => 'Errata ufficiale 2020: massimo di cure pari a 1 + modificatore della caratteristica da incantatore, minimo 2; poi lo spirito scompare.',
                'scalings' => [
                    [
                        'key' => 'maximum_healing_uses',
                        'target_field' => 'healing_uses',
                        'source_type' => 'other',
                        'operation' => 'set',
                        'flat_value' => 1,
                        'minimum_result' => 2,
                        'notes' => 'Input: modificatore della caratteristica da incantatore. Errata 2020: 1 + modificatore, minimo 2 cure complessive.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'maximilians_earthen_grasp',
        'name' => 'Stretta della Terra di Maximilian',
        'school_key' => 'transmutation',
        'page' => 169,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una mano in miniatura modellata '
            . 'in argilla.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Fa emergere dal terreno una mano di terra '
            . 'compatta che afferra, trattiene e può stritolare una '
            . 'creatura vicina.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'square',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'La mano occupa uno spazio sul terreno e '
                . 'bersaglia una creatura entro 1,524 metri da essa; '
                . 'i successivi tiri contro lo stritolamento dimezzano '
                . 'il danno quando superati.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Stretta della Terra di Maximilian',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Fa emergere dal terreno una mano di terra compatta che afferra, trattiene e può stritolare una creatura vicina.',
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
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Alla presa iniziale: TS Forza, nessun danno se riesce. Poi, usando un’azione per stritolare la creatura già trattenuta: nuovo TS Forza, metà danno se riesce.',
            ],
        ],
    ]),

    $spell([
        'key' => 'aganazzars_scorcher',
        'name' => 'Vampa di Aganazzar',
        'school_key' => 'evocation',
        'page' => 172,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una scaglia di drago rosso.',
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Proietta dall’incantatore una linea di '
            . 'fiamme rombanti che infligge danni da fuoco alle '
            . 'creature attraversate.',
        'higher_levels' => 'Il danno aumenta di 1d8 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'line',
            'area_size_meters' => 9.144,
            'area_secondary_size_meters' => 1.524,
            'notes' => 'La linea ha origine dall’incantatore.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Vampa di Aganazzar',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Proietta dall’incantatore una linea di fiamme rombanti che infligge danni da fuoco alle creature attraversate.',
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
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
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
                                'minimum_source' => 3,
                                'source_offset' => -2,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'warding_wind',
        'name' => 'Vento di Interdizione',
        'school_key' => 'evocation',
        'page' => 172,
        'range_type' => 'self',
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Genera un forte vento attorno '
            . 'all’incantatore che assorda, estingue piccole fiamme, '
            . 'ostacola il movimento e gli attacchi a distanza.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 3.048,
            'can_target_self' => true,
            'notes' => 'L’emanazione resta centrata sull’incantatore '
                . 'e si muove insieme a lui.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Vento di Interdizione',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Genera un forte vento attorno all’incantatore che assorda, estingue piccole fiamme, ostacola il movimento e gli attacchi a distanza.',
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
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 1,
                        'condition' => 'Solo attacchi con arma a distanza che entrano o escono dal vento.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'earthbind',
        'name' => 'Vincolo della Terra',
        'school_key' => 'transmutation',
        'page' => 172,
        'range' => 91.44,
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Avvolge una creatura visibile con energia '
            . 'magica e, se fallisce il tiro salvezza, annulla la sua '
            . 'velocità di volare facendola scendere gradualmente.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Vincolo della Terra',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Avvolge una creatura visibile con energia magica e, se fallisce il tiro salvezza, annulla la sua velocità di volare facendola scendere gradualmente.',
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
                'forced_movements' => [
                    [
                        'key' => 'movement_1',
                        'movement_type' => 'move',
                        'origin_type' => 'source',
                        'direction_type' => 'special',
                        'distance' => 18,
                        'up_to_distance' => false,
                        'straight_line' => true,
                        'stops_at_obstacle' => true,
                        'opportunity_attack_rule' => 'does_not_provoke',
                        'sort_order' => 1,
                        'condition' => 'Solo creatura volante che ha fallito il TS iniziale: discesa sicura di 18 metri per round finché arriva a terra.',
                        'notes' => 'Non è una caduta; annulla la velocità di volare per la durata.',
                    ],
                ],
            ],
        ],
    ]),
];
