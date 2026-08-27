<?php

//Valori condivisi dagli incantesimi di 1° livello di Tasha
$defaults = [
    'level' => 1,
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

//Restituisce l'unico incantesimo di 1° livello introdotto da Tasha
return [
    $spell([
        'key' => 'tashas_caustic_brew',
        'name' => 'Miscela Caustica di Tasha',
        'school_key' => 'evocation',
        'page' => 114,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un frammento di cibo avariato.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'L’incantatore emana una linea di acido. '
            . 'Le creature che falliscono il tiro salvezza restano '
            . 'ricoperte e subiscono danni all’inizio dei loro turni '
            . 'finché l’effetto non termina o l’acido viene rimosso.',
        'higher_levels' => 'I danni aumentano di 2d4 per ogni livello '
            . 'dello slot superiore al 1°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'line',
            'area_size_meters' => 9.144,
            'area_secondary_size_meters' => 1.524,
            'notes' => 'La linea parte dall’incantatore, è lunga '
                . '9,144 metri e larga 1,524 metri.',
        ],
        'effects' => [
            [
                'key' => 'acid_coating',
                'name' => 'Rivestimento di acido',
                'application_type' => 'on_start_turn',
                'target_scope' => 'targets',
                'ends_with_source' => true,
                'condition' => 'La creatura ha fallito il tiro '
                    . 'salvezza iniziale su Destrezza ed è ancora '
                    . 'ricoperta dall’acido.',
                'description' => 'La creatura subisce danni da acido '
                    . 'all’inizio di ogni suo turno.',
                'damages' => [
                    [
                        'key' => 'recurring_acid_damage',
                        'damage_type' => 'Acido',
                        'dice_count' => 2,
                        'die_size' => 4,
                        'is_primary' => true,
                        'condition' => 'All’inizio del turno della '
                            . 'creatura mentre è ricoperta dall’acido.',
                        'scalings' => [
                            [
                                'key' => 'extra_dice_per_slot_level',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 2,
                                'source_offset' => -1,
                                'multiplier' => 2,
                                'condition' => 'Quando viene usato '
                                    . 'uno slot di 2° livello '
                                    . 'o superiore.',
                                'notes' => 'Aggiunge 2d4 per ogni '
                                    . 'livello dello slot oltre il 1°.',
                            ],
                        ],
                    ],
                ],
                'durations' => [
                    [
                        'key' => 'until_spell_ends',
                        'duration_type' => 'until_source_ends',
                        'condition' => 'Termina insieme '
                            . 'all’incantesimo.',
                        'sort_order' => 1,
                    ],
                    [
                        'key' => 'until_acid_is_removed',
                        'duration_type' => 'until_condition',
                        'condition' => 'Termina sulla creatura quando '
                            . 'essa o un’altra creatura usa un’azione '
                            . 'per ripulire l’acido.',
                        'sort_order' => 2,
                    ],
                ],
            ],
        ],
    ]),
];
