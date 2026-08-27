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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Drago Illusorio',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Crea un enorme drago d’ombra che spaventa i nemici e può emettere coni di energia dannosa mentre viene spostato dall’incantatore.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 1,
                        'duration_unit' => 'minute',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_acid',
                        'damage_type' => 'Acido',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 7,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio per il soffio. TS Intelligenza riuscito: metà; chi riconosce l’illusione ha vantaggio al tiro. Tipo: Acido.',
                    ],
                    [
                        'key' => 'damage_cold',
                        'damage_type' => 'Freddo',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 7,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio per il soffio. TS Intelligenza riuscito: metà; chi riconosce l’illusione ha vantaggio al tiro. Tipo: Freddo.',
                    ],
                    [
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => false,
                        'sort_order' => 3,
                        'dice_count' => 7,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio per il soffio. TS Intelligenza riuscito: metà; chi riconosce l’illusione ha vantaggio al tiro. Tipo: Fuoco.',
                    ],
                    [
                        'key' => 'damage_lightning',
                        'damage_type' => 'Fulmine',
                        'is_primary' => false,
                        'sort_order' => 4,
                        'dice_count' => 7,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio per il soffio. TS Intelligenza riuscito: metà; chi riconosce l’illusione ha vantaggio al tiro. Tipo: Fulmine.',
                    ],
                    [
                        'key' => 'damage_necrotic',
                        'damage_type' => 'Necrotico',
                        'is_primary' => false,
                        'sort_order' => 5,
                        'dice_count' => 7,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio per il soffio. TS Intelligenza riuscito: metà; chi riconosce l’illusione ha vantaggio al tiro. Tipo: Necrotico.',
                    ],
                    [
                        'key' => 'damage_poison',
                        'damage_type' => 'Veleno',
                        'is_primary' => false,
                        'sort_order' => 6,
                        'dice_count' => 7,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Solo il tipo scelto al lancio per il soffio. TS Intelligenza riuscito: metà; chi riconosce l’illusione ha vantaggio al tiro. Tipo: Veleno.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Fortezza Possente',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Fa emergere dal terreno una fortezza in pietra completa di torrette, mura, rocca, arredi, cibo e servitori invisibili.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Orrido Avvizzimento di Abi-Dalzim',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Estrae l’umidità dalle creature in un grande cubo, infliggendo ingenti danni necrotici e facendo avvizzire i vegetali non magici.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_necrotic',
                        'damage_type' => 'Necrotico',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 12,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Oscurità della Follia',
                'application_type' => 'on_start_turn',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Riempie una vasta sfera di oscurità magica impenetrabile, voci folli e risate che infliggono danni psichici alle creature al suo interno.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 10,
                        'duration_unit' => 'minute',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_psychic',
                        'damage_type' => 'Psichico',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 8,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Una creatura che inizia il turno nell’area effettua un TS Saggezza, metà del danno se riesce.',
            ],
        ],
    ]),
];
