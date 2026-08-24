<?php

//Valori condivisi dagli incantesimi di 6° livello di Xanathar
$defaults = [
    'level' => 6,
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

//Restituisce i 12 incantesimi di 6° livello di Xanathar
return [
    $spell([
        'key' => 'druid_grove',
        'name' => 'Boschetto Druidico',
        'school_key' => 'abjuration',
        'page' => 151,
        'casting_time_value' => 10,
        'casting_time_type' => 'minute',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Vischio raccolto con un falcetto '
            . 'd’oro alla luce della luna piena.',
        'material_consumed' => true,
        'duration_type' => 'hour',
        'duration_value' => 24,
        'description' => 'Protegge una zona naturale con nebbia, '
            . 'sottobosco, alberi guardiani e altri effetti magici '
            . 'scelti dall’incantatore.',
        'notes' => 'Lanciato nella stessa area ogni giorno per un '
            . 'anno, dura finché non viene dissolto.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 27.432,
            'notes' => 'Il cubo può avere uno spigolo compreso tra '
                . '9,144 e 27,432 metri; edifici e altre strutture '
                . 'sono esclusi.',
        ],
    ]),

    $spell([
        'key' => 'create_homunculus',
        'name' => 'Creare Omuncolo',
        'school_key' => 'transmutation',
        'page' => 153,
        'casting_time_type' => 'hour',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Argilla, cenere e radice di '
            . 'mandragora, che vengono consumate, e un pugnale '
            . 'ingioiellato del valore di almeno 1.000 mo.',
        'material_consumed' => true,
        'material_cost' => 1000,
        'description' => 'Trasforma i materiali e il sangue '
            . 'dell’incantatore in un omuncolo fedele, collegando '
            . 'temporaneamente i loro punti ferita massimi.',
        'materials' => [
            [
                'key' => 'consumed_mixture',
                'name' => 'Miscela per l’omuncolo',
                'description' => 'Argilla, cenere e radice '
                    . 'di mandragora.',
                'consumed' => true,
                'focus_replaceable' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'jeweled_dagger',
                'name' => 'Pugnale ingioiellato',
                'description' => 'Un pugnale ingioiellato del valore '
                    . 'di almeno 1.000 mo.',
                'cost_amount' => 1000,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => false,
                'focus_replaceable' => false,
                'sort_order' => 2,
            ],
        ],
        'target' => [
            'target_type' => 'special',
            'notes' => 'Crea un solo omuncolo; il lancio fallisce se '
                . 'l’incantatore ne possiede già uno vivo.',
        ],
    ]),

    $spell([
        'key' => 'scatter',
        'name' => 'Disperdere',
        'school_key' => 'conjuration',
        'page' => 155,
        'range' => 9.144,
        'verbal_component' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Teletrasporta fino a cinque creature vicine '
            . 'in spazi liberi visibili situati entro 36,576 metri '
            . 'dall’incantatore.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 5,
            'requires_sight' => true,
            'notes' => 'Una creatura non consenziente può resistere '
                . 'con un tiro salvezza su Saggezza.',
        ],
    ]),

    $spell([
        'key' => 'soul_cage',
        'name' => 'Gabbia dell’Anima',
        'school_key' => 'necromancy',
        'page' => 158,
        'casting_time_type' => 'reaction',
        'casting_trigger' => 'Quando l’incantatore vede morire un '
            . 'umanoide entro 18,288 metri da sé.',
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una minuscola gabbia d’argento '
            . 'del valore di 100 mo.',
        'material_cost' => 100,
        'duration_type' => 'hour',
        'duration_value' => 8,
        'description' => 'Intrappola l’anima di un umanoide appena '
            . 'morto e consente di sfruttarla fino a sei volte per '
            . 'curarsi, interrogarla o ottenere altri benefici.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Il bersaglio è l’anima di un umanoide che '
                . 'l’incantatore vede morire entro gittata.',
        ],
    ]),

    $spell([
        'key' => 'primordial_ward',
        'name' => 'Interdizione Primordiale',
        'school_key' => 'abjuration',
        'page' => 159,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Conferisce resistenza ai principali danni '
            . 'elementali e permette di trasformare una resistenza '
            . 'in immunità per un breve periodo.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],
    ]),

    $spell([
        'key' => 'investiture_of_ice',
        'name' => 'Investitura del Ghiaccio',
        'school_key' => 'transmutation',
        'page' => 159,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Ricopre l’incantatore di ghiaccio, '
            . 'proteggendolo dal freddo e dal fuoco e permettendogli '
            . 'di emettere coni di vento gelido.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'L’azione concessa crea un cono di '
                . '4,572 metri che richiede un tiro salvezza '
                . 'su Costituzione.',
        ],
    ]),

    $spell([
        'key' => 'investiture_of_wind',
        'name' => 'Investitura del Vento',
        'school_key' => 'transmutation',
        'page' => 159,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Circonda l’incantatore di vento, gli '
            . 'conferisce volo, ostacola gli attacchi a distanza e '
            . 'permette di creare raffiche turbinanti.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'L’azione concessa crea un cubo con spigolo '
                . 'di 4,572 metri entro 18,288 metri.',
        ],
    ]),

    $spell([
        'key' => 'investiture_of_flame',
        'name' => 'Investitura della Fiamma',
        'school_key' => 'transmutation',
        'page' => 159,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Avvolge l’incantatore nelle fiamme, '
            . 'proteggendolo dal fuoco e dal freddo e permettendogli '
            . 'di sprigionare linee di fuoco.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'L’azione concessa crea una linea lunga '
                . '4,572 metri e larga 1,524 metri.',
        ],
    ]),

    $spell([
        'key' => 'investiture_of_stone',
        'name' => 'Investitura della Pietra',
        'school_key' => 'transmutation',
        'page' => 160,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Ricopre l’incantatore di pietra, '
            . 'proteggendolo dagli attacchi non magici e permettendo '
            . 'di attraversare terra e roccia.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'L’azione concessa scuote il terreno entro '
                . '4,572 metri dall’incantatore.',
        ],
    ]),

    $spell([
        'key' => 'bones_of_the_earth',
        'name' => 'Ossa della Terra',
        'school_key' => 'transmutation',
        'page' => 163,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Fa emergere dal terreno fino a sei colonne '
            . 'di pietra che possono sollevare, schiacciare o '
            . 'trattenere le creature.',
        'higher_levels' => 'Crea due colonne aggiuntive per ogni slot '
            . 'di livello superiore al 6°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'special',
            'requires_sight' => true,
            'notes' => 'Crea fino a sei colonne del diametro di '
                . '1,524 metri e alte fino a 9,144 metri, in punti '
                . 'del terreno visibili entro gittata.',
        ],
    ]),

    $spell([
        'key' => 'mental_prison',
        'name' => 'Prigione Mentale',
        'school_key' => 'illusion',
        'page' => 165,
        'range' => 18.288,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'INT',
        'save_success_damage' => 'full',
        'description' => 'Imprigiona una creatura in una minaccia '
            . 'illusoria che la isola e la trattiene, infliggendole '
            . 'ulteriori danni se tenta di attraversarla.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'tensers_transformation',
        'name' => 'Trasformazione di Tenser',
        'school_key' => 'transmutation',
        'page' => 169,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Alcuni peli di un toro.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Trasforma temporaneamente l’incantatore in '
            . 'un combattente marziale, conferendogli punti ferita '
            . 'temporanei, competenze e attacchi potenziati.',
        'notes' => 'Durante la trasformazione l’incantatore non può '
            . 'lanciare incantesimi e al termine deve superare un '
            . 'tiro salvezza su Costituzione.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],
    ]),
];
