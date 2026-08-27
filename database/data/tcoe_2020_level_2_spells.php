<?php

//Valori condivisi dagli incantesimi di 2° livello di Tasha
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

//Regole condivise dalle tre forme dello Spirito Bestiale
$beastAbilities = [
    'FOR' => 18,
    'DES' => 11,
    'COS' => 16,
    'INT' => 4,
    'SAG' => 14,
    'CAR' => 5,
];

$beastActions = [
    //Il numero di attacchi cresce con il livello dello slot
    [
        'key' => 'multiattack',
        'name' => 'Multiattacco',
        'description' => 'Lo spirito effettua un numero di attacchi '
            . 'Dilaniare pari alla metà del livello dello slot, '
            . 'arrotondata per difetto.',
        'sort_order' => 1,
    ],

    //Attacco principale comune a tutte le forme
    [
        'key' => 'rend',
        'name' => 'Dilaniare',
        'description' => 'Attacco con Arma da Mischia che usa il '
            . 'modificatore di attacco dell’incantesimo.',
        'sort_order' => 2,
        'attacks' => [
            [
                'key' => 'rend_attack',
                'name' => 'Dilaniare',
                'attack_type' => 'melee',
                'attack_kind' => 'weapon',
                'reach' => 1.524,
                'target_count' => 1,
                'notes' => 'Il bonus al tiro per colpire è quello '
                    . 'dell’attacco con incantesimo del personaggio.',
            ],
        ],
        'damages' => [
            [
                'attack_key' => 'rend_attack',
                'damage_type' => 'Perforante',
                'dice_count' => 1,
                'die_size' => 8,
                'bonus' => 4,
                'is_primary' => true,
                'sort_order' => 1,
                'notes' => 'Al bonus base si aggiunge il livello '
                    . 'dello slot usato per evocare lo spirito.',
            ],
        ],
    ],
];

//Crea una forma dello Spirito Bestiale senza duplicare lo stat block
$beastForm = function (
    string $name,
    int $baseHitPoints,
    array $movements,
    string $traits,
    bool $isDefault = false
) use ($beastAbilities, $beastActions): array {
    return [
        'name' => $name,
        'description' => "Forma {$name} dello Spirito Bestiale.",
        'is_default' => $isDefault,
        'stat_block' => [
            'name' => "Spirito Bestiale ({$name})",
            'abilities' => $beastAbilities,
            'armor_class' => [
                'value' => 11,
                'type' => 'natural_armor',
                'description' => '11 + il livello dello slot '
                    . 'dell’incantesimo.',
            ],
            'hit_points' => [
                'average_hit_points' => $baseHitPoints,
                'special_calculation' => $baseHitPoints
                    . ' + 5 per ogni livello dello slot sopra il 2°.',
            ],
            'movements' => $movements,
            'actions' => $beastActions,
            'description' => 'Bestia primordiale evocata; comprende '
                . 'i linguaggi parlati dall’incantatore e usa il suo '
                . 'bonus di competenza.',
            'notes' => 'Scurovisione 18 metri; Percezione passiva 12. '
                . $traits,
        ],
        'scalings' => [
            [
                'key' => 'armor_class_from_slot',
                'target_type' => 'armor_class',
                'source_type' => 'slot_level',
                'operation' => 'add',
                'condition' => 'Si aggiunge il livello dello slot.',
                'sort_order' => 1,
            ],
            [
                'key' => 'hit_points_above_second',
                'target_type' => 'hit_points',
                'source_type' => 'slot_level',
                'operation' => 'add',
                'minimum_source' => 3,
                'source_offset' => -2,
                'multiplier' => 5,
                'condition' => '5 PF per livello dello slot sopra il 2°.',
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
                'key' => 'rend_attack_from_caster',
                'target_type' => 'attack_bonus',
                'target_ref' => 'rend:rend_attack',
                'source_type' => 'caster_spell_attack_bonus',
                'operation' => 'set',
                'sort_order' => 4,
            ],
            [
                'key' => 'rend_damage_from_slot',
                'target_type' => 'damage_bonus',
                'target_ref' => 'rend:1',
                'source_type' => 'slot_level',
                'operation' => 'add',
                'sort_order' => 5,
            ],
        ],
    ];
};

//Restituisce i due incantesimi di 2° livello introdotti da Tasha
return [
    //Evocare Bestia e le sue tre forme ambientali
    $spell([
        'key' => 'summon_beast',
        'name' => 'Evocare Bestia',
        'school_key' => 'conjuration',
        'page' => 112,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una piuma, un ciuffo di pelliccia '
            . 'e una coda di pesce all’interno di una ghianda dorata '
            . 'del valore di almeno 200 mo.',
        'material_cost' => 200,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Invoca uno spirito bestiale in uno spazio '
            . 'libero visibile entro gittata, scegliendo una forma '
            . 'legata all’aria, alla terra o all’acqua.',
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
                'name' => 'Spirito Bestiale',
                'selection_type' => 'special',
                'quantity_type' => 'exact',
                'quantity' => 1,
                'selection_condition' => 'L’incantatore sceglie '
                    . 'la forma Aria, Terra o Acqua.',
                'control_rules' => 'Condivide l’iniziativa con '
                    . 'l’incantatore e agisce subito dopo di lui. '
                    . 'Obbedisce ai comandi verbali senza richiedere '
                    . 'azioni; senza comandi usa Schivata e si '
                    . 'allontana dal pericolo.',
                'templates' => [
                    [
                        'name' => 'Spirito Bestiale',
                        'creature_type_key' => 'beast',
                        'size_name' => 'Piccola',
                        'description' => 'Stat block condiviso dallo '
                            . 'spirito, specializzato dall’ambiente.',
                        'forms' => [
                            $beastForm(
                                'Aria',
                                20,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                    [
                                        'type' => 'Volare',
                                        'speed' => 18.288,
                                    ],
                                ],
                                'Sfuggente: non provoca attacchi '
                                    . 'di opportunità quando esce '
                                    . 'volando dalla portata.',
                                true
                            ),
                            $beastForm(
                                'Terra',
                                30,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                    [
                                        'type' => 'Scalare',
                                        'speed' => 9.144,
                                    ],
                                ],
                                'Tattiche del Branco: vantaggio '
                                    . 'all’attacco quando un alleato '
                                    . 'non incapacitato è adiacente '
                                    . 'al bersaglio.'
                            ),
                            $beastForm(
                                'Acqua',
                                30,
                                [
                                    [
                                        'type' => 'Terrestre',
                                        'speed' => 9.144,
                                    ],
                                    [
                                        'type' => 'Nuotare',
                                        'speed' => 18.288,
                                    ],
                                ],
                                'Tattiche del Branco; può respirare '
                                    . 'soltanto sott’acqua.'
                            ),
                        ],
                    ],
                ],
            ],
        ],
    ]),

    //Scudiscio Mentale di Tasha
    $spell([
        'key' => 'tashas_mind_whip',
        'name' => 'Scudiscio Mentale di Tasha',
        'school_key' => 'enchantment',
        'page' => 115,
        'range' => 27.432,
        'verbal_component' => true,
        'duration_type' => 'round',
        'duration_value' => 1,
        'saving_throw' => 'INT',
        'save_success_damage' => 'half',
        'description' => 'Il bersaglio effettua un tiro salvezza su '
            . 'Intelligenza. Se fallisce subisce danni psichici, '
            . 'perde le reazioni e nel turno successivo deve scegliere '
            . 'tra movimento, azione o azione bonus.',
        'higher_levels' => 'Bersaglia una creatura aggiuntiva per '
            . 'ogni livello dello slot superiore al 2°; i bersagli '
            . 'devono trovarsi entro 9,144 metri l’uno dall’altro.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 1,
            'requires_sight' => true,
        ],
        'effects' => [
            [
                'key' => 'psychic_assault',
                'name' => 'Assalto psichico',
                'application_type' => 'failed_save',
                'target_scope' => 'targets',
                'description' => 'Infligge danni psichici; un tiro '
                    . 'salvezza riuscito dimezza soltanto i danni.',
                'sort_order' => 1,
                'damages' => [
                    [
                        'key' => 'psychic_damage',
                        'damage_type' => 'Psichico',
                        'dice_count' => 3,
                        'die_size' => 6,
                        'is_primary' => true,
                    ],
                ],
                'scalings' => [
                    [
                        'key' => 'extra_target_per_slot',
                        'target_field' => 'target_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 3,
                        'source_offset' => -2,
                        'condition' => 'Una creatura aggiuntiva per '
                            . 'livello dello slot sopra il 2°.',
                    ],
                ],
            ],
            [
                'key' => 'limited_turn',
                'name' => 'Turno limitato',
                'application_type' => 'failed_save',
                'target_scope' => 'targets',
                'ends_with_source' => false,
                'description' => 'Il bersaglio non può usare reazioni '
                    . 'fino alla fine del suo turno successivo. '
                    . 'Durante quel turno può effettuare soltanto una '
                    . 'tra movimento, azione e azione bonus.',
                'sort_order' => 2,
                'durations' => [
                    [
                        'key' => 'until_target_next_turn_end',
                        'duration_type' => 'until_end_turn',
                        'turn_reference' => 'target',
                        'condition' => 'Termina alla fine del turno '
                            . 'successivo del bersaglio.',
                    ],
                ],
            ],
        ],
    ]),
];
