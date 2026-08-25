<?php

//Valori condivisi dai trucchetti del Calderone di Tasha
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
    'effects' => [],
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

//Requisito dell'arma condiviso da Lama Roboante e Lama Verdefiamma
$meleeWeapon = [[
    'key' => 'melee_weapon',
    'name' => 'Arma da mischia',
    'description' => 'Un’arma da mischia del valore di almeno 1 ma.',
    'cost_amount' => 1,
    'currency_code' => 'ma',
    'cost_is_minimum' => true,
    'consumed' => false,
    'focus_replaceable' => false,
]];

//Genera le soglie di crescita dei dadi dei trucchetti
$cantripScaling = function (array $diceByLevel): array {
    $levels = array_keys($diceByLevel);
    $scalings = [];

    foreach ($levels as $index => $level) {
        $nextLevel = $levels[$index + 1] ?? null;

        $scalings[] = [
            'key' => "character_level_{$level}",
            'target_field' => 'dice_count',
            'source_type' => 'character_level',
            'operation' => 'set',
            'minimum_source' => $level,
            'maximum_source' => $nextLevel === null
                ? null
                : $nextLevel - 1,
            'multiplier' => 0,
            'flat_value' => $diceByLevel[$level],
            'condition' => "Dal livello {$level} del personaggio.",
            'sort_order' => $index + 1,
        ];
    }

    return $scalings;
};

//Restituisce i 5 trucchetti definiti nel Calderone di Tasha
return [
    $spell([
        'key' => 'booming_blade',
        'name' => 'Lama Roboante',
        'school_key' => 'evocation',
        'page' => 113,
        'range_type' => 'self',
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un’arma da mischia del valore di '
            . 'almeno 1 ma.',
        'material_cost' => 0.1,
        'materials' => $meleeWeapon,
        'duration_type' => 'round',
        'duration_value' => 1,
        'attack_type' => 'melee',
        'description' => 'L’incantatore effettua un attacco con '
            . 'l’arma usata come componente e avvolge il bersaglio '
            . 'in energia roboante che lo punisce se si muove.',
        'higher_levels' => 'I danni da tuono dell’attacco e del '
            . 'movimento diventano rispettivamente 1d8 e 2d8 al '
            . '5° livello, 2d8 e 3d8 all’11° e 3d8 e 4d8 al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'La creatura deve trovarsi entro 1,524 metri '
                . 'dall’incantatore.',
        ],
        'effects' => [
            [
                'key' => 'weapon_attack',
                'name' => 'Attacco con l’arma',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'description' => 'Il bersaglio subisce i normali '
                    . 'effetti dell’attacco con l’arma usata come '
                    . 'componente materiale.',
                'sort_order' => 1,
                'damages' => [
                    [
                        'key' => 'thunder_on_hit',
                        'damage_type' => 'Tuono',
                        'dice_count' => 1,
                        'die_size' => 8,
                        'is_primary' => false,
                        'condition' => 'Si applica soltanto dal 5° '
                            . 'livello del personaggio.',
                        'sort_order' => 1,
                        'scalings' => $cantripScaling([
                            11 => 2,
                            17 => 3,
                        ]),
                    ],
                ],
            ],
            [
                'key' => 'booming_energy',
                'name' => 'Energia roboante',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => false,
                'condition' => 'Dopo che l’attacco con l’arma colpisce.',
                'description' => 'Se il bersaglio si muove '
                    . 'volontariamente di almeno 1,5 metri, subisce '
                    . 'danni da tuono.',
                'sort_order' => 2,
                'damages' => [
                    [
                        'key' => 'thunder_on_move',
                        'damage_type' => 'Tuono',
                        'dice_count' => 1,
                        'die_size' => 8,
                        'is_primary' => true,
                        'condition' => 'Il bersaglio si muove '
                            . 'volontariamente di almeno 1,5 metri '
                            . 'prima dell’inizio del turno successivo '
                            . 'dell’incantatore.',
                        'sort_order' => 1,
                        'scalings' => $cantripScaling([
                            5 => 2,
                            11 => 3,
                            17 => 4,
                        ]),
                    ],
                ],
                'durations' => [
                    [
                        'key' => 'until_caster_next_turn',
                        'duration_type' => 'until_start_turn',
                        'turn_reference' => 'source',
                        'condition' => 'Termina all’inizio del turno '
                            . 'successivo dell’incantatore.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'green_flame_blade',
        'name' => 'Lama Verdefiamma',
        'school_key' => 'evocation',
        'page' => 113,
        'range_type' => 'self',
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un’arma da mischia del valore di '
            . 'almeno 1 ma.',
        'material_cost' => 0.1,
        'materials' => $meleeWeapon,
        'attack_type' => 'melee',
        'description' => 'L’incantatore attacca una creatura con '
            . 'l’arma usata come componente e può far balzare una '
            . 'fiamma verde su una seconda creatura vicina.',
        'higher_levels' => 'I danni da fuoco inflitti al primo e al '
            . 'secondo bersaglio aumentano di 1d8 al 5°, 11° e '
            . '17° livello.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 2,
            'requires_sight' => true,
            'notes' => 'Il primo bersaglio deve trovarsi entro '
                . '1,524 metri; il secondo deve essere visibile e '
                . 'trovarsi entro 1,524 metri dal primo.',
        ],
        'effects' => [
            [
                'key' => 'weapon_attack',
                'name' => 'Attacco con l’arma',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'description' => 'Il primo bersaglio subisce i '
                    . 'normali effetti dell’attacco con l’arma.',
                'sort_order' => 1,
                'damages' => [
                    [
                        'key' => 'primary_fire',
                        'damage_type' => 'Fuoco',
                        'dice_count' => 1,
                        'die_size' => 8,
                        'condition' => 'Si applica soltanto dal 5° '
                            . 'livello del personaggio.',
                        'sort_order' => 1,
                        'scalings' => $cantripScaling([
                            11 => 2,
                            17 => 3,
                        ]),
                    ],
                ],
            ],
            [
                'key' => 'green_flame_jump',
                'name' => 'Balzo della fiamma verde',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'condition' => 'Una seconda creatura scelta '
                    . 'dall’incantatore è visibile entro 1,5 metri '
                    . 'dal primo bersaglio.',
                'description' => 'La seconda creatura subisce danni '
                    . 'da fuoco pari al modificatore della '
                    . 'caratteristica da incantatore, più i dadi '
                    . 'ottenuti con la crescita del trucchetto.',
                'sort_order' => 2,
                'damages' => [
                    [
                        'key' => 'secondary_spellcasting_modifier',
                        'damage_type' => 'Fuoco',
                        'modifier_source_type' =>
                            'caster_ability_modifier',
                        'modifier_multiplier' => 1,
                        'is_primary' => true,
                        'sort_order' => 1,
                        'notes' => 'Usa la caratteristica da '
                            . 'incantatore impiegata per lanciare '
                            . 'l’incantesimo.',
                    ],
                    [
                        'key' => 'secondary_fire_dice',
                        'damage_type' => 'Fuoco',
                        'dice_count' => 1,
                        'die_size' => 8,
                        'condition' => 'Si applica soltanto dal 5° '
                            . 'livello del personaggio.',
                        'sort_order' => 2,
                        'scalings' => $cantripScaling([
                            11 => 2,
                            17 => 3,
                        ]),
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'lightning_lure',
        'name' => 'Lenza Elettrizzante',
        'school_key' => 'evocation',
        'page' => 114,
        'range_type' => 'self',
        'verbal_component' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Una frusta di fulmine trascina una creatura '
            . 'verso l’incantatore e la danneggia se arriva abbastanza '
            . 'vicino a lui.',
        'higher_levels' => 'I danni da fulmine aumentano al 5°, 11° '
            . 'e 17° livello, diventando rispettivamente 2d8, 3d8 '
            . 'e 4d8.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Il bersaglio deve trovarsi entro 4,572 metri '
                . 'dall’incantatore.',
        ],
        'effects' => [
            [
                'key' => 'failed_strength_save',
                'name' => 'Trazione elettrizzante',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'description' => 'Il bersaglio viene trascinato verso '
                    . 'l’incantatore e può subire danni da fulmine.',
                'damages' => [
                    [
                        'key' => 'lightning_damage',
                        'damage_type' => 'Fulmine',
                        'dice_count' => 1,
                        'die_size' => 8,
                        'is_primary' => true,
                        'condition' => 'Dopo la trazione il bersaglio '
                            . 'si trova entro 1,5 metri '
                            . 'dall’incantatore.',
                        'scalings' => $cantripScaling([
                            5 => 2,
                            11 => 3,
                            17 => 4,
                        ]),
                    ],
                ],
                'forced_movements' => [
                    [
                        'key' => 'pull_toward_caster',
                        'movement_type' => 'pull',
                        'origin_type' => 'source',
                        'direction_type' => 'toward_origin',
                        'distance' => 3.048,
                        'up_to_distance' => true,
                        'straight_line' => true,
                        'stops_at_obstacle' => true,
                        'opportunity_attack_rule' =>
                            'does_not_provoke',
                        'notes' => 'La distanza massima ufficiale '
                            . 'è 3 metri nella traduzione italiana.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'mind_sliver',
        'name' => 'Scheggia della Mente',
        'school_key' => 'enchantment',
        'page' => 114,
        'range' => 18.288,
        'verbal_component' => true,
        'duration_type' => 'round',
        'duration_value' => 1,
        'saving_throw' => 'INT',
        'save_success_damage' => 'none',
        'description' => 'Disorienta la mente di una creatura, '
            . 'infliggendo danni psichici e sottraendo 1d4 al suo '
            . 'prossimo tiro salvezza prima della fine dell’effetto.',
        'higher_levels' => 'I danni psichici aumentano al 5°, 11° '
            . 'e 17° livello, diventando rispettivamente 2d6, 3d6 '
            . 'e 4d6.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
        'effects' => [
            [
                'key' => 'psychic_damage',
                'name' => 'Danno psichico',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'damages' => [
                    [
                        'key' => 'mind_sliver_damage',
                        'damage_type' => 'Psichico',
                        'dice_count' => 1,
                        'die_size' => 6,
                        'is_primary' => true,
                        'scalings' => $cantripScaling([
                            5 => 2,
                            11 => 3,
                            17 => 4,
                        ]),
                    ],
                ],
                'sort_order' => 1,
            ],
            [
                'key' => 'saving_throw_penalty',
                'name' => 'Penalità al prossimo tiro salvezza',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'ends_with_source' => false,
                'roll_modifiers' => [
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'penalty',
                        'dice_count' => 1,
                        'die_size' => 4,
                        'condition' => 'Si applica soltanto al primo '
                            . 'tiro salvezza effettuato dal bersaglio.',
                        'sort_order' => 1,
                        'notes' => 'Sottrae il risultato di 1d4.',
                    ],
                ],
                'durations' => [
                    [
                        'key' => 'until_caster_next_turn_end',
                        'duration_type' => 'until_end_turn',
                        'turn_reference' => 'source',
                        'condition' => 'Termina dopo il primo tiro '
                            . 'salvezza interessato o alla fine del '
                            . 'turno successivo dell’incantatore.',
                    ],
                ],
                'sort_order' => 2,
            ],
        ],
    ]),

    $spell([
        'key' => 'sword_burst',
        'name' => 'Turbine di Spade',
        'school_key' => 'conjuration',
        'page' => 116,
        'range_type' => 'self',
        'verbal_component' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Crea un cerchio momentaneo di spade '
            . 'spettrali che infligge danni da forza alle creature '
            . 'attorno all’incantatore.',
        'higher_levels' => 'I danni da forza aumentano al 5°, 11° '
            . 'e 17° livello, diventando rispettivamente 2d6, 3d6 '
            . 'e 4d6.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 1.524,
            'can_target_self' => false,
            'notes' => 'L’emanazione esclude l’incantatore.',
        ],
        'effects' => [
            [
                'key' => 'failed_dexterity_save',
                'name' => 'Danno delle spade spettrali',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'condition' => 'Ogni altra creatura nell’area che '
                    . 'fallisce il tiro salvezza su Destrezza.',
                'damages' => [
                    [
                        'key' => 'force_damage',
                        'damage_type' => 'Forza',
                        'dice_count' => 1,
                        'die_size' => 6,
                        'is_primary' => true,
                        'scalings' => $cantripScaling([
                            5 => 2,
                            11 => 3,
                            17 => 4,
                        ]),
                    ],
                ],
            ],
        ],
    ]),
];
