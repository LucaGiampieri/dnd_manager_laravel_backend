<?php

//Valori condivisi dagli incantesimi di 8° livello di Xanathar
$defaults = [
    'level' => 8,
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

//Restituisce i 4 incantesimi di 8° livello di Xanathar
return [
    $spell([
        'key' => 'illusory_dragon',
        'name' => 'Drago Illusorio',
        'school_key' => 'illusion',
        'page' => 155,
        'range' => 36.576,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'INT',
        'save_success_damage' => 'half',
        'description' => 'Crea un enorme drago d’ombra che spaventa '
            . 'i nemici e può emettere coni di energia dannosa '
            . 'mentre viene spostato dall’incantatore.',
        'notes' => 'L’apparizione richiede un tiro salvezza su '
            . 'Saggezza; il soffio richiede un tiro su Intelligenza.',
        'target' => [
            'target_type' => 'special',
            'requires_sight' => true,
            'notes' => 'Il drago compare in uno spazio libero visibile '
                . 'entro gittata e il suo soffio forma un cono di '
                . '18,288 metri.',
        ],
    ]),

    $spell([
        'key' => 'mighty_fortress',
        'name' => 'Fortezza Possente',
        'school_key' => 'conjuration',
        'page' => 157,
        'casting_time_type' => 'minute',
        'range' => 1609.344,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un diamante del valore di almeno '
            . '500 mo, che l’incantesimo consuma.',
        'material_consumed' => true,
        'material_cost' => 500,
        'description' => 'Fa emergere dal terreno una fortezza in '
            . 'pietra completa di torrette, mura, rocca, arredi, cibo '
            . 'e servitori invisibili.',
        'notes' => 'Lanciato nello stesso luogo ogni sette giorni per '
            . 'un anno, rende permanente la fortezza.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'square',
            'area_size_meters' => 36.576,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'abi_dalzims_horrid_wilting',
        'name' => 'Orrido Avvizzimento di Abi-Dalzim',
        'school_key' => 'necromancy',
        'page' => 163,
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un frammento di spugna.',
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Estrae l’umidità dalle creature in un '
            . 'grande cubo, infliggendo ingenti danni necrotici e '
            . 'facendo avvizzire i vegetali non magici.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 9.144,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'maddening_darkness',
        'name' => 'Oscurità della Follia',
        'school_key' => 'evocation',
        'page' => 163,
        'range' => 45.72,
        'verbal_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia di pece mescolata a '
            . 'una goccia di mercurio.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'half',
        'description' => 'Riempie una vasta sfera di oscurità magica '
            . 'impenetrabile, voci folli e risate che infliggono '
            . 'danni psichici alle creature al suo interno.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 18.288,
        ],
    ]),
];
