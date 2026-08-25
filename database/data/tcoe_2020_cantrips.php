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
            . 'movimento aumentano al 5°, 11° e 17° livello.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'La creatura deve trovarsi entro 1,524 metri '
                . 'dall’incantatore.',
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
            . 'secondo bersaglio aumentano al 5°, 11° e 17° livello.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 2,
            'requires_sight' => true,
            'notes' => 'Il primo bersaglio deve trovarsi entro '
                . '1,524 metri; il secondo deve essere visibile e '
                . 'trovarsi entro 1,524 metri dal primo.',
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
            . 'e 17° livello.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Il bersaglio deve trovarsi entro 4,572 metri '
                . 'dall’incantatore.',
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
            . 'e 17° livello.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
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
            . 'e 17° livello.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 1.524,
            'can_target_self' => false,
            'notes' => 'L’emanazione esclude l’incantatore.',
        ],
    ]),
];
