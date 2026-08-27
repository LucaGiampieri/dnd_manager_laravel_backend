<?php

//Valori condivisi dagli incantesimi di 6° livello di Tasha
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

//Regole di controllo comuni allo Spirito Demoniaco
$summonControlRules = 'Condivide l’iniziativa con l’incantatore '
    . 'e agisce subito dopo di lui. Obbedisce ai comandi verbali '
    . 'senza richiedere azioni; senza comandi usa Schivata e si '
    . 'allontana dal pericolo.';

//Crea le progressioni comuni delle tre forme dello Spirito Demoniaco
$fiendScalings = function (
    string $attackActionKey,
    string $attackKey,
    bool $hasDeathThroes = false
): array {
    $scalings = [
        [
            'key' => 'armor_class_from_slot',
            'target_type' => 'armor_class',
            'source_type' => 'slot_level',
            'operation' => 'add',
            'sort_order' => 1,
        ],
        [
            'key' => 'hit_points_above_sixth',
            'target_type' => 'hit_points',
            'source_type' => 'slot_level',
            'operation' => 'add',
            'minimum_source' => 7,
            'source_offset' => -6,
            'multiplier' => 15,
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
        [
            'key' => $attackActionKey . '_attack_from_caster',
            'target_type' => 'attack_bonus',
            'target_ref' => $attackActionKey . ':' . $attackKey,
            'source_type' => 'caster_spell_attack_bonus',
            'operation' => 'set',
            'sort_order' => 4,
        ],
        [
            'key' => $attackActionKey . '_damage_from_slot',
            'target_type' => 'damage_bonus',
            'target_ref' => $attackActionKey . ':1',
            'source_type' => 'slot_level',
            'operation' => 'add',
            'sort_order' => 5,
        ],
    ];

    //Soltanto la forma Demone aggiunge lo slot ai danni alla morte
    if ($hasDeathThroes) {
        $scalings[] = [
            'key' => 'death_throes_damage_from_slot',
            'target_type' => 'damage_bonus',
            'target_ref' => 'death_throes:1',
            'source_type' => 'slot_level',
            'operation' => 'add',
            'sort_order' => 6,
        ];
    }

    return $scalings;
};

//Crea una forma completa dello Spirito Demoniaco
$fiendForm = function (
    string $name,
    int $baseHitPoints,
    array $movements,
    string $attackActionKey,
    string $attackName,
    string $attackKey,
    string $attackType,
    int $diceCount,
    int $dieSize,
    string $damageType,
    ?float $reach,
    ?float $range,
    string $specialTrait,
    array $extraActions,
    bool $hasDeathThroes = false,
    bool $isDefault = false
) use ($fiendScalings): array {
    $actions = [
        //Numero di attacchi dipendente dal livello dello slot
        [
            'key' => 'multiattack',
            'name' => 'Multiattacco',
            'description' => 'Effettua un numero di attacchi '
                . $attackName . ' pari alla metà del livello dello '
                . 'slot, arrotondata per difetto.',
            'sort_order' => 1,
        ],

        //Attacco esclusivo della forma scelta
        [
            'key' => $attackActionKey,
            'name' => $attackName,
            'description' => 'Usa il modificatore di attacco '
                . 'dell’incantesimo e infligge il danno indicato.',
            'sort_order' => 2,
            'attacks' => [
                [
                    'key' => $attackKey,
                    'name' => $attackName,
                    'attack_type' => $attackType,
                    'attack_kind' => 'weapon',
                    'reach' => $reach,
                    'range' => $range,
                    'target_count' => 1,
                    'notes' => 'Usa il modificatore di attacco '
                        . 'dell’incantesimo.',
                ],
            ],
            'damages' => [
                [
                    'attack_key' => $attackKey,
                    'damage_type' => $damageType,
                    'dice_count' => $diceCount,
                    'die_size' => $dieSize,
                    'bonus' => 3,
                    'is_primary' => true,
                    'sort_order' => 1,
                ],
            ],
        ],
    ];

    //Aggiunge le capacità proprie della forma dopo l'attacco
    foreach ($extraActions as $extraAction) {
        $actions[] = $extraAction;
    }

    return [
        'name' => $name,
        'description' => "Forma {$name} dello Spirito Demoniaco.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito Demoniaco ({$name})",
            'abilities' => [
                'FOR' => 13,
                'DES' => 16,
                'COS' => 15,
                'INT' => 10,
                'SAG' => 10,
                'CAR' => 16,
            ],
            'armor_class' => [
                'value' => 12,
                'description' => '12 + il livello dello slot.',
            ],
            'hit_points' => [
                'average_hit_points' => $baseHitPoints,
                'special_calculation' => $baseHitPoints
                    . ' + 15 per ogni livello dello slot sopra il 6°.',
            ],
            'movements' => $movements,
            'actions' => $actions,
            'description' => 'Immondo di taglia Grande evocato, '
                . 'alleato dell’incantatore.',
            'notes' => 'Resistenza ai danni da fuoco; immunità ai '
                . 'danni da veleno e alla condizione avvelenato; '
                . 'scurovisione 18,288 metri; resistenza alla magia; '
                . 'Percezione passiva 10; parla Abissale, Infernale '
                . 'e telepatia. ' . $specialTrait,
        ],
        'scalings' => $fiendScalings(
            $attackActionKey,
            $attackKey,
            $hasDeathThroes
        ),
    ];
};

//Restituisce i due incantesimi di 6° livello introdotti da Tasha
return [
    //Abito Ultraterreno di Tasha
    $spell([
        'key' => 'tashas_otherworldly_guise',
        'name' => 'Abito Ultraterreno di Tasha',
        'school_key' => 'transmutation',
        'page' => 106,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un oggetto su cui è inciso il '
            . 'simbolo dei Piani Esterni del valore di almeno 500 mo.',
        'material_cost' => 500,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'L’incantatore assume un aspetto '
            . 'ultraterreno legato ai Piani Inferiori o Superiori, '
            . 'ottenendo difese, ali e capacità marziali potenziate.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'Tutti i benefici si applicano '
                . 'all’incantatore.',
        ],
        'effects' => [
            //Difese determinate dal piano scelto al lancio
            [
                'key' => 'planar_defense',
                'name' => 'Difesa Planare',
                'application_type' => 'automatic',
                'target_scope' => 'source',
                'description' => 'Piani Inferiori: immunità ai danni '
                    . 'da fuoco o veleno e alla condizione avvelenato. '
                    . 'Piani Superiori: immunità ai danni radiosi o '
                    . 'necrotici e alla condizione affascinato.',
                'condition' => 'Il beneficio dipende dal gruppo di '
                    . 'Piani Esterni scelto al lancio.',
                'sort_order' => 1,
            ],

            //Movimento aereo fornito dalle ali spettrali
            [
                'key' => 'spectral_wings',
                'name' => 'Ali Spettrali',
                'application_type' => 'automatic',
                'target_scope' => 'source',
                'description' => 'Spuntano ali spettrali che '
                    . 'conferiscono una velocità di volare di '
                    . '12,192 metri.',
                'sort_order' => 2,
            ],

            //Bonus difensivi e offensivi dell'abito
            [
                'key' => 'empowered_combat',
                'name' => 'Combattimento Ultraterreno',
                'application_type' => 'automatic',
                'target_scope' => 'source',
                'description' => 'Conferisce +2 alla CA; gli attacchi '
                    . 'con arma sono magici; per i tiri per colpire e '
                    . 'i danni delle armi si può usare il modificatore '
                    . 'da incantatore. Con l’azione Attacco si può '
                    . 'attaccare due volte, salvo una capacità che '
                    . 'consenta già più attacchi.',
                'sort_order' => 3,
            ],
        ],
    ]),

    //Evoca Immondo
    $spell([
        'key' => 'summon_fiend',
        'name' => 'Evoca Immondo',
        'school_key' => 'conjuration',
        'page' => 110,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Sangue umanoide contenuto in una '
            . 'fiala di rubino del valore di almeno 600 mo.',
        'material_cost' => 600,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Invoca uno spirito demoniaco scegliendo la '
            . 'forma Demone, Diavolo o Yugoloth.',
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
                'name' => 'Spirito Demoniaco',
                'selection_condition' => 'Scegliere Demone, Diavolo '
                    . 'o Yugoloth.',
                'control_rules' => $summonControlRules,
                'templates' => [
                    [
                        'name' => 'Spirito Demoniaco',
                        'creature_type_key' => 'fiend',
                        'size_name' => 'Grande',
                        'forms' => [
                            //Forma demoniaca con esplosione alla morte
                            $fiendForm(
                                'Demone',
                                50,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 12.192,
                                    ],
                                    [
                                        'type' => 'Scalare',
                                        'speed' => 12.192,
                                    ],
                                ],
                                'bite',
                                'Morso',
                                'bite_attack',
                                'melee',
                                1,
                                12,
                                'Necrotico',
                                1.524,
                                null,
                                'Quando scende a 0 punti ferita o '
                                    . 'l’incantesimo termina, esplode.',
                                [
                                    [
                                        'key' => 'death_throes',
                                        'name' => 'Spasmi di Morte',
                                        'action_type' => 'special',
                                        'description' => 'Quando lo '
                                            . 'spirito scende a 0 PF '
                                            . 'o l’incantesimo '
                                            . 'termina, ogni creatura '
                                            . 'entro 3,048 metri deve '
                                            . 'effettuare un TS su '
                                            . 'Destrezza.',
                                        'trigger' => 'Quando scende a '
                                            . '0 PF o termina '
                                            . 'l’incantesimo.',
                                        'sort_order' => 3,
                                        'damages' => [
                                            [
                                                'damage_type' => 'Fuoco',
                                                'dice_count' => 2,
                                                'die_size' => 10,
                                                'bonus' => 0,
                                                'is_primary' => true,
                                                'sort_order' => 1,
                                            ],
                                        ],
                                        'saving_throws' => [
                                            [
                                                'key' => 'death_throes_save',
                                                'ability' => 'DES',
                                                'success_type' => 'half_damage',
                                                'failure_description' =>
                                                    'Subisce tutti i '
                                                    . 'danni da fuoco.',
                                                'success_description' =>
                                                    'Subisce metà dei '
                                                    . 'danni da fuoco.',
                                                'notes' => 'La CD è la '
                                                    . 'CD degli '
                                                    . 'incantesimi.',
                                            ],
                                        ],
                                    ],
                                ],
                                true,
                                true
                            ),

                            //Forma diabolica con attacco a distanza
                            $fiendForm(
                                'Diavolo',
                                40,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 12.192,
                                    ],
                                    [
                                        'type' => 'Volare',
                                        'speed' => 18.288,
                                    ],
                                ],
                                'hurl_flame',
                                'Scagliare Fiamma',
                                'hurl_flame_attack',
                                'ranged',
                                2,
                                6,
                                'Fuoco',
                                null,
                                45.72,
                                'Vista del Diavolo: l’oscurità '
                                    . 'magica non impedisce la '
                                    . 'scurovisione. Un oggetto '
                                    . 'infiammabile non indossato né '
                                    . 'trasportato prende fuoco.',
                                []
                            ),

                            //Forma yugoloth con teletrasporto tattico
                            $fiendForm(
                                'Yugoloth',
                                60,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 12.192,
                                    ],
                                ],
                                'claws',
                                'Artigli',
                                'claws_attack',
                                'melee',
                                1,
                                8,
                                'Tagliente',
                                1.524,
                                null,
                                'Immediatamente dopo aver colpito o '
                                    . 'mancato con Artigli può '
                                    . 'teletrasportarsi fino a 9,144 '
                                    . 'metri in uno spazio libero '
                                    . 'visibile.',
                                []
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),
];
