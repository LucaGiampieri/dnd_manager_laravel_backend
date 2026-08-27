<?php

//Valori condivisi dagli incantesimi di 7° livello di Tasha
$defaults = [
    'level' => 7,
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

//Restituisce l'unico incantesimo di 7° livello introdotto da Tasha
return [
    //Sogno del Velo Celeste
    $spell([
        'key' => 'dream_of_the_blue_veil',
        'name' => 'Sogno del Velo Celeste',
        'school_key' => 'conjuration',
        'page' => 115,
        'casting_time_value' => 10,
        'casting_time_type' => 'minute',
        'range' => 6.096,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un oggetto magico o una creatura '
            . 'consenziente proveniente dal mondo di destinazione.',
        'duration_type' => 'hour',
        'duration_value' => 6,
        'description' => 'L’incantatore e fino a otto creature '
            . 'consenzienti cadono privi di sensi e ricevono visioni '
            . 'di un altro mondo del Piano Materiale. Se l’effetto '
            . 'dura per tutte le 6 ore, vengono trasportati nel '
            . 'mondo osservato.',
        'notes' => 'Per raggiungere il mondo scelto serve un oggetto '
            . 'magico proveniente da quel mondo oppure una delle '
            . 'creature influenzate deve esservi nata.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 9,
            'can_target_self' => true,
            'notes' => 'L’incantatore e fino a otto creature '
                . 'consenzienti entro 6,096 metri.',
        ],
        'effects' => [
            //Sonno e visione condivisa che precedono il viaggio
            [
                'key' => 'shared_world_dream',
                'name' => 'Sogno di un Altro Mondo',
                'application_type' => 'automatic',
                'target_scope' => 'targets',
                'description' => 'Le creature influenzate cadono '
                    . 'prive di sensi e vedono il mondo di '
                    . 'destinazione per un massimo di 6 ore.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'six_hour_vision',
                        'duration_type' => 'fixed',
                        'duration_value' => 6,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
            ],

            //Trasferimento planare che avviene al termine del sogno
            [
                'key' => 'world_transit',
                'name' => 'Viaggio nel Mondo della Visione',
                'application_type' => 'special',
                'target_scope' => 'targets',
                'condition' => 'Il sogno deve durare per tutte le '
                    . '6 ore senza essere interrotto.',
                'description' => 'Le creature vengono trasportate '
                    . 'mentalmente e fisicamente in un luogo sicuro '
                    . 'entro 1,5 km dal luogo collegato all’oggetto '
                    . 'magico o dalla nascita della creatura usata '
                    . 'come requisito.',
                'sort_order' => 2,
            ],

            //Regola di interruzione causata dai danni
            [
                'key' => 'damage_interruption',
                'name' => 'Interruzione per Danno',
                'application_type' => 'on_damage',
                'target_scope' => 'special',
                'description' => 'Se una creatura influenzata subisce '
                    . 'danni, l’effetto termina soltanto per lei. Se '
                    . 'li subisce l’incantatore, termina per tutti e '
                    . 'nessuno viene trasportato.',
                'sort_order' => 3,
            ],
        ],
    ]),
];
