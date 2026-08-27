<?php

//Valori condivisi dagli incantesimi di 3° livello di Tasha
$defaults = [
    'level' => 3,
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

//Genera le progressioni comuni degli stat block evocati
$summonScalings = function (
    int $baseLevel,
    int $hitPointMultiplier,
    array $attacks
): array {
    $scalings = [
        [
            'key' => 'armor_class_from_slot',
            'target_type' => 'armor_class',
            'source_type' => 'slot_level',
            'operation' => 'add',
            'condition' => 'Si aggiunge il livello dello slot.',
            'sort_order' => 1,
        ],
        [
            'key' => 'hit_points_above_base_slot',
            'target_type' => 'hit_points',
            'source_type' => 'slot_level',
            'operation' => 'add',
            'minimum_source' => $baseLevel + 1,
            'source_offset' => -$baseLevel,
            'multiplier' => $hitPointMultiplier,
            'condition' => 'Aumenta i PF per ogni livello dello '
                . 'slot sopra quello base.',
            'sort_order' => 2,
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
            'sort_order' => 3,
        ],
    ];

    foreach ($attacks as $index => $attack) {
        $order = 4 + ($index * 2);

        $scalings[] = [
            'key' => $attack['key'] . '_attack_from_caster',
            'target_type' => 'attack_bonus',
            'target_ref' => $attack['attack_ref'],
            'source_type' => 'caster_spell_attack_bonus',
            'operation' => 'set',
            'sort_order' => $order,
        ];

        $scalings[] = [
            'key' => $attack['key'] . '_damage_from_slot',
            'target_type' => 'damage_bonus',
            'target_ref' => $attack['damage_ref'],
            'source_type' => 'slot_level',
            'operation' => 'add',
            'sort_order' => $order + 1,
        ];
    }

    return $scalings;
};

//Azioni comuni dello Spirito d'Ombra
$shadowActions = [
    [
        'key' => 'multiattack',
        'name' => 'Multiattacco',
        'description' => 'Effettua un numero di attacchi Squarcio '
            . 'Gelido pari alla metà del livello dello slot, '
            . 'arrotondata per difetto.',
        'sort_order' => 1,
    ],
    [
        'key' => 'chilling_rend',
        'name' => 'Squarcio Gelido',
        'description' => 'Attacco con Arma da Mischia che usa il '
            . 'modificatore di attacco dell’incantesimo.',
        'sort_order' => 2,
        'attacks' => [
            [
                'key' => 'chilling_rend_attack',
                'name' => 'Squarcio Gelido',
                'attack_type' => 'melee',
                'attack_kind' => 'weapon',
                'reach' => 1.524,
                'target_count' => 1,
            ],
        ],
        'damages' => [
            [
                'attack_key' => 'chilling_rend_attack',
                'damage_type' => 'Freddo',
                'dice_count' => 1,
                'die_size' => 12,
                'bonus' => 3,
                'is_primary' => true,
                'sort_order' => 1,
            ],
        ],
    ],
    [
        'key' => 'dreadful_scream',
        'name' => 'Grido Agghiacciante',
        'description' => 'Ogni creatura entro 9,144 metri deve '
            . 'superare un TS su Saggezza o essere spaventata per '
            . '1 minuto; può ripetere il TS alla fine dei suoi turni.',
        'max_uses' => 1,
        'recharge_type' => 'per_day',
        'sort_order' => 3,
        'saving_throws' => [
            [
                'key' => 'frightened_save',
                'ability' => 'SAG',
                'success_type' => 'no_effect',
                'failure_description' => 'Il bersaglio è spaventato '
                    . 'dallo spirito per 1 minuto.',
                'notes' => 'La CD è la CD degli incantesimi '
                    . 'dell’incantatore.',
            ],
        ],
    ],
];

//Crea una forma dello Spirito d'Ombra
$shadowForm = function (
    string $name,
    string $trait,
    bool $isDefault = false
) use ($shadowActions, $summonScalings): array {
    return [
        'name' => $name,
        'description' => "Stato d’animo {$name}.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito d’Ombra ({$name})",
            'abilities' => [
                'FOR' => 13,
                'DES' => 16,
                'COS' => 15,
                'INT' => 4,
                'SAG' => 10,
                'CAR' => 16,
            ],
            'armor_class' => [
                'value' => 11,
                'description' => '11 + il livello dello slot.',
            ],
            'hit_points' => [
                'average_hit_points' => 35,
                'special_calculation' => '35 + 15 per ogni livello '
                    . 'dello slot sopra il 3°.',
            ],
            'movements' => [
                [
                    'type' => 'Terrestre',
                    'speed' => 12.192,
                ],
            ],
            'actions' => $shadowActions,
            'description' => 'Mostruosità d’ombra evocata, alleata '
                . 'dell’incantatore.',
            'notes' => 'Resistenza ai danni necrotici; immunità '
                . 'alla condizione spaventato; scurovisione 36,576 '
                . 'metri; Percezione passiva 10. ' . $trait,
        ],
        'scalings' => $summonScalings(3, 15, [
            [
                'key' => 'chilling_rend',
                'attack_ref' =>
                    'chilling_rend:chilling_rend_attack',
                'damage_ref' => 'chilling_rend:1',
            ],
        ]),
    ];
};

//Azioni comuni dello Spirito Fatato
$feyActions = [
    [
        'key' => 'multiattack',
        'name' => 'Multiattacco',
        'description' => 'Effettua un numero di attacchi Spada Corta '
            . 'pari alla metà del livello dello slot, arrotondata '
            . 'per difetto.',
        'sort_order' => 1,
    ],
    [
        'key' => 'shortsword',
        'name' => 'Spada Corta',
        'description' => 'Attacco con Arma da Mischia che infligge '
            . 'danni perforanti e danni da forza.',
        'sort_order' => 2,
        'attacks' => [
            [
                'key' => 'shortsword_attack',
                'name' => 'Spada Corta',
                'attack_type' => 'melee',
                'attack_kind' => 'weapon',
                'reach' => 1.524,
                'target_count' => 1,
            ],
        ],
        'damages' => [
            [
                'attack_key' => 'shortsword_attack',
                'damage_type' => 'Perforante',
                'dice_count' => 1,
                'die_size' => 6,
                'bonus' => 3,
                'is_primary' => true,
                'sort_order' => 1,
            ],
            [
                'attack_key' => 'shortsword_attack',
                'damage_type' => 'Forza',
                'dice_count' => 1,
                'die_size' => 6,
                'sort_order' => 2,
            ],
        ],
    ],
];

//Crea una forma dello Spirito Fatato con il relativo Passo Fatato
$feyForm = function (
    string $name,
    string $mistyStepEffect,
    bool $isDefault = false
) use ($feyActions, $summonScalings): array {
    $actions = $feyActions;
    $actions[] = [
        'key' => 'fey_step',
        'name' => 'Passo Fatato',
        'action_type' => 'bonus_action',
        'description' => 'Si teletrasporta fino a 9,144 metri in '
            . 'uno spazio libero visibile. ' . $mistyStepEffect,
        'sort_order' => 3,
    ];

    return [
        'name' => $name,
        'description' => "Umore {$name} dello Spirito Fatato.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito Fatato ({$name})",
            'abilities' => [
                'FOR' => 13,
                'DES' => 16,
                'COS' => 14,
                'INT' => 14,
                'SAG' => 11,
                'CAR' => 16,
            ],
            'armor_class' => [
                'value' => 12,
                'description' => '12 + il livello dello slot.',
            ],
            'hit_points' => [
                'average_hit_points' => 30,
                'special_calculation' => '30 + 10 per ogni livello '
                    . 'dello slot sopra il 3°.',
            ],
            'movements' => [
                [
                    'type' => 'Terrestre',
                    'speed' => 12.192,
                ],
            ],
            'actions' => $actions,
            'description' => 'Folletto evocato che comprende i '
                . 'linguaggi dell’incantatore.',
            'notes' => 'Immunità alla condizione affascinato; '
                . 'scurovisione 18,288 metri; Percezione passiva 10.',
        ],
        'scalings' => $summonScalings(3, 10, [
            [
                'key' => 'shortsword',
                'attack_ref' => 'shortsword:shortsword_attack',
                'damage_ref' => 'shortsword:1',
            ],
        ]),
    ];
};

//Crea una azione di attacco per una forma dello Spirito Non Morto
$undeadAttack = function (
    string $key,
    string $name,
    string $attackType,
    string $attackKind,
    string $damageType,
    int $diceCount,
    int $dieSize,
    ?float $reach,
    ?float $range,
    string $extraDescription
): array {
    return [
        'key' => $key,
        'name' => $name,
        'description' => $extraDescription,
        'sort_order' => 2,
        'attacks' => [
            [
                'key' => $key . '_attack',
                'name' => $name,
                'attack_type' => $attackType,
                'attack_kind' => $attackKind,
                'reach' => $reach,
                'range' => $range,
                'target_count' => 1,
            ],
        ],
        'damages' => [
            [
                'attack_key' => $key . '_attack',
                'damage_type' => $damageType,
                'dice_count' => $diceCount,
                'die_size' => $dieSize,
                'bonus' => 3,
                'is_primary' => true,
                'sort_order' => 1,
            ],
        ],
    ];
};

//Crea una forma dello Spirito Non Morto
$undeadForm = function (
    string $name,
    int $baseHitPoints,
    array $movements,
    array $attack,
    string $traits,
    array $extraActions = [],
    bool $isDefault = false
) use ($summonScalings): array {
    $actions = [
        [
            'key' => 'multiattack',
            'name' => 'Multiattacco',
            'description' => 'Effettua un numero di attacchi pari '
                . 'alla metà del livello dello slot, arrotondata '
                . 'per difetto.',
            'sort_order' => 1,
        ],
        $attack,
        ...$extraActions,
    ];

    return [
        'name' => $name,
        'description' => "Forma {$name} dello Spirito Non Morto.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito Non Morto ({$name})",
            'abilities' => [
                'FOR' => 12,
                'DES' => 16,
                'COS' => 15,
                'INT' => 4,
                'SAG' => 10,
                'CAR' => 9,
            ],
            'armor_class' => [
                'value' => 11,
                'description' => '11 + il livello dello slot.',
            ],
            'hit_points' => [
                'average_hit_points' => $baseHitPoints,
                'special_calculation' => $baseHitPoints
                    . ' + 10 per ogni livello dello slot sopra il 3°.',
            ],
            'movements' => $movements,
            'actions' => $actions,
            'description' => 'Spirito non morto evocato e alleato '
                . 'dell’incantatore.',
            'notes' => 'Immunità ai danni necrotici e da veleno; '
                . 'immunità ad avvelenato, indebolimento, paralizzato '
                . 'e spaventato; scurovisione 18,288 metri; '
                . 'Percezione passiva 10. ' . $traits,
        ],
        'scalings' => $summonScalings(3, 10, [
            [
                'key' => $attack['key'],
                'attack_ref' => $attack['key'] . ':'
                    . $attack['key'] . '_attack',
                'damage_ref' => $attack['key'] . ':1',
            ],
        ]),
    ];
};

//Restituisce i cinque incantesimi di 3° livello introdotti da Tasha
return [
    //Evoca Bestia d'Ombra
    $spell([
        'key' => 'summon_shadowspawn',
        'name' => 'Evoca Bestia d’Ombra',
        'school_key' => 'conjuration',
        'page' => 107,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Delle lacrime all’interno di una '
            . 'gemma del valore di almeno 300 mo.',
        'material_cost' => 300,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Invoca uno spirito d’ombra scegliendo '
            . 'uno stato d’animo tra furia, disperazione o paura.',
        'higher_levels' => 'Lo stat block usa il livello dello slot '
            . 'impiegato e cresce secondo le sue formule.',
        'target' => [
            'target_type' => 'point',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Lo spirito compare in uno spazio libero '
                . 'visibile entro 27,432 metri.',
        ],
        'summons' => [
            [
                'name' => 'Spirito d’Ombra',
                'selection_condition' => 'Scegliere Furia, '
                    . 'Disperazione o Paura.',
                'control_rules' => $summonControlRules,
                'templates' => [
                    [
                        'name' => 'Spirito d’Ombra',
                        'creature_type_key' => 'monstrosity',
                        'size_name' => 'Media',
                        'forms' => [
                            $shadowForm(
                                'Furia',
                                'Frenesia Spaventosa: vantaggio '
                                    . 'agli attacchi contro creature '
                                    . 'spaventate.',
                                true
                            ),
                            $shadowForm(
                                'Disperazione',
                                'Peso del Dolore: le altre creature '
                                    . 'che iniziano il turno entro '
                                    . '1,524 metri riducono la velocità '
                                    . 'di 6,096 metri per quel turno.'
                            ),
                            $shadowForm(
                                'Paura',
                                'Furtività d’Ombra: come azione bonus '
                                    . 'può Nascondersi in luce fioca '
                                    . 'o oscurità.'
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),

    //Evoca Folletto
    $spell([
        'key' => 'summon_fey',
        'name' => 'Evoca Folletto',
        'school_key' => 'conjuration',
        'page' => 110,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un fiore dorato del valore di '
            . 'almeno 300 mo.',
        'material_cost' => 300,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Invoca uno spirito fatato scegliendo '
            . 'un umore tra rabbioso, gioioso o malandrino.',
        'higher_levels' => 'Lo stat block usa il livello dello slot '
            . 'impiegato e cresce secondo le sue formule.',
        'target' => [
            'target_type' => 'point',
            'target_count' => 1,
            'requires_sight' => true,
        ],
        'summons' => [
            [
                'name' => 'Spirito Fatato',
                'selection_condition' => 'Scegliere Rabbioso, '
                    . 'Gioioso o Malandrino.',
                'control_rules' => $summonControlRules,
                'templates' => [
                    [
                        'name' => 'Spirito Fatato',
                        'creature_type_key' => 'fey',
                        'size_name' => 'Piccola',
                        'forms' => [
                            $feyForm(
                                'Rabbioso',
                                'Ottiene vantaggio al prossimo '
                                    . 'attacco effettuato entro la '
                                    . 'fine del turno.',
                                true
                            ),
                            $feyForm(
                                'Gioioso',
                                'Può costringere una creatura entro '
                                    . '3,048 metri a un TS su Saggezza; '
                                    . 'se fallisce resta affascinata '
                                    . 'per 1 minuto o finché subisce danni.'
                            ),
                            $feyForm(
                                'Malandrino',
                                'Crea oscurità magica in un cubo con '
                                    . 'spigolo di 1,524 metri adiacente, '
                                    . 'fino alla fine del turno successivo.'
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),

    //Evoca Non Morto
    $spell([
        'key' => 'summon_undead',
        'name' => 'Evoca Non Morto',
        'school_key' => 'necromancy',
        'page' => 111,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un teschio dorato del valore di '
            . 'almeno 300 mo.',
        'material_cost' => 300,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Invoca uno spirito non morto scegliendo '
            . 'la forma spettrale, putrida o scheletrica.',
        'higher_levels' => 'Lo stat block usa il livello dello slot '
            . 'impiegato e cresce secondo le sue formule.',
        'target' => [
            'target_type' => 'point',
            'target_count' => 1,
            'requires_sight' => true,
        ],
        'summons' => [
            [
                'name' => 'Spirito Non Morto',
                'selection_condition' => 'Scegliere Spettrale, '
                    . 'Putrido o Scheletrico.',
                'control_rules' => $summonControlRules,
                'templates' => [
                    [
                        'name' => 'Spirito Non Morto',
                        'creature_type_key' => 'undead',
                        'size_name' => 'Media',
                        'forms' => [
                            $undeadForm(
                                'Spettrale',
                                30,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                    [
                                        'type' => 'Volare',
                                        'speed' => 12.192,
                                        'can_hover' => true,
                                    ],
                                ],
                                $undeadAttack(
                                    'deathly_touch',
                                    'Tocco Mortale',
                                    'melee',
                                    'weapon',
                                    'Necrotico',
                                    1,
                                    8,
                                    1.524,
                                    null,
                                    'Dopo il colpo il bersaglio deve '
                                        . 'superare un TS su Saggezza '
                                        . 'o essere spaventato fino '
                                        . 'alla fine del suo turno '
                                        . 'successivo.'
                                ),
                                'Movimento Incorporeo: attraversa '
                                    . 'creature e oggetti come terreno '
                                    . 'difficile, subendo danni se '
                                    . 'termina il turno in un oggetto.',
                                [],
                                true
                            ),
                            $undeadForm(
                                'Putrido',
                                30,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                ],
                                $undeadAttack(
                                    'rotting_claw',
                                    'Artiglio Putrefatto',
                                    'melee',
                                    'weapon',
                                    'Tagliente',
                                    1,
                                    6,
                                    1.524,
                                    null,
                                    'Se il bersaglio è avvelenato, '
                                        . 'deve superare un TS su '
                                        . 'Costituzione o essere '
                                        . 'paralizzato fino alla fine '
                                        . 'del suo turno successivo.'
                                ),
                                'Aura Marcescente: le altre creature '
                                    . 'che iniziano il turno entro '
                                    . '1,524 metri effettuano un TS su '
                                    . 'Costituzione o sono avvelenate '
                                    . 'fino al turno successivo.'
                            ),
                            $undeadForm(
                                'Scheletrico',
                                20,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                ],
                                $undeadAttack(
                                    'grave_bolt',
                                    'Dardo Tombale',
                                    'ranged',
                                    'spell',
                                    'Necrotico',
                                    2,
                                    4,
                                    null,
                                    45.72,
                                    'Attacco con Incantesimo a Distanza.'
                                ),
                                'Forma scheletrica specializzata '
                                    . 'negli attacchi a distanza.'
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),

    //Fortezza della Mente
    $spell([
        'key' => 'intellect_fortress',
        'name' => 'Fortezza della Mente',
        'school_key' => 'abjuration',
        'page' => 112,
        'range' => 9.144,
        'verbal_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Una creatura consenziente ottiene '
            . 'resistenza ai danni psichici e vantaggio ai tiri '
            . 'salvezza su Intelligenza, Saggezza e Carisma.',
        'higher_levels' => 'Bersaglia una creatura aggiuntiva per '
            . 'ogni livello dello slot superiore al 3°; i bersagli '
            . 'devono trovarsi entro 9,144 metri l’uno dall’altro.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 1,
            'can_target_self' => true,
            'requires_sight' => true,
            'notes' => 'Il bersaglio deve essere consenziente.',
        ],
        'effects' => [
            [
                'key' => 'mental_fortress',
                'name' => 'Fortezza mentale',
                'application_type' => 'automatic',
                'target_scope' => 'targets',
                'description' => 'Conferisce resistenza ai danni '
                    . 'psichici e vantaggio ai TS su INT, SAG e CAR.',
                'roll_modifiers' => [
                    [
                        'roll_type' => 'saving_throw',
                        'ability' => 'INT',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'ability' => 'SAG',
                        'modifier_type' => 'advantage',
                        'sort_order' => 2,
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'ability' => 'CAR',
                        'modifier_type' => 'advantage',
                        'sort_order' => 3,
                    ],
                ],
                'scalings' => [
                    [
                        'key' => 'extra_target_per_slot',
                        'target_field' => 'target_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 4,
                        'source_offset' => -3,
                    ],
                ],
            ],
        ],
    ]),

    //Sudario Spirituale
    $spell([
        'key' => 'spirit_shroud',
        'name' => 'Sudario Spirituale',
        'school_key' => 'necromancy',
        'page' => 116,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Gli attacchi dell’incantatore infliggono '
            . 'danni extra a creature entro 3,048 metri; una creatura '
            . 'colpita non può recuperare PF fino al turno successivo. '
            . 'Un’altra creatura che inizi entro 9,144 metri perde '
            . '3,048 metri di velocità per quel turno.',
        'higher_levels' => 'I danni aumentano di 1d8 per ogni due '
            . 'livelli dello slot superiori al 3°.',
        'target' => [
            'target_type' => 'self',
            'can_target_self' => true,
        ],
        'effects' => [
            [
                'key' => 'shrouded_attacks',
                'name' => 'Attacchi ammantati',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'condition' => 'Il bersaglio colpito si trova entro '
                    . '3,048 metri dall’incantatore.',
                'description' => 'Al lancio l’incantatore sceglie '
                    . 'freddo, necrotico o radioso per tutti i danni '
                    . 'extra dell’incantesimo.',
                'sort_order' => 1,
                'damages' => [
                    [
                        'key' => 'cold_option',
                        'damage_type' => 'Freddo',
                        'dice_count' => 1,
                        'die_size' => 8,
                        'is_primary' => true,
                        'condition' => 'Opzione Freddo scelta al lancio.',
                        'sort_order' => 1,
                        'scalings' => [
                            [
                                'key' => 'extra_die_every_two_slots',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 5,
                                'source_offset' => -3,
                                'divisor' => 2,
                                'rounding' => 'floor',
                            ],
                        ],
                    ],
                    [
                        'key' => 'necrotic_option',
                        'damage_type' => 'Necrotico',
                        'dice_count' => 1,
                        'die_size' => 8,
                        'condition' => 'Opzione Necrotico scelta '
                            . 'al lancio.',
                        'sort_order' => 2,
                        'scalings' => [
                            [
                                'key' => 'extra_die_every_two_slots',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 5,
                                'source_offset' => -3,
                                'divisor' => 2,
                                'rounding' => 'floor',
                            ],
                        ],
                    ],
                    [
                        'key' => 'radiant_option',
                        'damage_type' => 'Radioso',
                        'dice_count' => 1,
                        'die_size' => 8,
                        'condition' => 'Opzione Radioso scelta '
                            . 'al lancio.',
                        'sort_order' => 3,
                        'scalings' => [
                            [
                                'key' => 'extra_die_every_two_slots',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 5,
                                'source_offset' => -3,
                                'divisor' => 2,
                                'rounding' => 'floor',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'key' => 'healing_block',
                'name' => 'Blocco della guarigione',
                'application_type' => 'on_damage',
                'target_scope' => 'target',
                'description' => 'La creatura danneggiata non può '
                    . 'recuperare punti ferita fino all’inizio del '
                    . 'turno successivo dell’incantatore.',
                'sort_order' => 2,
                'durations' => [
                    [
                        'key' => 'until_caster_next_turn',
                        'duration_type' => 'until_start_turn',
                        'turn_reference' => 'source',
                    ],
                ],
            ],
            [
                'key' => 'speed_reduction',
                'name' => 'Riduzione della velocità',
                'application_type' => 'on_start_turn',
                'target_scope' => 'target',
                'description' => 'Una creatura visibile scelta entro '
                    . '9,144 metri riduce la velocità di 3,048 metri '
                    . 'fino all’inizio del turno successivo '
                    . 'dell’incantatore.',
                'sort_order' => 3,
                'durations' => [
                    [
                        'key' => 'until_caster_next_turn',
                        'duration_type' => 'until_start_turn',
                        'turn_reference' => 'source',
                    ],
                ],
            ],
        ],
    ]),
];
