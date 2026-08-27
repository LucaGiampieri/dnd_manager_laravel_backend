<?php

//Valori condivisi dagli incantesimi di 4° livello di Tasha
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

//Regole di controllo condivise dagli spiriti evocati
$summonControlRules = 'Condivide l’iniziativa con l’incantatore '
    . 'e agisce subito dopo di lui. Obbedisce ai comandi verbali '
    . 'senza richiedere azioni; senza comandi usa Schivata e si '
    . 'allontana dal pericolo.';

//Genera le progressioni comuni di una forma evocata
$summonScalings = function (
    int $baseLevel,
    int $hitPointMultiplier,
    string $actionKey,
    string $attackKey,
    int $damageSortOrder = 1,
    int $baseSortOrder = 1
): array {
    return [
        [
            'key' => 'armor_class_from_slot',
            'target_type' => 'armor_class',
            'source_type' => 'slot_level',
            'operation' => 'add',
            'sort_order' => $baseSortOrder,
        ],
        [
            'key' => 'hit_points_above_base_slot',
            'target_type' => 'hit_points',
            'source_type' => 'slot_level',
            'operation' => 'add',
            'minimum_source' => $baseLevel + 1,
            'source_offset' => -$baseLevel,
            'multiplier' => $hitPointMultiplier,
            'sort_order' => $baseSortOrder + 1,
        ],
        [
            'key' => 'multiattack_from_slot',
            'target_type' => 'attack_count',
            'target_ref' => 'multiattack',
            'source_type' => 'slot_level',
            'operation' => 'set',
            'divisor' => 2,
            'rounding' => 'floor',
            'minimum_result' => 1,
            'sort_order' => $baseSortOrder + 2,
        ],
        [
            'key' => $actionKey . '_attack_from_caster',
            'target_type' => 'attack_bonus',
            'target_ref' => $actionKey . ':' . $attackKey,
            'source_type' => 'caster_spell_attack_bonus',
            'operation' => 'set',
            'sort_order' => $baseSortOrder + 3,
        ],
        [
            'key' => $actionKey . '_damage_from_slot',
            'target_type' => 'damage_bonus',
            'target_ref' => $actionKey . ':' . $damageSortOrder,
            'source_type' => 'slot_level',
            'operation' => 'add',
            'sort_order' => $baseSortOrder + 4,
        ],
    ];
};

//Crea una azione Multiattacco collegata al livello dello slot
$multiattack = function (string $attackName): array {
    return [
        'key' => 'multiattack',
        'name' => 'Multiattacco',
        'description' => 'Effettua un numero di attacchi '
            . $attackName . ' pari alla metà del livello dello slot, '
            . 'arrotondata per difetto.',
        'sort_order' => 1,
    ];
};

//Crea una normale azione di attacco dello spirito evocato
$attackAction = function (
    string $key,
    string $name,
    string $attackType,
    string $attackKind,
    string $damageType,
    int $diceCount,
    int $dieSize,
    int $bonus,
    ?float $reach,
    ?float $range,
    ?float $longRange = null,
    string $description = ''
): array {
    return [
        'key' => $key,
        'name' => $name,
        'description' => $description,
        'sort_order' => 2,
        'attacks' => [
            [
                'key' => $key . '_attack',
                'name' => $name,
                'attack_type' => $attackType,
                'attack_kind' => $attackKind,
                'reach' => $reach,
                'range' => $range,
                'long_range' => $longRange,
                'target_count' => 1,
                'notes' => 'Usa il modificatore di attacco '
                    . 'dell’incantesimo del personaggio.',
            ],
        ],
        'damages' => [
            [
                'attack_key' => $key . '_attack',
                'damage_type' => $damageType,
                'dice_count' => $diceCount,
                'die_size' => $dieSize,
                'bonus' => $bonus,
                'is_primary' => true,
                'sort_order' => 1,
            ],
        ],
    ];
};

//Crea una delle tre forme dello Spirito Aberrante
$aberrationForm = function (
    string $name,
    array $movements,
    array $attack,
    string $traits,
    array $extraActions = [],
    bool $isDefault = false
) use ($multiattack, $summonScalings): array {
    return [
        'name' => $name,
        'description' => "Forma {$name} dello Spirito Aberrante.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito Aberrante ({$name})",
            'abilities' => [
                'FOR' => 16,
                'DES' => 10,
                'COS' => 15,
                'INT' => 16,
                'SAG' => 10,
                'CAR' => 6,
            ],
            'armor_class' => [
                'value' => 11,
                'description' => '11 + il livello dello slot.',
            ],
            'hit_points' => [
                'average_hit_points' => 40,
                'special_calculation' => '40 + 10 per ogni livello '
                    . 'dello slot sopra il 4°.',
            ],
            'movements' => $movements,
            'actions' => [
                $multiattack($attack['name']),
                $attack,
                ...$extraActions,
            ],
            'description' => 'Aberrazione evocata che comprende '
                . 'i linguaggi parlati dall’incantatore.',
            'notes' => 'Immunità ai danni psichici; scurovisione '
                . '18,288 metri; Percezione passiva 10; conosce il '
                . 'Gergo delle Profondità. ' . $traits,
        ],
        'scalings' => $summonScalings(
            4,
            10,
            $attack['key'],
            $attack['key'] . '_attack'
        ),
    ];
};

//Crea una delle tre forme dello Spirito del Costrutto
$constructForm = function (
    string $name,
    string $traits,
    array $extraActions = [],
    bool $isDefault = false
) use ($multiattack, $attackAction, $summonScalings): array {
    $slam = $attackAction(
        'slam',
        'Schianto',
        'melee',
        'weapon',
        'Contundente',
        1,
        8,
        4,
        1.524,
        null,
        null,
        'Attacco con Arma da Mischia.'
    );

    return [
        'name' => $name,
        'description' => "Forma di {$name} dello Spirito del Costrutto.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito del Costrutto ({$name})",
            'abilities' => [
                'FOR' => 18,
                'DES' => 10,
                'COS' => 18,
                'INT' => 14,
                'SAG' => 11,
                'CAR' => 5,
            ],
            'armor_class' => [
                'value' => 13,
                'description' => '13 + il livello dello slot.',
            ],
            'hit_points' => [
                'average_hit_points' => 40,
                'special_calculation' => '40 + 15 per ogni livello '
                    . 'dello slot sopra il 4°.',
            ],
            'movements' => [
                [
                    'type' => 'Terrestre',
                    'speed' => 9.144,
                ],
            ],
            'actions' => [
                $multiattack('Schianto'),
                $slam,
                ...$extraActions,
            ],
            'description' => 'Costrutto evocato che comprende i '
                . 'linguaggi parlati dall’incantatore.',
            'notes' => 'Resistenza ai danni da veleno; immunità ad '
                . 'affascinato, avvelenato, incapacitato, '
                . 'indebolimento, paralizzato, pietrificato e '
                . 'spaventato; scurovisione 18,288 metri; '
                . 'Percezione passiva 10. ' . $traits,
        ],
        'scalings' => $summonScalings(
            4,
            15,
            'slam',
            'slam_attack'
        ),
    ];
};

//Crea una delle quattro forme dello Spirito Elementale
$elementalForm = function (
    string $name,
    array $movements,
    string $damageType,
    string $traits,
    bool $isDefault = false
) use ($multiattack, $attackAction, $summonScalings): array {
    $slam = $attackAction(
        'slam',
        'Schianto',
        'melee',
        'weapon',
        $damageType,
        1,
        10,
        4,
        1.524,
        null,
        null,
        'Attacco con Arma da Mischia.'
    );

    return [
        'name' => $name,
        'description' => "Forma {$name} dello Spirito Elementale.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito Elementale ({$name})",
            'abilities' => [
                'FOR' => 18,
                'DES' => 15,
                'COS' => 17,
                'INT' => 4,
                'SAG' => 10,
                'CAR' => 16,
            ],
            'armor_class' => [
                'value' => 11,
                'description' => '11 + il livello dello slot.',
            ],
            'hit_points' => [
                'average_hit_points' => 50,
                'special_calculation' => '50 + 10 per ogni livello '
                    . 'dello slot sopra il 4°.',
            ],
            'movements' => $movements,
            'actions' => [
                $multiattack('Schianto'),
                $slam,
            ],
            'description' => 'Elementale evocato che comprende i '
                . 'linguaggi parlati dall’incantatore.',
            'notes' => 'Immunità ai danni da veleno e alle condizioni '
                . 'avvelenato, indebolimento, paralizzato, pietrificato '
                . 'e privo di sensi; scurovisione 18,288 metri; '
                . 'Percezione passiva 10; parla Primordiale. ' . $traits,
        ],
        'scalings' => $summonScalings(
            4,
            10,
            'slam',
            'slam_attack'
        ),
    ];
};

//Restituisce i tre incantesimi di 4° livello introdotti da Tasha
return [
    //Evoca Aberrazione
    $spell([
        'key' => 'summon_aberration',
        'name' => 'Evoca Aberrazione',
        'school_key' => 'conjuration',
        'page' => 106,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un tentacolo in salamoia e un '
            . 'bulbo oculare in una fiala intarsiata di platino del '
            . 'valore di almeno 400 mo.',
        'material_cost' => 400,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Evoca uno spirito aberrante scegliendo '
            . 'pseudo-onnivedente, slaad o progenie stellare.',
        'higher_levels' => 'Lo stat block usa il livello dello slot '
            . 'impiegato e cresce secondo le sue formule.',
        'target' => [
            'target_type' => 'point',
            'target_count' => 1,
            'requires_sight' => true,
        ],
        'summons' => [
            [
                'name' => 'Spirito Aberrante',
                'selection_condition' => 'Scegliere '
                    . 'Pseudo-onnivedente, Slaad o Progenie Stellare.',
                'control_rules' => $summonControlRules,
                'templates' => [
                    [
                        'name' => 'Spirito Aberrante',
                        'creature_type_key' => 'aberration',
                        'size_name' => 'Media',
                        'forms' => [
                            $aberrationForm(
                                'Pseudo-onnivedente',
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                    [
                                        'type' => 'Volare',
                                        'speed' => 9.144,
                                        'can_hover' => true,
                                    ],
                                ],
                                $attackAction(
                                    'eye_ray',
                                    'Raggio Oculare',
                                    'ranged',
                                    'spell',
                                    'Psichico',
                                    1,
                                    8,
                                    3,
                                    null,
                                    45.72
                                ),
                                'Possiede una velocità di volo e '
                                    . 'può fluttuare.',
                                [],
                                true
                            ),
                            $aberrationForm(
                                'Slaad',
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                ],
                                $attackAction(
                                    'claws',
                                    'Artigli',
                                    'melee',
                                    'weapon',
                                    'Tagliente',
                                    1,
                                    10,
                                    3,
                                    1.524,
                                    null,
                                    null,
                                    'La creatura colpita non può '
                                        . 'recuperare PF fino '
                                        . 'all’inizio del turno '
                                        . 'successivo dello spirito.'
                                ),
                                'Rigenerazione: se possiede almeno '
                                    . '1 PF recupera 5 PF all’inizio '
                                    . 'del proprio turno.'
                            ),
                            $aberrationForm(
                                'Progenie Stellare',
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                ],
                                $attackAction(
                                    'psychic_slam',
                                    'Colpo Psichico',
                                    'melee',
                                    'spell',
                                    'Psichico',
                                    1,
                                    8,
                                    3,
                                    1.524,
                                    null
                                ),
                                'Aura Sussurrante: le altre creature '
                                    . 'che iniziano il turno entro '
                                    . '1,524 metri effettuano un TS su '
                                    . 'Saggezza contro la CD del '
                                    . 'personaggio o subiscono 2d6 '
                                    . 'danni psichici.',
                                [
                                    [
                                        'key' => 'whispering_aura',
                                        'name' => 'Aura Sussurrante',
                                        'action_type' => 'special',
                                        'description' => 'All’inizio '
                                            . 'del turno dello spirito, '
                                            . 'le altre creature entro '
                                            . '1,524 metri effettuano '
                                            . 'un TS su Saggezza.',
                                        'sort_order' => 3,
                                        'damages' => [
                                            [
                                                'damage_type' =>
                                                    'Psichico',
                                                'dice_count' => 2,
                                                'die_size' => 6,
                                                'is_primary' => true,
                                                'sort_order' => 1,
                                            ],
                                        ],
                                        'saving_throws' => [
                                            [
                                                'key' => 'aura_save',
                                                'ability' => 'SAG',
                                                'success_type' =>
                                                    'no_effect',
                                                'failure_description' =>
                                                    'Subisce 2d6 danni '
                                                    . 'psichici.',
                                                'notes' => 'La CD è '
                                                    . 'quella degli '
                                                    . 'incantesimi del '
                                                    . 'personaggio.',
                                            ],
                                        ],
                                    ],
                                ]
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),

    //Evoca Costrutto
    $spell([
        'key' => 'summon_construct',
        'name' => 'Evoca Costrutto',
        'school_key' => 'conjuration',
        'page' => 109,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una pietra dipinta e uno scrigno '
            . 'di metallo del valore di almeno 400 mo.',
        'material_cost' => 400,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Evoca lo spirito di un costrutto scegliendo '
            . 'un corpo di argilla, metallo o pietra.',
        'higher_levels' => 'Lo stat block usa il livello dello slot '
            . 'impiegato e cresce secondo le sue formule.',
        'target' => [
            'target_type' => 'point',
            'target_count' => 1,
            'requires_sight' => true,
        ],
        'summons' => [
            [
                'name' => 'Spirito del Costrutto',
                'selection_condition' => 'Scegliere Argilla, '
                    . 'Metallo o Pietra.',
                'control_rules' => $summonControlRules,
                'templates' => [
                    [
                        'name' => 'Spirito del Costrutto',
                        'creature_type_key' => 'construct',
                        'size_name' => 'Media',
                        'forms' => [
                            $constructForm(
                                'Argilla',
                                'Slancio Rabbioso: come reazione '
                                    . 'quando subisce danni attacca '
                                    . 'una creatura casuale vicina; '
                                    . 'se non può, si muove verso un '
                                    . 'nemico senza provocare attacchi '
                                    . 'di opportunità.',
                                [
                                    [
                                        'key' => 'berserk_lashing',
                                        'name' => 'Slancio Rabbioso',
                                        'action_type' => 'reaction',
                                        'trigger' => 'Quando il '
                                            . 'costrutto subisce danni.',
                                        'description' => 'Effettua '
                                            . 'Schianto contro una '
                                            . 'creatura casuale entro '
                                            . '1,524 metri; altrimenti '
                                            . 'si muove verso un nemico.',
                                        'sort_order' => 3,
                                    ],
                                ],
                                true
                            ),
                            $constructForm(
                                'Metallo',
                                'Corpo Riscaldato: chi lo tocca o lo '
                                    . 'colpisce in mischia da entro '
                                    . '1,524 metri subisce 1d10 danni '
                                    . 'da fuoco.',
                                [
                                    [
                                        'key' => 'heated_body',
                                        'name' => 'Corpo Riscaldato',
                                        'action_type' => 'special',
                                        'trigger' => 'Quando una '
                                            . 'creatura lo tocca o lo '
                                            . 'colpisce in mischia da '
                                            . 'entro 1,524 metri.',
                                        'damages' => [
                                            [
                                                'damage_type' => 'Fuoco',
                                                'dice_count' => 1,
                                                'die_size' => 10,
                                                'is_primary' => true,
                                                'sort_order' => 1,
                                            ],
                                        ],
                                        'sort_order' => 3,
                                    ],
                                ]
                            ),
                            $constructForm(
                                'Pietra',
                                'Flemma Rocciosa: una creatura che '
                                    . 'inizia il turno entro 3,048 '
                                    . 'metri effettua un TS su Saggezza '
                                    . 'o perde le reazioni e dimezza la '
                                    . 'velocità fino al turno successivo.',
                                [
                                    [
                                        'key' => 'stony_lethargy',
                                        'name' => 'Flemma Rocciosa',
                                        'action_type' => 'special',
                                        'description' => 'Una creatura '
                                            . 'visibile che inizia il '
                                            . 'turno entro 3,048 metri '
                                            . 'effettua un TS su Saggezza.',
                                        'saving_throws' => [
                                            [
                                                'key' => 'lethargy_save',
                                                'ability' => 'SAG',
                                                'success_type' =>
                                                    'no_effect',
                                                'failure_description' =>
                                                    'Non può usare '
                                                    . 'reazioni e la '
                                                    . 'velocità è '
                                                    . 'dimezzata.',
                                                'notes' => 'La CD è '
                                                    . 'quella degli '
                                                    . 'incantesimi.',
                                            ],
                                        ],
                                        'sort_order' => 3,
                                    ],
                                ]
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),

    //Evoca Elementale
    $spell([
        'key' => 'summon_elemental',
        'name' => 'Evoca Elementale',
        'school_key' => 'conjuration',
        'page' => 109,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Aria, un sassolino, cenere e acqua '
            . 'all’interno di una fiala intarsiata d’oro del valore '
            . 'di almeno 400 mo.',
        'material_cost' => 400,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Evoca uno spirito elementale scegliendo '
            . 'aria, terra, fuoco o acqua.',
        'higher_levels' => 'Lo stat block usa il livello dello slot '
            . 'impiegato e cresce secondo le sue formule.',
        'target' => [
            'target_type' => 'point',
            'target_count' => 1,
            'requires_sight' => true,
        ],
        'summons' => [
            [
                'name' => 'Spirito Elementale',
                'selection_condition' => 'Scegliere Aria, Terra, '
                    . 'Fuoco o Acqua.',
                'control_rules' => $summonControlRules,
                'templates' => [
                    [
                        'name' => 'Spirito Elementale',
                        'creature_type_key' => 'elemental',
                        'size_name' => 'Media',
                        'forms' => [
                            $elementalForm(
                                'Aria',
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 12.192,
                                    ],
                                    [
                                        'type' => 'Volare',
                                        'speed' => 12.192,
                                        'can_hover' => true,
                                    ],
                                ],
                                'Contundente',
                                'Resistenza ai danni da fulmine e '
                                    . 'tuono; Forma Amorfa.',
                                true
                            ),
                            $elementalForm(
                                'Terra',
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 12.192,
                                    ],
                                    [
                                        'type' => 'Scavare',
                                        'speed' => 12.192,
                                    ],
                                ],
                                'Contundente',
                                'Resistenza ai danni perforanti '
                                    . 'e taglienti.'
                            ),
                            $elementalForm(
                                'Fuoco',
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 12.192,
                                    ],
                                ],
                                'Fuoco',
                                'Immunità ai danni da fuoco; '
                                    . 'Forma Amorfa.'
                            ),
                            $elementalForm(
                                'Acqua',
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 12.192,
                                    ],
                                    [
                                        'type' => 'Nuotare',
                                        'speed' => 12.192,
                                    ],
                                ],
                                'Contundente',
                                'Resistenza ai danni da acido; '
                                    . 'Forma Amorfa.'
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),
];
