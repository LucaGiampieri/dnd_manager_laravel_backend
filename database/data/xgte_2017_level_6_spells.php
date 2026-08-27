<?php

//Valori condivisi dagli incantesimi di 6° livello di Xanathar
$defaults = [
    'level' => 6,
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

//Restituisce i 12 incantesimi di 6° livello di Xanathar
return [
    $spell([
        'key' => 'druid_grove',
        'name' => 'Boschetto Druidico',
        'school_key' => 'abjuration',
        'page' => 151,
        'casting_time_value' => 10,
        'casting_time_type' => 'minute',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Vischio raccolto con un falcetto '
            . 'd’oro alla luce della luna piena.',
        'material_consumed' => true,
        'duration_type' => 'hour',
        'duration_value' => 24,
        'description' => 'Protegge una zona naturale con nebbia, '
            . 'sottobosco, alberi guardiani e altri effetti magici '
            . 'scelti dall’incantatore.',
        'notes' => 'Lanciato nella stessa area ogni giorno per un '
            . 'anno, dura finché non viene dissolto.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 27.432,
            'notes' => 'Il cubo può avere uno spigolo compreso tra '
                . '9,144 e 27,432 metri; edifici e altre strutture '
                . 'sono esclusi.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Boschetto Druidico',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Protegge una zona naturale con nebbia, sottobosco, alberi guardiani e altri effetti magici scelti dall’incantatore.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 24,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'guardian_tree_slam',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 3,
                        'die_size' => 6,
                        'flat_bonus' => 4,
                        'condition' => 'Singolo attacco Schianto di un Albero Guardiano, solo se si sceglie questa opzione. Usa le statistiche dell’Albero Risvegliato del Manuale dei Mostri; non è danno automatico dell’area.',
                        'notes' => 'Albero Risvegliato: tiro per colpire +6, portata 3 metri. I guardiani non sono creature evocate con le formule di Tasha.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'create_homunculus',
        'name' => 'Creare Omuncolo',
        'school_key' => 'transmutation',
        'page' => 153,
        'casting_time_type' => 'hour',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Argilla, cenere e radice di '
            . 'mandragora, che vengono consumate, e un pugnale '
            . 'ingioiellato del valore di almeno 1.000 mo.',
        'material_consumed' => true,
        'material_cost' => 1000,
        'description' => 'Trasforma i materiali e il sangue '
            . 'dell’incantatore in un omuncolo fedele, collegando '
            . 'temporaneamente i loro punti ferita massimi.',
        'materials' => [
            [
                'key' => 'consumed_mixture',
                'name' => 'Miscela per l’omuncolo',
                'description' => 'Argilla, cenere e radice '
                    . 'di mandragora.',
                'consumed' => true,
                'focus_replaceable' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'jeweled_dagger',
                'name' => 'Pugnale ingioiellato',
                'description' => 'Un pugnale ingioiellato del valore '
                    . 'di almeno 1.000 mo.',
                'cost_amount' => 1000,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => false,
                'focus_replaceable' => false,
                'sort_order' => 2,
            ],
        ],
        'target' => [
            'target_type' => 'special',
            'notes' => 'Crea un solo omuncolo; il lancio fallisce se '
                . 'l’incantatore ne possiede già uno vivo.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Creare Omuncolo',
                'application_type' => 'automatic',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Trasforma i materiali e il sangue dell’incantatore in un omuncolo fedele, collegando temporaneamente i loro punti ferita massimi.',
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
                        'dice_count' => 2,
                        'die_size' => 4,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Danno all’incantatore per il taglio rituale; non può essere ridotto in alcun modo.',
            ],
        ],
    ]),

    $spell([
        'key' => 'scatter',
        'name' => 'Disperdere',
        'school_key' => 'conjuration',
        'page' => 155,
        'range' => 9.144,
        'verbal_component' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Teletrasporta fino a cinque creature vicine '
            . 'in spazi liberi visibili situati entro 36,576 metri '
            . 'dall’incantatore.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 5,
            'requires_sight' => true,
            'notes' => 'Una creatura non consenziente può resistere '
                . 'con un tiro salvezza su Saggezza.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Disperdere',
                'application_type' => 'special',
                'target_scope' => 'targets',
                'ends_with_source' => true,
                'description' => 'Teletrasporta fino a cinque creature vicine in spazi liberi visibili situati entro 36,576 metri dall’incantatore.',
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
        'key' => 'soul_cage',
        'name' => 'Gabbia dell’Anima',
        'school_key' => 'necromancy',
        'page' => 158,
        'casting_time_type' => 'reaction',
        'casting_trigger' => 'Quando l’incantatore vede morire un '
            . 'umanoide entro 18,288 metri da sé.',
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una minuscola gabbia d’argento '
            . 'del valore di 100 mo.',
        'material_cost' => 100,
        'duration_type' => 'hour',
        'duration_value' => 8,
        'description' => 'Intrappola l’anima di un umanoide appena '
            . 'morto e consente di sfruttarla fino a sei volte per '
            . 'curarsi, interrogarla o ottenere altri benefici.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Il bersaglio è l’anima di un umanoide che '
                . 'l’incantatore vede morire entro gittata.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Gabbia dell’Anima',
                'application_type' => 'automatic',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Intrappola l’anima di un umanoide appena morto e consente di sfruttarla fino a sei volte per curarsi, interrogarla o ottenere altri benefici.',
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
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                        'condition' => 'Esperienza in Prestito: una sola prova, attacco o salvezza a scelta entro l’inizio del turno successivo; i tre vantaggi non si consumano separatamente.',
                    ],
                    [
                        'roll_type' => 'ability_check',
                        'modifier_type' => 'advantage',
                        'sort_order' => 2,
                        'condition' => 'Esperienza in Prestito: una sola prova, attacco o salvezza a scelta entro l’inizio del turno successivo; i tre vantaggi non si consumano separatamente.',
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'advantage',
                        'sort_order' => 3,
                        'condition' => 'Esperienza in Prestito: una sola prova, attacco o salvezza a scelta entro l’inizio del turno successivo; i tre vantaggi non si consumano separatamente.',
                    ],
                ],
            ],
            [
                'key' => 'spell_effect_2',
                'name' => 'Rubare Vita',
                'application_type' => 'manual',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Intrappola l’anima di un umanoide appena morto e consente di sfruttarla fino a sei volte per curarsi, interrogarla o ottenere altri benefici.',
                'sort_order' => 2,
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
                'healings' => [
                    [
                        'key' => 'healing',
                        'healing_type' => 'hit_points',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Opzione Rubare Vita usando un’azione bonus. Ogni cura consuma uno dei sei usi complessivi dell’anima.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'primordial_ward',
        'name' => 'Interdizione Primordiale',
        'school_key' => 'abjuration',
        'page' => 159,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Conferisce resistenza ai principali danni '
            . 'elementali e permette di trasformare una resistenza '
            . 'in immunità per un breve periodo.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Interdizione Primordiale',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Conferisce resistenza ai principali danni elementali e permette di trasformare una resistenza in immunità per un breve periodo.',
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
        'key' => 'investiture_of_ice',
        'name' => 'Investitura del Ghiaccio',
        'school_key' => 'transmutation',
        'page' => 159,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Ricopre l’incantatore di ghiaccio, '
            . 'proteggendolo dal freddo e dal fuoco e permettendogli '
            . 'di emettere coni di vento gelido.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'L’azione concessa crea un cono di '
                . '4,572 metri che richiede un tiro salvezza '
                . 'su Costituzione.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Investitura del Ghiaccio',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Ricopre l’incantatore di ghiaccio, proteggendolo dal freddo e dal fuoco e permettendogli di emettere coni di vento gelido.',
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
                        'key' => 'damage_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 4,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Con un’azione: cono di vento gelido, TS Costituzione, metà se riesce; il fallimento dimezza la velocità fino all’inizio del turno successivo dell’incantatore.',
            ],
        ],
    ]),

    $spell([
        'key' => 'investiture_of_wind',
        'name' => 'Investitura del Vento',
        'school_key' => 'transmutation',
        'page' => 159,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Circonda l’incantatore di vento, gli '
            . 'conferisce volo, ostacola gli attacchi a distanza e '
            . 'permette di creare raffiche turbinanti.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'L’azione concessa crea un cubo con spigolo '
                . 'di 4,572 metri entro 18,288 metri.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Investitura del Vento',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Circonda l’incantatore di vento, gli conferisce volo, ostacola gli attacchi a distanza e permette di creare raffiche turbinanti.',
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
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Con un’azione: cubo di vento, TS Costituzione, metà se riesce; una creatura Grande o inferiore che fallisce è anche spinta.',
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 1,
                        'condition' => 'Attacchi con arma a distanza contro l’incantatore.',
                    ],
                ],
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
                        'condition' => 'Solo creatura Grande o inferiore che fallisce il TS; allontanamento dal centro del cubo.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'investiture_of_flame',
        'name' => 'Investitura della Fiamma',
        'school_key' => 'transmutation',
        'page' => 159,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Avvolge l’incantatore nelle fiamme, '
            . 'proteggendolo dal fuoco e dal freddo e permettendogli '
            . 'di sprigionare linee di fuoco.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'L’azione concessa crea una linea lunga '
                . '4,572 metri e larga 1,524 metri.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Investitura della Fiamma',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Avvolge l’incantatore nelle fiamme, proteggendolo dal fuoco e dal freddo e permettendogli di sprigionare linee di fuoco.',
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
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 4,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                    [
                        'key' => 'flame_aura',
                        'damage_type' => 'Fuoco',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 1,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'condition' => 'Una creatura entra entro 1,5 metri per la prima volta nel turno oppure vi termina il turno; senza tiro salvezza.',
                    ],
                ],
                'condition' => 'Linea di fuoco usando un’azione: TS Destrezza, metà se riesce. L’aura a contatto è una formula distinta.',
            ],
        ],
    ]),

    $spell([
        'key' => 'investiture_of_stone',
        'name' => 'Investitura della Pietra',
        'school_key' => 'transmutation',
        'page' => 160,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Ricopre l’incantatore di pietra, '
            . 'proteggendolo dagli attacchi non magici e permettendo '
            . 'di attraversare terra e roccia.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'L’azione concessa scuote il terreno entro '
                . '4,572 metri dall’incantatore.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Investitura della Pietra',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Ricopre l’incantatore di pietra, proteggendolo dagli attacchi non magici e permettendo di attraversare terra e roccia.',
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
                        'key' => 'damage_force',
                        'damage_type' => 'Forza',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Solo se l’incantatore termina il proprio turno all’interno di terra o roccia: espulsione al più vicino spazio libero e 1d10 da forza.',
            ],
        ],
    ]),

    $spell([
        'key' => 'bones_of_the_earth',
        'name' => 'Ossa della Terra',
        'school_key' => 'transmutation',
        'page' => 163,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Fa emergere dal terreno fino a sei colonne '
            . 'di pietra che possono sollevare, schiacciare o '
            . 'trattenere le creature.',
        'higher_levels' => 'Crea due colonne aggiuntive per ogni slot '
            . 'di livello superiore al 6°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'special',
            'requires_sight' => true,
            'notes' => 'Crea fino a sei colonne del diametro di '
                . '1,524 metri e alte fino a 9,144 metri, in punti '
                . 'del terreno visibili entro gittata.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Ossa della Terra',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Fa emergere dal terreno fino a sei colonne di pietra che possono sollevare, schiacciare o trattenere le creature.',
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
                        'dice_count' => 6,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Nessun secondo tiro salvezza e nessun dimezzamento dei 6d6 da schiacciamento.',
                    ],
                ],
                'condition' => 'Danno automatico solo se una creatura sulla colonna viene schiacciata contro un soffitto o un ostacolo. Il TS Destrezza precedente evita di essere sollevata, non dimezza il danno da schiacciamento.',
                'scalings' => [
                    [
                        'key' => 'base_pillar_count',
                        'target_field' => 'pillar_count',
                        'source_type' => 'fixed',
                        'fixed_value' => 6,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_pillar_count',
                        'target_field' => 'pillar_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 7,
                        'source_offset' => -6,
                        'multiplier' => 2,
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
        'key' => 'mental_prison',
        'name' => 'Prigione Mentale',
        'school_key' => 'illusion',
        'page' => 165,
        'range' => 18.288,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'INT',
        'save_success_damage' => 'full',
        'description' => 'Imprigiona una creatura in una minaccia '
            . 'illusoria che la isola e la trattiene, infliggendole '
            . 'ulteriori danni se tenta di attraversarla.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Prigione Mentale',
                'application_type' => 'automatic',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Imprigiona una creatura in una minaccia illusoria che la isola e la trattiene, infliggendole ulteriori danni se tenta di attraversarla.',
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
                        'dice_count' => 5,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'notes' => 'I 5d10 iniziali si applicano anche con tiro salvezza riuscito; in quel caso non ci sono ulteriori effetti. Una creatura immune allo charme supera automaticamente il tiro.',
                    ],
                    [
                        'key' => 'break_illusion',
                        'damage_type' => 'Psichico',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 10,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'condition' => 'Solo dopo il fallimento del tiro iniziale: se il bersaglio viene mosso fuori dall’illusione, effettua un attacco in mischia attraverso di essa o vi protende una parte del corpo. Poi l’incantesimo termina.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'tensers_transformation',
        'name' => 'Trasformazione di Tenser',
        'school_key' => 'transmutation',
        'page' => 169,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Alcuni peli di un toro.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Trasforma temporaneamente l’incantatore in '
            . 'un combattente marziale, conferendogli punti ferita '
            . 'temporanei, competenze e attacchi potenziati.',
        'notes' => 'Durante la trasformazione l’incantatore non può '
            . 'lanciare incantesimi e al termine deve superare un '
            . 'tiro salvezza su Costituzione.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Trasformazione di Tenser',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Trasforma temporaneamente l’incantatore in un combattente marziale, conferendogli punti ferita temporanei, competenze e attacchi potenziati.',
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
                        'key' => 'damage_force',
                        'damage_type' => 'Forza',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 12,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Quando un attacco dell’incantatore con un’arma colpisce durante la trasformazione: 2d12 extra. Il vantaggio ai tiri per colpire è limitato alle armi semplici e da guerra.',
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
                        'duration_value' => 10,
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
                        'flat_bonus' => 50.0,
                        'condition' => 'Si perdono i PF temporanei residui concessi dall’incantesimo quando termina.',
                        'temporary_hit_point_rule' => 'replace_if_higher',
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                        'condition' => 'Solo attacchi con armi semplici e da guerra.',
                    ],
                ],
            ],
        ],
    ]),
];
