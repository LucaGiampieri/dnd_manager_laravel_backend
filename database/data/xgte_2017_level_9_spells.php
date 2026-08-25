<?php

//Valori condivisi dagli incantesimi di 9° livello di Xanathar
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

//Restituisce i 3 incantesimi di 9° livello di Xanathar
return [
    $spell([
        'key' => 'invulnerability',
        'name' => 'Invulnerabilità',
        'school_key' => 'abjuration',
        'page' => 160,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un piccolo frammento di adamantio '
            . 'del valore di almeno 500 mo, che l’incantesimo consuma.',
        'material_consumed' => true,
        'material_cost' => 500,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Rende l’incantatore immune a tutti i danni '
            . 'per la durata dell’incantesimo.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],
    ]),

    $spell([
        'key' => 'mass_polymorph',
        'name' => 'Metamorfosi di Massa',
        'school_key' => 'transmutation',
        'page' => 161,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Il bozzolo di un bruco.',
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Trasforma fino a dieci creature visibili in '
            . 'bestie scelte dall’incantatore, conferendo punti ferita '
            . 'temporanei basati sulle nuove forme.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 10,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'psychic_scream',
        'name' => 'Urlo Psichico',
        'school_key' => 'enchantment',
        'page' => 171,
        'range' => 27.432,
        'somatic_component' => true,
        'saving_throw' => 'INT',
        'save_success_damage' => 'half',
        'description' => 'Scatena energia mentale contro fino a dieci '
            . 'creature, infliggendo danni psichici e stordendo chi '
            . 'fallisce il tiro salvezza.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 10,
            'requires_sight' => true,
        ],
    ]),
];
