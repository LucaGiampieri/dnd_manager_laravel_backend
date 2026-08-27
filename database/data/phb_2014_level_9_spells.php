<?php

//Valori comuni applicati agli incantesimi di 9° livello
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
];

//Costruisce un incantesimo applicando i valori predefiniti del bersaglio
$spell = static function (array $data) use ($defaults): array {
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

//Restituisce tutti i 16 incantesimi di 9° livello del PHB 2014
return [
    $spell([
        'key' => 'wish',
        'name' => 'Desiderio',
        'school_key' => 'conjuration',
        'page' => 229,
        'range_type' => 'self',
        'verbal_component' => true,
        'description' => 'Altera la realtà, normalmente replicando senza requisiti un incantesimo di 8° livello o inferiore oppure producendo un effetto straordinario.',
        'target' => [
            'target_type' => 'self',
            'can_target_self' => true,
            'notes' => 'Il bersaglio e gli effetti concreti dipendono dal desiderio formulato dall’incantatore.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Desiderio',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Altera la realtà, normalmente replicando senza requisiti un incantesimo di 8° livello o inferiore oppure producendo un effetto straordinario.',
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
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'later_spell_level',
                                'target_field' => 'dice_count',
                                'source_type' => 'other',
                                'operation' => 'set',
                                'notes' => 'Input: livello dell’incantesimo successivamente lanciato, non livello 9 di Desiderio. Un trucchetto di livello 0 non infligge danno.',
                            ],
                        ],
                    ],
                ],
                'condition' => 'Solo dopo lo stress causato da un uso diverso dalla duplicazione: ogni incantesimo successivo, fino al riposo lungo, infligge all’incantatore 1d10 necrotico per livello di quell’incantesimo; non riducibile.',
            ],
        ],
    ]),

    $spell([
        'key' => 'weird',
        'name' => 'Fatale',
        'school_key' => 'illusion',
        'page' => 235,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Materializza nella mente delle creature i loro incubi peggiori, spaventandole e infliggendo danni psichici finché resistono all’effetto.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 9.144,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Fatale',
                'application_type' => 'on_end_turn',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Materializza nella mente delle creature i loro incubi peggiori, spaventandole e infliggendo danni psichici finché resistono all’effetto.',
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
                        'dice_count' => 4,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Solo sul bersaglio spaventato che fallisce il TS Saggezza di fine turno; il successo termina l’effetto.',
            ],
        ],
    ]),

    $spell([
        'key' => 'time_stop',
        'name' => 'Fermare il Tempo',
        'school_key' => 'transmutation',
        'page' => 235,
        'range_type' => 'self',
        'verbal_component' => true,
        'description' => 'Arresta temporaneamente il tempo per tutti tranne l’incantatore, che può svolgere 1d4 + 1 turni consecutivi.',
        'target' => [
            'target_type' => 'self',
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Fermare il Tempo',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Arresta temporaneamente il tempo per tutti tranne l’incantatore, che può svolgere 1d4 + 1 turni consecutivi.',
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
                        'roll_type' => 'other',
                        'modifier_type' => 'special',
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 4,
                        'notes' => 'Il risultato +1 è il numero di turni consecutivi dell’incantatore.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'mass_heal',
        'name' => 'Guarigione di Massa',
        'school_key' => 'evocation',
        'page' => 240,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'description' => 'Distribuisce fino a 700 punti ferita tra le creature visibili e cura malattie, accecamento e sordità.',
        'target' => [
            'target_type' => 'creature',
            'requires_sight' => true,
            'notes' => 'Può influenzare un qualsiasi numero di creature entro gittata, ma non costrutti o non morti.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Guarigione di Massa',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Distribuisce fino a 700 punti ferita tra le creature visibili e cura malattie, accecamento e sordità.',
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
                        'flat_bonus' => 700.0,
                        'notes' => 'Riserva complessiva di 700 PF da distribuire tra le creature scelte; non 700 PF per ciascuna.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'imprisonment',
        'name' => 'Imprigionare',
        'school_key' => 'abjuration',
        'page' => 243,
        'casting_time_type' => 'minute',
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un ritratto o una statuetta del bersaglio e una componente speciale del valore di almeno 500 mo per ogni suo Dado Vita.',
        'material_cost' => 500,
        'duration_type' => 'until_dispelled',
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Vincola magicamente una creatura con una delle forme di prigionia scelte dall’incantatore finché l’effetto non viene dissolto.',
        'materials' => [
            [
                'key' => 'target_likeness',
                'name' => 'Raffigurazione del bersaglio',
                'description' => 'Un ritratto su pergamena o una statuetta che riproduce le fattezze del bersaglio.',
                'sort_order' => 1,
            ],
            [
                'key' => 'imprisonment_reagent',
                'name' => 'Componente della prigionia',
                'description' => 'Una componente speciale determinata dalla forma di prigionia scelta.',
                'cost_amount' => 500,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => false,
                'focus_replaceable' => false,
                'sort_order' => 2,
                'notes' => 'Il costo minimo è di 500 mo per ogni Dado Vita del bersaglio.',
            ],
        ],
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Imprigionare',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Vincola magicamente una creatura con una delle forme di prigionia scelte dall’incantatore finché l’effetto non viene dissolto.',
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
        'key' => 'true_polymorph',
        'name' => 'Metamorfosi Pura',
        'school_key' => 'transmutation',
        'page' => 251,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia di mercurio, una sfera di resina e uno sbuffo di fumo.',
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Trasforma una creatura o un oggetto non magico; mantenere la concentrazione per l’intera durata rende l’effetto permanente finché non viene dissolto.',
        'target' => [
            'target_type' => 'special',
            'target_count' => 1,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'Può trasformare una creatura in un’altra creatura o in un oggetto, oppure un oggetto non magico in una creatura.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Metamorfosi Pura',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Trasforma una creatura o un oggetto non magico; mantenere la concentrazione per l’intera durata rende l’effetto permanente finché non viene dissolto.',
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
        'key' => 'prismatic_wall',
        'name' => 'Muro Prismatico',
        'school_key' => 'abjuration',
        'page' => 254,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Crea sette strati di luce colorata, ciascuno dotato di un effetto difensivo e di un metodo specifico per essere distrutto.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'special',
            'requires_sight' => true,
            'notes' => 'Può formare un muro lungo fino a 27,432 metri e alto 9,144 metri oppure una sfera con diametro massimo di 9,144 metri.',
        ],
        'notes' => 'I tiri salvezza e i danni variano in base allo strato attraversato; l’incantesimo può anche accecare le creature vicine.',

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Muro Prismatico',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea sette strati di luce colorata, ciascuno dotato di un effetto difensivo e di un metodo specifico per essere distrutto.',
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
                        'dice_count' => 10,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Quando attraversa lo strato 1 (Fuoco). TS Destrezza distinto: metà se riesce. Gli strati attraversati si applicano separatamente.',
                    ],
                    [
                        'key' => 'damage_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 10,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Quando attraversa lo strato 2 (Acido). TS Destrezza distinto: metà se riesce. Gli strati attraversati si applicano separatamente.',
                    ],
                    [
                        'key' => 'damage_lightning',
                        'damage_type' => 'Fulmine',
                        'is_primary' => false,
                        'sort_order' => 3,
                        'dice_count' => 10,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Quando attraversa lo strato 3 (Fulmine). TS Destrezza distinto: metà se riesce. Gli strati attraversati si applicano separatamente.',
                    ],
                    [
                        'key' => 'damage_poison',
                        'damage_type' => 'Veleno',
                        'is_primary' => false,
                        'sort_order' => 4,
                        'dice_count' => 10,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Quando attraversa lo strato 4 (Veleno). TS Destrezza distinto: metà se riesce. Gli strati attraversati si applicano separatamente.',
                    ],
                    [
                        'key' => 'damage_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => false,
                        'sort_order' => 5,
                        'dice_count' => 10,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Quando attraversa lo strato 5 (Freddo). TS Destrezza distinto: metà se riesce. Gli strati attraversati si applicano separatamente.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'power_word_heal',
        'name' => 'Parola del Potere Guarire',
        'school_key' => 'evocation',
        'page' => 258,
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'description' => 'Ripristina tutti i punti ferita di una creatura e termina diverse condizioni debilitanti; può anche farla rialzare usando la sua reazione.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'notes' => 'Non ha effetto sui costrutti o sui non morti.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Parola del Potere Guarire',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Ripristina tutti i punti ferita di una creatura e termina diverse condizioni debilitanti; può anche farla rialzare usando la sua reazione.',
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
                        'notes' => 'Ripristina tutti i punti ferita: valore uguale ai PF mancanti del bersaglio. Non si effettua un tiro di guarigione.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'power_word_kill',
        'name' => 'Parola del Potere Uccidere',
        'school_key' => 'enchantment',
        'page' => 259,
        'range' => 18.288,
        'verbal_component' => true,
        'description' => 'Uccide istantaneamente una creatura visibile con non più di 100 punti ferita; altrimenti non produce alcun effetto.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Parola del Potere Uccidere',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Uccide istantaneamente una creatura visibile con non più di 100 punti ferita; altrimenti non produce alcun effetto.',
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
        'key' => 'gate',
        'name' => 'Portale',
        'school_key' => 'conjuration',
        'page' => 263,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un diamante del valore di almeno 5.000 mo.',
        'material_cost' => 5000,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Apre un passaggio verso un punto preciso di un altro piano e può richiamare una creatura pronunciandone il nome.',
        'materials' => [
            [
                'key' => 'diamond',
                'name' => 'Diamante',
                'description' => 'Un diamante del valore di almeno 5.000 mo.',
                'cost_amount' => 5000,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => false,
                'focus_replaceable' => false,
                'sort_order' => 1,
            ],
        ],
        'target' => [
            'target_type' => 'point',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Il portale circolare può avere un diametro compreso tra 1,524 e 6,096 metri.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Portale',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Apre un passaggio verso un punto preciso di un altro piano e può richiamare una creatura pronunciandone il nome.',
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
        'key' => 'foresight',
        'name' => 'Previsione',
        'school_key' => 'divination',
        'page' => 264,
        'casting_time_type' => 'minute',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una piuma di colibrì.',
        'duration_type' => 'hour',
        'duration_value' => 8,
        'description' => 'Conferisce a una creatura consenziente una limitata capacità di prevedere il futuro, migliorandone attacchi, prove, difese e tiri salvezza.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Previsione',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Conferisce a una creatura consenziente una limitata capacità di prevedere il futuro, migliorandone attacchi, prove, difese e tiri salvezza.',
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
                        'condition' => 'Attacchi effettuati dal beneficiario.',
                    ],
                    [
                        'roll_type' => 'ability_check',
                        'modifier_type' => 'advantage',
                        'sort_order' => 2,
                        'condition' => 'Prove effettuate dal beneficiario.',
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'advantage',
                        'sort_order' => 3,
                        'condition' => 'Tiri salvezza effettuati dal beneficiario.',
                    ],
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 4,
                        'condition' => 'Attacchi effettuati da altre creature contro il beneficiario.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'astral_projection',
        'name' => 'Proiezione Astrale',
        'school_key' => 'necromancy',
        'page' => 264,
        'casting_time_type' => 'hour',
        'range' => 3.048,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Per ogni creatura, un giacinto da almeno 1.000 mo e un lingotto d’argento decorato da almeno 100 mo, entrambi consumati.',
        'material_consumed' => true,
        'material_cost' => 1100,
        'duration_type' => 'special',
        'description' => 'Proietta sul Piano Astrale i corpi astrali dell’incantatore e di un massimo di otto creature consenzienti.',
        'materials' => [
            [
                'key' => 'jacinth',
                'name' => 'Giacinto',
                'description' => 'Un giacinto del valore di almeno 1.000 mo per ogni creatura influenzata.',
                'cost_amount' => 1000,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => true,
                'focus_replaceable' => false,
                'sort_order' => 1,
                'notes' => 'La quantità e il costo si applicano a ogni creatura influenzata.',
            ],
            [
                'key' => 'ornate_silver_bar',
                'name' => 'Lingotto d’argento decorato',
                'description' => 'Un lingotto d’argento finemente decorato del valore di almeno 100 mo per ogni creatura influenzata.',
                'cost_amount' => 100,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => true,
                'focus_replaceable' => false,
                'sort_order' => 2,
                'notes' => 'La quantità e il costo si applicano a ogni creatura influenzata.',
            ],
        ],
        'target' => [
            'target_type' => 'creature',
            'target_count' => 9,
            'can_target_self' => true,
            'notes' => 'Influenza l’incantatore e fino a otto creature consenzienti entro gittata.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Proiezione Astrale',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Proietta sul Piano Astrale i corpi astrali dell’incantatore e di un massimo di otto creature consenzienti.',
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
        'key' => 'true_resurrection',
        'name' => 'Resurrezione Pura',
        'school_key' => 'necromancy',
        'page' => 270,
        'casting_time_type' => 'hour',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Uno spruzzo d’acqua santa e diamanti del valore di almeno 25.000 mo, consumati dall’incantesimo.',
        'material_consumed' => true,
        'material_cost' => 25000,
        'description' => 'Riporta in vita con tutti i punti ferita una creatura morta da non più di due secoli, ricreandone anche il corpo se necessario.',
        'materials' => [
            [
                'key' => 'holy_water',
                'name' => 'Acqua santa',
                'description' => 'Uno spruzzo d’acqua santa.',
                'sort_order' => 1,
            ],
            [
                'key' => 'diamonds',
                'name' => 'Diamanti',
                'description' => 'Diamanti del valore complessivo di almeno 25.000 mo.',
                'cost_amount' => 25000,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => true,
                'focus_replaceable' => false,
                'sort_order' => 2,
            ],
        ],
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'notes' => 'La creatura deve essere morta da non più di 200 anni per una causa diversa dalla vecchiaia e la sua anima deve essere libera e consenziente.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Resurrezione Pura',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Riporta in vita con tutti i punti ferita una creatura morta da non più di due secoli, ricreandone anche il corpo se necessario.',
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
                        'notes' => 'Ripristina tutti i punti ferita: valore uguale ai PF mancanti del bersaglio. Non si effettua un tiro di guarigione.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'meteor_swarm',
        'name' => 'Sciame di Meteore',
        'school_key' => 'evocation',
        'page' => 273,
        'range' => 1609.344,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Fa precipitare meteore in quattro punti visibili, infliggendo ingenti danni da fuoco e contundenti nelle aree d’impatto.',
        'target' => [
            'target_type' => 'area',
            'target_count' => 4,
            'area_shape' => 'sphere',
            'area_size_meters' => 12.192,
            'requires_sight' => true,
            'notes' => 'Ogni creatura subisce l’effetto una sola volta anche se si trova in più sfere.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Sciame di Meteore',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Fa precipitare meteore in quattro punti visibili, infliggendo ingenti danni da fuoco e contundenti nelle aree d’impatto.',
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
                        'dice_count' => 20,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                    ],
                    [
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 20,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Si somma ai danni da fuoco. TS Destrezza riuscito: metà. Una creatura in più aree subisce il danno una sola volta.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'storm_of_vengeance',
        'name' => 'Tempesta di Vendetta',
        'school_key' => 'conjuration',
        'page' => 284,
        'range_type' => 'sight',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Genera una vasta nube tempestosa che produce tuoni, pioggia acida, fulmini, grandine e venti sempre più violenti.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'circle',
            'area_size_meters' => 109.728,
            'requires_sight' => true,
            'notes' => 'La nube influenza le creature fino a 1.524 metri sotto di essa e produce un effetto differente in ogni round.',
        ],
        'notes' => 'I tiri salvezza e i tipi di danno cambiano in base al round dell’incantesimo.',

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Tempesta di Vendetta',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Genera una vasta nube tempestosa che produce tuoni, pioggia acida, fulmini, grandine e venti sempre più violenti.',
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
                        'key' => 'damage_thunder',
                        'damage_type' => 'Tuono',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Primo round: solo se fallisce il TS Costituzione iniziale, senza danno se riesce.',
                    ],
                    [
                        'key' => 'damage_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Secondo round: creature e oggetti sotto la nube, senza tiro salvezza.',
                    ],
                    [
                        'key' => 'damage_lightning',
                        'damage_type' => 'Fulmine',
                        'is_primary' => false,
                        'sort_order' => 3,
                        'dice_count' => 10,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Terzo round: fino a sei creature o oggetti diversi scelti; TS Destrezza, metà se riesce.',
                    ],
                    [
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => false,
                        'sort_order' => 4,
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Quarto round: grandine sulle creature sotto la nube, senza tiro salvezza.',
                    ],
                    [
                        'key' => 'damage_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => false,
                        'sort_order' => 5,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Dal quinto al decimo round: creature sotto la nube, senza tiro salvezza.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'shapechange',
        'name' => 'Trasformazione',
        'school_key' => 'transmutation',
        'page' => 286,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un diadema di giada del valore di almeno 1.500 mo, indossato durante il lancio.',
        'material_cost' => 1500,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Trasforma l’incantatore in altre creature viste in precedenza, permettendogli di cambiare forma più volte durante la durata.',
        'materials' => [
            [
                'key' => 'jade_circlet',
                'name' => 'Diadema di giada',
                'description' => 'Un diadema di giada che deve essere indossato prima del lancio.',
                'cost_amount' => 1500,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => false,
                'focus_replaceable' => false,
                'sort_order' => 1,
            ],
        ],
        'target' => [
            'target_type' => 'self',
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Trasformazione',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Trasforma l’incantatore in altre creature viste in precedenza, permettendogli di cambiare forma più volte durante la durata.',
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
];
