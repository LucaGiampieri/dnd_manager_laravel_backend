<?php

//Valori condivisi dagli incantesimi di 5° livello di Tasha
$defaults = [
    'level' => 5,
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

//Regole di controllo comuni allo Spirito Celestiale
$summonControlRules = 'Condivide l’iniziativa con l’incantatore '
    . 'e agisce subito dopo di lui. Obbedisce ai comandi verbali '
    . 'senza richiedere azioni; senza comandi usa Schivata e si '
    . 'allontana dal pericolo.';

//Crea una forma completa dello Spirito Celestiale
$celestialForm = function (
    string $name,
    int $baseArmorClass,
    string $attackKey,
    string $attackName,
    string $attackType,
    int $diceCount,
    int $dieSize,
    int $bonus,
    ?float $reach,
    ?float $range,
    ?float $longRange,
    string $extraEffect,
    bool $isDefault = false
): array {
    return [
        'name' => $name,
        'description' => "Forma {$name} dello Spirito Celestiale.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito Celestiale ({$name})",
            'abilities' => [
                'FOR' => 16,
                'DES' => 14,
                'COS' => 16,
                'INT' => 10,
                'SAG' => 14,
                'CAR' => 16,
            ],
            'armor_class' => [
                'value' => $baseArmorClass,
                'description' => $baseArmorClass
                    . ' + il livello dello slot.',
            ],
            'hit_points' => [
                'average_hit_points' => 40,
                'special_calculation' => '40 + 10 per ogni livello '
                    . 'dello slot sopra il 5°.',
            ],
            'movements' => [
                [
                    'type' => 'Terrestre',
                    'speed' => 9.144,
                ],
                [
                    'type' => 'Volare',
                    'speed' => 12.192,
                ],
            ],
            'actions' => [
                //Numero di attacchi dipendente dal livello dello slot
                [
                    'key' => 'multiattack',
                    'name' => 'Multiattacco',
                    'description' => 'Effettua un numero di attacchi '
                        . $attackName . ' pari alla metà del livello '
                        . 'dello slot, arrotondata per difetto.',
                    'sort_order' => 1,
                ],

                //Attacco esclusivo della forma scelta
                [
                    'key' => $attackKey,
                    'name' => $attackName,
                    'description' => $extraEffect,
                    'sort_order' => 2,
                    'attacks' => [
                        [
                            'key' => $attackKey . '_attack',
                            'name' => $attackName,
                            'attack_type' => $attackType,
                            'attack_kind' => 'weapon',
                            'reach' => $reach,
                            'range' => $range,
                            'long_range' => $longRange,
                            'target_count' => 1,
                            'notes' => 'Usa il modificatore di '
                                . 'attacco dell’incantesimo.',
                        ],
                    ],
                    'damages' => [
                        [
                            'attack_key' => $attackKey . '_attack',
                            'damage_type' => 'Radioso',
                            'dice_count' => $diceCount,
                            'die_size' => $dieSize,
                            'bonus' => $bonus,
                            'is_primary' => true,
                            'sort_order' => 1,
                        ],
                    ],
                ],

                //Guarigione utilizzabile una volta al giorno
                [
                    'key' => 'healing_touch',
                    'name' => 'Tocco Guaritore',
                    'description' => 'Tocca un’altra creatura, che '
                        . 'recupera 2d8 + il livello dello slot punti '
                        . 'ferita.',
                    'max_uses' => 1,
                    'recharge_type' => 'per_day',
                    'sort_order' => 3,
                    'notes' => 'La formula è conservata nella '
                        . 'descrizione perché le azioni degli stat '
                        . 'block non possiedono una tabella healing.',
                ],
            ],
            'description' => 'Celestiale angelico evocato, alleato '
                . 'dell’incantatore.',
            'notes' => 'Resistenza ai danni radiosi; immunità alle '
                . 'condizioni affascinato e spaventato; scurovisione '
                . '18,288 metri; Percezione passiva 12; parla '
                . 'Celestiale e comprende i linguaggi del personaggio.',
        ],
        'scalings' => [
            [
                'key' => 'armor_class_from_slot',
                'target_type' => 'armor_class',
                'source_type' => 'slot_level',
                'operation' => 'add',
                'sort_order' => 1,
            ],
            [
                'key' => 'hit_points_above_fifth',
                'target_type' => 'hit_points',
                'source_type' => 'slot_level',
                'operation' => 'add',
                'minimum_source' => 6,
                'source_offset' => -5,
                'multiplier' => 10,
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
                'key' => $attackKey . '_attack_from_caster',
                'target_type' => 'attack_bonus',
                'target_ref' => $attackKey . ':'
                    . $attackKey . '_attack',
                'source_type' => 'caster_spell_attack_bonus',
                'operation' => 'set',
                'sort_order' => 4,
            ],
            [
                'key' => $attackKey . '_damage_from_slot',
                'target_type' => 'damage_bonus',
                'target_ref' => $attackKey . ':1',
                'source_type' => 'slot_level',
                'operation' => 'add',
                'sort_order' => 5,
            ],
            [
                'key' => 'healing_touch_from_slot',
                'target_type' => 'other',
                'source_type' => 'slot_level',
                'operation' => 'add',
                'sort_order' => 6,
                'notes' => 'Aggiunge il livello dello slot ai 2d8 '
                    . 'di Tocco Guaritore.',
            ],
        ],
    ];
};

//Restituisce l'unico incantesimo di 5° livello introdotto da Tasha
return [
    //Evoca Celestiale
    $spell([
        'key' => 'summon_celestial',
        'name' => 'Evoca Celestiale',
        'school_key' => 'conjuration',
        'page' => 108,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un reliquario dorato del valore '
            . 'di almeno 500 mo.',
        'material_cost' => 500,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Invoca uno spirito celestiale in forma '
            . 'angelica scegliendo Vendicatore o Difensore.',
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
                'name' => 'Spirito Celestiale',
                'selection_condition' => 'Scegliere Vendicatore '
                    . 'o Difensore.',
                'control_rules' => $summonControlRules,
                'templates' => [
                    [
                        'name' => 'Spirito Celestiale',
                        'creature_type_key' => 'celestial',
                        'size_name' => 'Grande',
                        'forms' => [
                            $celestialForm(
                                'Vendicatore',
                                11,
                                'radiant_bow',
                                'Arco Radioso',
                                'ranged',
                                2,
                                6,
                                2,
                                null,
                                45.72,
                                182.88,
                                'Attacco con Arma a Distanza che '
                                    . 'infligge danni radiosi.',
                                true
                            ),
                            $celestialForm(
                                'Difensore',
                                13,
                                'radiant_mace',
                                'Mazza Radiosa',
                                'melee',
                                1,
                                10,
                                3,
                                1.524,
                                null,
                                null,
                                'Dopo il colpo sceglie se stesso o '
                                    . 'una creatura visibile entro '
                                    . '3,048 metri dal bersaglio; la '
                                    . 'creatura scelta ottiene 1d10 '
                                    . 'punti ferita temporanei.'
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),
];
