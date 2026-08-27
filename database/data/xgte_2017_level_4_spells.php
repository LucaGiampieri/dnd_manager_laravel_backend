<?php

//Valori condivisi dagli incantesimi di 4° livello di Xanathar
$defaults = [
    'level' => 4,
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

//Restituisce i 10 incantesimi di 4° livello di Xanathar
return [
    $spell([
        'key' => 'elemental_bane',
        'name' => 'Anatema Elementale',
        'school_key' => 'transmutation',
        'page' => 151,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Priva una creatura della resistenza a un '
            . 'tipo di danno elementale scelto e le infligge danni '
            . 'extra la prima volta che lo subisce in ogni turno.',
        'higher_levels' => 'Bersaglia una creatura aggiuntiva per ogni '
            . 'slot di livello superiore al 4°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Anatema Elementale',
                'application_type' => 'on_damage',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Priva una creatura della resistenza a un tipo di danno elementale scelto e le infligge danni extra la prima volta che lo subisce in ogni turno.',
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
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio, la prima volta in ogni turno che il bersaglio subisce quel tipo di danno dopo aver fallito il TS iniziale. Tipo: Acido.',
                    ],
                    [
                        'key' => 'damage_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio, la prima volta in ogni turno che il bersaglio subisce quel tipo di danno dopo aver fallito il TS iniziale. Tipo: Freddo.',
                    ],
                    [
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => false,
                        'sort_order' => 3,
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio, la prima volta in ogni turno che il bersaglio subisce quel tipo di danno dopo aver fallito il TS iniziale. Tipo: Fuoco.',
                    ],
                    [
                        'key' => 'damage_lightning',
                        'damage_type' => 'Fulmine',
                        'is_primary' => false,
                        'sort_order' => 4,
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio, la prima volta in ogni turno che il bersaglio subisce quel tipo di danno dopo aver fallito il TS iniziale. Tipo: Fulmine.',
                    ],
                    [
                        'key' => 'damage_thunder',
                        'damage_type' => 'Tuono',
                        'is_primary' => false,
                        'sort_order' => 5,
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio, la prima volta in ogni turno che il bersaglio subisce quel tipo di danno dopo aver fallito il TS iniziale. Tipo: Tuono.',
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
                        'minimum_source' => 5,
                        'source_offset' => -4,
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
        'key' => 'charm_monster',
        'name' => 'Charme sui Mostri',
        'school_key' => 'enchantment',
        'page' => 152,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Tenta di affascinare una creatura visibile, '
            . 'che considera l’incantatore amichevole finché non '
            . 'viene danneggiata o l’effetto termina.',
        'higher_levels' => 'Bersaglia una creatura aggiuntiva per ogni '
            . 'slot di livello superiore al 4°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Charme sui Mostri',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Tenta di affascinare una creatura visibile, che considera l’incantatore amichevole finché non viene danneggiata o l’effetto termina.',
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
                        'minimum_source' => 5,
                        'source_offset' => -4,
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
        'key' => 'summon_greater_demon',
        'name' => 'Evoca Demone Maggiore',
        'school_key' => 'conjuration',
        'page' => 157,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una fiala di sangue di un umanoide '
            . 'ucciso nelle ultime 24 ore; il sangue viene consumato '
            . 'soltanto se usato per tracciare il cerchio protettivo.',
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Evoca un demone dell’Abisso e consente '
            . 'all’incantatore di impartirgli ordini finché la '
            . 'creatura non riesce a spezzare il controllo.',
        'higher_levels' => 'Il grado di sfida massimo aumenta di 1 per '
            . 'ogni slot di livello superiore al 4°.',
        'notes' => 'Il componente viene consumato soltanto quando '
            . 'viene usato per creare il cerchio protettivo.',
        'target' => [
            'target_type' => 'special',
            'requires_sight' => true,
            'notes' => 'Il demone compare in uno spazio libero visibile '
                . 'entro gittata e può effettuare tiri salvezza su '
                . 'Carisma per liberarsi dal controllo.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Evoca Demone Maggiore',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Evoca un demone dell’Abisso e consente all’incantatore di impartirgli ordini finché la creatura non riesce a spezzare il controllo.',
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
                'scalings' => [
                    [
                        'key' => 'base_maximum_challenge_rating',
                        'target_field' => 'maximum_challenge_rating',
                        'source_type' => 'fixed',
                        'fixed_value' => 5,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_maximum_challenge_rating',
                        'target_field' => 'maximum_challenge_rating',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 5,
                        'source_offset' => -4,
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
        'key' => 'sickening_radiance',
        'name' => 'Fulgore Nauseante',
        'school_key' => 'evocation',
        'page' => 158,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Riempie una vasta area di luce verdastra che '
            . 'può infliggere danni radiosi, causare indebolimento e '
            . 'impedire di beneficiare dell’invisibilità.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 9.144,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Fulgore Nauseante',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Riempie una vasta area di luce verdastra che può infliggere danni radiosi, causare indebolimento e impedire di beneficiare dell’invisibilità.',
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
                'damages' => [
                    [
                        'key' => 'damage_radiant',
                        'damage_type' => 'Radioso',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 4,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Prima entrata nell’area in un turno oppure inizio del turno: TS Costituzione, nessun danno né livello di sfinimento se riesce.',
            ],
        ],
    ]),

    $spell([
        'key' => 'guardian_of_nature',
        'name' => 'Guardiano della Natura',
        'school_key' => 'transmutation',
        'page' => 159,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Trasforma l’incantatore in una Bestia '
            . 'Primordiale o in un Grande Albero, conferendo benefici '
            . 'differenti a movimento, difese e attacchi.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Guardiano della Natura',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Trasforma l’incantatore in una Bestia Primordiale o in un Grande Albero, conferendo benefici differenti a movimento, difese e attacchi.',
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
                        'die_size' => 6,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Solo forma Bestia Primordiale: 1d6 da forza extra agli attacchi con arma in mischia. Non si applica alla forma Grande Albero.',
            ],
            [
                'key' => 'spell_effect_2',
                'name' => 'Beneficio dell’incantatore',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Beneficio applicato all’incantatore; restano valide le condizioni delle singole formule.',
                'sort_order' => 2,
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
                        'healing_type' => 'temporary_hit_points',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'flat_bonus' => 10.0,
                        'condition' => 'Solo opzione Grande Albero; non opzione Bestia Primordiale.',
                        'temporary_hit_point_rule' => 'replace_if_higher',
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                        'ability' => 'FOR',
                        'condition' => 'Solo Bestia Primordiale, attacchi basati su Forza.',
                    ],
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 2,
                        'ability' => 'DES',
                        'condition' => 'Solo Grande Albero, attacchi basati su questa caratteristica.',
                    ],
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 3,
                        'ability' => 'SAG',
                        'condition' => 'Solo Grande Albero, attacchi basati su questa caratteristica.',
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'advantage',
                        'sort_order' => 4,
                        'ability' => 'COS',
                        'condition' => 'Solo Grande Albero.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'shadow_of_moil',
        'name' => 'Ombra di Moil',
        'school_key' => 'necromancy',
        'page' => 163,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Il globo oculare di un non morto '
            . 'racchiuso in una gemma del valore di almeno 150 mo.',
        'material_cost' => 150,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Avvolge l’incantatore in ombre infuocate che '
            . 'lo oscurano, attenuano la luce, resistono al radioso e '
            . 'colpiscono chi lo attacca da vicino.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'Le ombre influenzano anche la luce entro '
                . '3,048 metri dall’incantatore.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Ombra di Moil',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Avvolge l’incantatore in ombre infuocate che lo oscurano, attenuano la luce, resistono al radioso e colpiscono chi lo attacca da vicino.',
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
                        'key' => 'damage_necrotic',
                        'damage_type' => 'Necrotico',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Quando una creatura entro 3 metri colpisce l’incantatore con un attacco; danno automatico alla creatura attaccante.',
            ],
        ],
    ]),

    $spell([
        'key' => 'watery_sphere',
        'name' => 'Sfera Acquea',
        'school_key' => 'conjuration',
        'page' => 168,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia d’acqua.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Evoca una sfera d’acqua mobile che può '
            . 'inghiottire e trattenere creature, trascinandole con '
            . 'sé quando viene spostata.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Può trattenere fino a quattro creature Medie '
                . 'o più piccole oppure una creatura Grande.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Sfera Acquea',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Evoca una sfera d’acqua mobile che può inghiottire e trattenere creature, trascinandole con sé quando viene spostata.',
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
            ],
        ],
    ]),

    $spell([
        'key' => 'vitriolic_sphere',
        'name' => 'Sfera al Vetriolo',
        'school_key' => 'evocation',
        'page' => 168,
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia di bile di una lumaca '
            . 'gigante.',
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Scaglia una sfera di acido che esplode in '
            . 'un’ampia area e continua a danneggiare nel turno '
            . 'successivo chi fallisce il tiro salvezza.',
        'higher_levels' => 'Il danno iniziale aumenta di 2d4 per ogni '
            . 'slot di livello superiore al 4°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 6.096,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Sfera al Vetriolo',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Scaglia una sfera di acido che esplode in un’ampia area e continua a danneggiare nel turno successivo chi fallisce il tiro salvezza.',
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
                        'dice_count' => 10,
                        'die_size' => 4,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 5,
                                'source_offset' => -4,
                                'multiplier' => 2,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                    [
                        'key' => 'lingering_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 5,
                        'die_size' => 4,
                        'flat_bonus' => 0,
                        'condition' => 'Solo per chi ha fallito il tiro iniziale, alla fine del suo turno successivo. Non cresce con lo slot.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'storm_sphere',
        'name' => 'Sfera della Tempesta',
        'school_key' => 'evocation',
        'page' => 168,
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'attack_type' => 'ranged',
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Crea una sfera di aria turbinante che '
            . 'danneggia e ostacola le creature e dalla quale '
            . 'l’incantatore può scagliare fulmini.',
        'higher_levels' => 'I danni del vento e del fulmine aumentano '
            . 'di 1d6 per ogni slot di livello superiore al 4°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 6.096,
            'notes' => 'Il fulmine può bersagliare una creatura entro '
                . '18,288 metri dal centro della sfera.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Sfera della Tempesta',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea una sfera di aria turbinante che danneggia e ostacola le creature e dalla quale l’incantatore può scagliare fulmini.',
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
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 5,
                                'source_offset' => -4,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                    [
                        'key' => 'lightning_bolt',
                        'damage_type' => 'Fulmine',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 4,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Con un’azione bonus, attacco con incantesimo a distanza contro una creatura entro 18 metri dal centro; vantaggio se il bersaglio è nella sfera.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 5,
                                'source_offset' => -4,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                ],
                'condition' => 'Vento: alla comparsa o alla fine del turno nella sfera, TS Forza, nessun danno se riesce. Fulmine: azione bonus e tiro per colpire separati.',
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                        'condition' => 'Solo il fulmine dell’incantesimo contro un bersaglio dentro la sfera.',
                    ],
                    [
                        'roll_type' => 'skill_check',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 2,
                        'ability' => 'SAG',
                        'condition' => 'Creature entro 9 metri dalla sfera: solo Saggezza (Percezione) per ascoltare.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'find_greater_steed',
        'name' => 'Trova Cavalcatura Superiore',
        'school_key' => 'conjuration',
        'page' => 171,
        'casting_time_value' => 10,
        'casting_time_type' => 'minute',
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'description' => 'Evoca uno spirito fedele che assume la '
            . 'forma di una potente cavalcatura e rimane legato '
            . 'all’incantatore anche dopo essere scomparso.',
        'target' => [
            'target_type' => 'special',
            'notes' => 'La cavalcatura appare in uno spazio libero '
                . 'entro gittata e può assumere una delle forme '
                . 'previste dall’incantesimo.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Trova Cavalcatura Superiore',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Evoca uno spirito fedele che assume la forma di una potente cavalcatura e rimane legato all’incantatore anche dopo essere scomparso.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'healings' => [
                    [
                        'key' => 'healing',
                        'healing_type' => 'hit_points',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'modifier_source_type' => 'other',
                        'condition' => 'Quando un nuovo lancio richiama la stessa cavalcatura precedentemente scomparsa.',
                        'notes' => 'Riporta la cavalcatura al massimo dei suoi PF; non è una formula con dadi.',
                    ],
                ],
            ],
        ],
    ]),
];
