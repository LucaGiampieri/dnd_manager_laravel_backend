<?php

//Valori condivisi dai trucchetti di Xanathar
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

//Restituisce i 12 trucchetti della Guida di Xanathar
return [
    $spell([
        'key' => 'control_flames',
        'name' => 'Controllare Fiamme',
        'school_key' => 'transmutation',
        'page' => 153,
        'range' => 18.288,
        'somatic_component' => true,
        'duration_type' => 'special',
        'description' => 'Permette di espandere, estinguere o '
            . 'modificare una fiamma non magica visibile contenuta '
            . 'in un piccolo cubo.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Influenza una fiamma non magica; alcuni '
                . 'effetti sono istantanei e altri durano 1 ora.',
        ],
    ]),

    $spell([
        'key' => 'create_bonfire',
        'name' => 'Creare Falò',
        'school_key' => 'conjuration',
        'page' => 154,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Crea su un punto del terreno un falò '
            . 'magico che danneggia le creature nel suo spazio e '
            . 'può incendiare oggetti infiammabili.',
        'higher_levels' => 'Il danno aumenta a 2d8 al 5° livello, '
            . '3d8 all’11° e 4d8 al 17°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'Il falò occupa un cubo appoggiato sul terreno.',
        ],
    ]),

    $spell([
        'key' => 'primal_savagery',
        'name' => 'Ferocia Primordiale',
        'school_key' => 'transmutation',
        'page' => 158,
        'range_type' => 'self',
        'somatic_component' => true,
        'attack_type' => 'melee',
        'description' => 'Sviluppa temporaneamente denti o unghie '
            . 'magiche e consente un attacco in mischia che infligge '
            . 'danni da acido.',
        'higher_levels' => 'Il danno aumenta a 2d10 al 5° livello, '
            . '3d10 all’11° e 4d10 al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'La creatura deve trovarsi entro 1,524 metri '
                . 'dall’incantatore.',
        ],
    ]),

    $spell([
        'key' => 'gust',
        'name' => 'Folata',
        'school_key' => 'transmutation',
        'page' => 158,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Genera una folata capace di spingere una '
            . 'creatura, muovere un piccolo oggetto oppure produrre '
            . 'un innocuo effetto d’aria.',
        'target' => [
            'target_type' => 'special',
            'target_count' => 1,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'Può influenzare una creatura Media o più '
                . 'piccola, un oggetto leggero o un punto visibile.',
        ],
    ]),

    $spell([
        'key' => 'infestation',
        'name' => 'Infestazione',
        'school_key' => 'conjuration',
        'page' => 160,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una pulce viva.',
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Circonda brevemente una creatura visibile '
            . 'con parassiti magici, infliggendo danni da veleno e '
            . 'potendo provocare un piccolo movimento casuale.',
        'higher_levels' => 'Il danno aumenta a 2d6 al 5° livello, '
            . '3d6 all’11° e 4d6 al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'shape_water',
        'name' => 'Modellare Acqua',
        'school_key' => 'transmutation',
        'page' => 162,
        'range' => 9.144,
        'somatic_component' => true,
        'duration_type' => 'special',
        'description' => 'Muove, modella, colora, rende opaca o '
            . 'congela una piccola quantità d’acqua visibile.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Influenza l’acqua contenuta nel cubo; alcuni '
                . 'effetti sono istantanei e altri durano 1 ora.',
        ],
    ]),

    $spell([
        'key' => 'mold_earth',
        'name' => 'Modellare Terra',
        'school_key' => 'transmutation',
        'page' => 162,
        'range' => 9.144,
        'somatic_component' => true,
        'duration_type' => 'special',
        'description' => 'Scava terra smossa, traccia forme su terra '
            . 'o pietra oppure modifica temporaneamente la difficoltà '
            . 'del terreno.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'Influenza terra o pietra nel cubo; alcuni '
                . 'effetti sono istantanei e altri durano 1 ora.',
        ],
    ]),

    $spell([
        'key' => 'frostbite',
        'name' => 'Morsa del Gelo',
        'school_key' => 'evocation',
        'page' => 162,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Ricopre una creatura visibile di gelo, '
            . 'infliggendo danni da freddo e ostacolando il suo '
            . 'successivo attacco con un’arma.',
        'higher_levels' => 'Il danno aumenta a 2d6 al 5° livello, '
            . '3d6 all’11° e 4d6 al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'word_of_radiance',
        'name' => 'Parola Radiosa',
        'school_key' => 'evocation',
        'page' => 164,
        'range' => 1.524,
        'verbal_component' => true,
        'material_component' => true,
        'material_description' => 'Un simbolo sacro.',
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Emette un bagliore divino che infligge '
            . 'danni radiosi alle creature visibili scelte '
            . 'dall’incantatore nelle vicinanze.',
        'higher_levels' => 'Il danno aumenta a 2d6 al 5° livello, '
            . '3d6 all’11° e 4d6 al 17°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Colpisce soltanto le creature scelte '
                . 'dall’incantatore entro la gittata.',
        ],
    ]),

    $spell([
        'key' => 'magic_stone',
        'name' => 'Pietra Magica',
        'school_key' => 'transmutation',
        'page' => 165,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'description' => 'Infunde magia in un massimo di tre sassolini '
            . 'che possono essere scagliati a mano o con una fionda.',
        'target' => [
            'target_type' => 'objects',
            'target_count' => 3,
            'can_target_objects' => true,
            'notes' => 'Bersaglia da uno a tre sassolini toccati.',
        ],
    ]),

    $spell([
        'key' => 'toll_the_dead',
        'name' => 'Rintocco dei Morti',
        'school_key' => 'necromancy',
        'page' => 166,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Fa risuonare una campana funebre attorno '
            . 'a una creatura visibile, infliggendo più danni '
            . 'necrotici se è già ferita.',
        'higher_levels' => 'Aumenta di un dado al 5° livello, di due '
            . 'all’11° e di tre al 17°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'thunderclap',
        'name' => 'Rombo di Tuono',
        'school_key' => 'evocation',
        'page' => 166,
        'range' => 1.524,
        'somatic_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Genera un’esplosione tonante udibile a '
            . 'distanza che danneggia le creature vicine, escluso '
            . 'l’incantatore.',
        'higher_levels' => 'Il danno aumenta a 2d6 al 5° livello, '
            . '3d6 all’11° e 4d6 al 17°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 1.524,
            'notes' => 'L’emanazione esclude sempre l’incantatore.',
        ],
    ]),
];
