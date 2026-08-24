<?php

//Valori condivisi dagli incantesimi di 1° livello di Xanathar
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

//Restituisce i 10 incantesimi di 1° livello di Xanathar
return [
    $spell([
        'key' => 'absorb_elements',
        'name' => 'Assorbire Elementi',
        'school_key' => 'abjuration',
        'page' => 151,
        'casting_time_type' => 'reaction',
        'casting_trigger' => 'Quando l’incantatore subisce danni da '
            . 'acido, freddo, fulmine, fuoco o tuono.',
        'range_type' => 'self',
        'somatic_component' => true,
        'duration_type' => 'round',
        'duration_value' => 1,
        'description' => 'Assorbe parte dell’energia elementale '
            . 'subita, conferendo resistenza e potenziando il '
            . 'successivo attacco in mischia.',
        'higher_levels' => 'Il danno extra aumenta di 1d6 per ogni '
            . 'slot di livello superiore al 1°.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],
    ]),

    $spell([
        'key' => 'catapult',
        'name' => 'Catapulta',
        'school_key' => 'transmutation',
        'page' => 152,
        'range' => 18.288,
        'somatic_component' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Scaglia un piccolo oggetto non trasportato '
            . 'lungo una linea, danneggiando l’oggetto e ciò contro '
            . 'cui impatta.',
        'higher_levels' => 'Il peso massimo aumenta di 2,5 kg e il '
            . 'danno di 1d8 per ogni slot oltre il 1°.',
        'target' => [
            'target_type' => 'object',
            'target_count' => 1,
            'can_target_objects' => true,
            'notes' => 'L’oggetto vola fino a 27,432 metri e può '
                . 'colpire una creatura o una superficie.',
        ],
    ]),

    $spell([
        'key' => 'ceremony',
        'name' => 'Cerimonia',
        'school_key' => 'abjuration',
        'page' => 152,
        'casting_time_type' => 'hour',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Polvere argentata del valore di '
            . '25 mo, che l’incantesimo consuma.',
        'material_consumed' => true,
        'material_cost' => 25,
        'ritual' => true,
        'description' => 'Celebra un rito religioso magico, come '
            . 'benedire acqua, compiere un’espiazione, una dedizione, '
            . 'un matrimonio o un rito funebre.',
        'target' => [
            'target_type' => 'special',
            'can_target_self' => true,
            'can_target_objects' => true,
            'notes' => 'Il bersaglio varia in base al rito scelto e '
                . 'deve restare entro 3,048 metri durante il lancio.',
        ],
    ]),

    $spell([
        'key' => 'zephyr_strike',
        'name' => 'Colpo dello Zefiro',
        'school_key' => 'transmutation',
        'page' => 153,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Impedisce al movimento dell’incantatore di '
            . 'provocare attacchi di opportunità e potenzia una volta '
            . 'un attacco con un’arma e la sua velocità.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],
    ]),

    $spell([
        'key' => 'ice_knife',
        'name' => 'Coltello di Ghiaccio',
        'school_key' => 'conjuration',
        'page' => 153,
        'range' => 18.288,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia d’acqua o un frammento '
            . 'di ghiaccio.',
        'attack_type' => 'ranged',
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Scaglia un frammento di ghiaccio contro una '
            . 'creatura; dopo l’attacco il frammento esplode e può '
            . 'danneggiare le creature vicine.',
        'higher_levels' => 'Il danno da freddo dell’esplosione aumenta '
            . 'di 1d6 per ogni slot oltre il 1°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 1.524,
            'notes' => 'La sfera è centrata sulla creatura bersaglio '
                . 'dell’attacco con incantesimo a distanza.',
        ],
    ]),

    $spell([
        'key' => 'chaos_bolt',
        'name' => 'Dardo di Caos',
        'school_key' => 'evocation',
        'page' => 155,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'attack_type' => 'ranged',
        'description' => 'Scaglia energia caotica contro una creatura; '
            . 'il tipo di danno è casuale e l’energia può balzare '
            . 'verso un altro bersaglio.',
        'higher_levels' => 'Il danno aumenta di 1d6 per ogni slot di '
            . 'livello superiore al 1°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'notes' => 'Può balzare verso creature differenti entro '
                . '9,144 metri dal bersaglio precedente.',
        ],
    ]),

    $spell([
        'key' => 'cause_fear',
        'name' => 'Incuti Paura',
        'school_key' => 'necromancy',
        'page' => 160,
        'range' => 18.288,
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Costringe una creatura visibile a confrontarsi '
            . 'con la propria mortalità, potendo renderla spaventata.',
        'higher_levels' => 'Può bersagliare una creatura aggiuntiva per '
            . 'ogni slot di livello superiore al 1°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Costrutti e non morti sono immuni.',
        ],
    ]),

    $spell([
        'key' => 'beast_bond',
        'name' => 'Legame con le Bestie',
        'school_key' => 'divination',
        'page' => 161,
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un ciuffo di pelliccia avvolto in '
            . 'un pezzo di stoffa.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Stabilisce un legame telepatico con una '
            . 'bestia amichevole o affascinata e ne migliora gli '
            . 'attacchi contro i nemici vicini all’incantatore.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'La bestia deve avere Intelligenza inferiore a '
                . '4 e il legame richiede linea di vista reciproca.',
        ],
    ]),

    $spell([
        'key' => 'earth_tremor',
        'name' => 'Scossa Tellurica',
        'school_key' => 'evocation',
        'page' => 167,
        'range' => 3.048,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Scuote il terreno attorno all’incantatore, '
            . 'danneggiando e facendo cadere le creature e rendendo '
            . 'difficile il terreno smosso.',
        'higher_levels' => 'Il danno aumenta di 1d6 per ogni slot di '
            . 'livello superiore al 1°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 3.048,
            'can_target_objects' => true,
            'notes' => 'L’area esclude l’incantatore e influenza il '
                . 'terreno di pietra o terriccio smosso.',
        ],
    ]),

    $spell([
        'key' => 'snare',
        'name' => 'Trabocchetto',
        'school_key' => 'abjuration',
        'page' => 170,
        'casting_time_type' => 'minute',
        'range_type' => 'touch',
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => '7,5 metri di corda, che '
            . 'l’incantesimo consuma.',
        'material_consumed' => true,
        'duration_type' => 'hour',
        'duration_value' => 8,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Trasforma una corda in una trappola magica '
            . 'quasi invisibile che solleva e trattiene una creatura '
            . 'che entra nella sua area.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'circle',
            'area_size_meters' => 1.524,
            'can_target_objects' => true,
            'notes' => 'Il cerchio viene tracciato sul terreno o sul '
                . 'pavimento e si attiva una sola volta.',
        ],
    ]),
];
