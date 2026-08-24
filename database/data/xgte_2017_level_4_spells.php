<?php

//Valori condivisi dagli incantesimi di 4° livello di Xanathar
$defaults = [
    'level' => 4,
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

//Restituisce i 10 incantesimi di 4° livello di Xanathar
return [
    $spell([
        'key' => 'elemental_bane',
        'name' => 'Anatema Elementale',
        'school_key' => 'transmutation',
        'page' => 151,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Priva una creatura della resistenza a un '
            . 'tipo di danno elementale scelto e le infligge danni '
            . 'extra la prima volta che lo subisce in ogni turno.',
        'higher_levels' => 'Bersaglia una creatura aggiuntiva per ogni '
            . 'slot di livello superiore al 4°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'charm_monster',
        'name' => 'Charme sui Mostri',
        'school_key' => 'enchantment',
        'page' => 152,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Tenta di affascinare una creatura visibile, '
            . 'che considera l’incantatore amichevole finché non '
            . 'viene danneggiata o l’effetto termina.',
        'higher_levels' => 'Bersaglia una creatura aggiuntiva per ogni '
            . 'slot di livello superiore al 4°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'summon_greater_demon',
        'name' => 'Evoca Demone Maggiore',
        'school_key' => 'conjuration',
        'page' => 157,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una fiala di sangue di un umanoide '
            . 'ucciso nelle ultime 24 ore; il sangue viene consumato '
            . 'soltanto se usato per tracciare il cerchio protettivo.',
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Evoca un demone dell’Abisso e consente '
            . 'all’incantatore di impartirgli ordini finché la '
            . 'creatura non riesce a spezzare il controllo.',
        'higher_levels' => 'Il grado di sfida massimo aumenta di 1 per '
            . 'ogni slot di livello superiore al 4°.',
        'notes' => 'Il componente viene consumato soltanto quando '
            . 'viene usato per creare il cerchio protettivo.',
        'target' => [
            'target_type' => 'special',
            'requires_sight' => true,
            'notes' => 'Il demone compare in uno spazio libero visibile '
                . 'entro gittata e può effettuare tiri salvezza su '
                . 'Carisma per liberarsi dal controllo.',
        ],
    ]),

    $spell([
        'key' => 'sickening_radiance',
        'name' => 'Fulgore Nauseante',
        'school_key' => 'evocation',
        'page' => 158,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Riempie una vasta area di luce verdastra che '
            . 'può infliggere danni radiosi, causare indebolimento e '
            . 'impedire di beneficiare dell’invisibilità.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 9.144,
        ],
    ]),

    $spell([
        'key' => 'guardian_of_nature',
        'name' => 'Guardiano della Natura',
        'school_key' => 'transmutation',
        'page' => 159,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Trasforma l’incantatore in una Bestia '
            . 'Primordiale o in un Grande Albero, conferendo benefici '
            . 'differenti a movimento, difese e attacchi.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
        ],
    ]),

    $spell([
        'key' => 'shadow_of_moil',
        'name' => 'Ombra di Moil',
        'school_key' => 'necromancy',
        'page' => 163,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Il globo oculare di un non morto '
            . 'racchiuso in una gemma del valore di almeno 150 mo.',
        'material_cost' => 150,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Avvolge l’incantatore in ombre infuocate che '
            . 'lo oscurano, attenuano la luce, resistono al radioso e '
            . 'colpiscono chi lo attacca da vicino.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'Le ombre influenzano anche la luce entro '
                . '3,048 metri dall’incantatore.',
        ],
    ]),

    $spell([
        'key' => 'watery_sphere',
        'name' => 'Sfera Acquea',
        'school_key' => 'conjuration',
        'page' => 168,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia d’acqua.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Evoca una sfera d’acqua mobile che può '
            . 'inghiottire e trattenere creature, trascinandole con '
            . 'sé quando viene spostata.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Può trattenere fino a quattro creature Medie '
                . 'o più piccole oppure una creatura Grande.',
        ],
    ]),

    $spell([
        'key' => 'vitriolic_sphere',
        'name' => 'Sfera al Vetriolo',
        'school_key' => 'evocation',
        'page' => 168,
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia di bile di una lumaca '
            . 'gigante.',
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Scaglia una sfera di acido che esplode in '
            . 'un’ampia area e continua a danneggiare nel turno '
            . 'successivo chi fallisce il tiro salvezza.',
        'higher_levels' => 'Il danno iniziale aumenta di 2d4 per ogni '
            . 'slot di livello superiore al 4°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 6.096,
        ],
    ]),

    $spell([
        'key' => 'storm_sphere',
        'name' => 'Sfera della Tempesta',
        'school_key' => 'evocation',
        'page' => 168,
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'attack_type' => 'ranged',
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Crea una sfera di aria turbinante che '
            . 'danneggia e ostacola le creature e dalla quale '
            . 'l’incantatore può scagliare fulmini.',
        'higher_levels' => 'I danni del vento e del fulmine aumentano '
            . 'di 1d6 per ogni slot di livello superiore al 4°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 6.096,
            'notes' => 'Il fulmine può bersagliare una creatura entro '
                . '18,288 metri dal centro della sfera.',
        ],
    ]),

    $spell([
        'key' => 'find_greater_steed',
        'name' => 'Trova Cavalcatura Superiore',
        'school_key' => 'conjuration',
        'page' => 171,
        'casting_time_value' => 10,
        'casting_time_type' => 'minute',
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'description' => 'Evoca uno spirito fedele che assume la '
            . 'forma di una potente cavalcatura e rimane legato '
            . 'all’incantatore anche dopo essere scomparso.',
        'target' => [
            'target_type' => 'special',
            'notes' => 'La cavalcatura appare in uno spazio libero '
                . 'entro gittata e può assumere una delle forme '
                . 'previste dall’incantesimo.',
        ],
    ]),
];
