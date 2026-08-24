<?php

//Valori condivisi dagli incantesimi di 5° livello di Xanathar
$defaults = [
    'level' => 5,
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

//Restituisce i 16 incantesimi di 5° livello di Xanathar
return [
    $spell([
        'key' => 'skill_empowerment',
        'name' => 'Abilità Potenziata',
        'school_key' => 'transmutation',
        'page' => 150,
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Conferisce a una creatura consenziente '
            . 'maestria in un’abilità in cui è già competente, '
            . 'raddoppiandone il bonus di competenza.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
        ],
    ]),

    $spell([
        'key' => 'dawn',
        'name' => 'Alba',
        'school_key' => 'evocation',
        'page' => 150,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un pendente che raffigura un sole '
            . 'splendente del valore di almeno 100 mo.',
        'material_cost' => 100,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Crea un cilindro mobile di luce solare '
            . 'intensa che infligge danni radiosi alle creature '
            . 'presenti al suo interno.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cylinder',
            'area_size_meters' => 9.144,
            'area_secondary_size_meters' => 12.192,
            'notes' => 'La misura principale rappresenta il raggio '
                . 'e la seconda misura rappresenta l’altezza.',
        ],
    ]),

    $spell([
        'key' => 'holy_weapon',
        'name' => 'Arma Sacra',
        'school_key' => 'evocation',
        'page' => 150,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Infondendo energia sacra in un’arma, le '
            . 'conferisce danni radiosi aggiuntivi e permette di '
            . 'liberare un’esplosione accecante.',
        'target' => [
            'target_type' => 'object',
            'target_count' => 1,
            'can_target_objects' => true,
            'notes' => 'L’esplosione finale influenza le creature '
                . 'scelte entro 9,144 metri dall’arma.',
        ],
    ]),

    $spell([
        'key' => 'wrath_of_nature',
        'name' => 'Collera della Natura',
        'school_key' => 'evocation',
        'page' => 152,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Anima erba, alberi, radici e rocce in '
            . 'un’ampia zona naturale per ostacolare, trattenere e '
            . 'ferire i nemici dell’incantatore.',
        'notes' => 'Gli effetti dell’area possono richiedere tiri '
            . 'salvezza su Destrezza o Forza.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 18.288,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'steel_wind_strike',
        'name' => 'Colpo del Vento d’Acciaio',
        'school_key' => 'conjuration',
        'page' => 152,
        'range' => 9.144,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un’arma da mischia del valore di '
            . 'almeno 1 ma.',
        'material_cost' => 0.1,
        'attack_type' => 'melee',
        'description' => 'Scompare come il vento, effettua un attacco '
            . 'con incantesimo in mischia contro un massimo di cinque '
            . 'creature e può ricomparire vicino a una di esse.',
        'materials' => [
            [
                'key' => 'melee_weapon',
                'name' => 'Arma da mischia',
                'description' => 'Un’arma da mischia del valore di '
                    . 'almeno 1 ma.',
                'cost_amount' => 1,
                'currency_code' => 'ma',
                'cost_is_minimum' => true,
                'consumed' => false,
                'focus_replaceable' => false,
            ],
        ],
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 5,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'control_winds',
        'name' => 'Controllare Venti',
        'school_key' => 'transmutation',
        'page' => 153,
        'range' => 91.44,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Controlla l’aria in un grande cubo, creando '
            . 'folate orizzontali, correnti discendenti o correnti '
            . 'ascendenti modificabili durante la durata.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 30.48,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'danse_macabre',
        'name' => 'Danza Macabra',
        'school_key' => 'necromancy',
        'page' => 153,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Anima fino a cinque cadaveri come scheletri '
            . 'o zombi temporanei e permette di comandarli '
            . 'mentalmente con un’azione bonus.',
        'higher_levels' => 'Anima due cadaveri aggiuntivi per ogni '
            . 'slot di livello superiore al 5°.',
        'target' => [
            'target_type' => 'objects',
            'target_count' => 5,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'I bersagli sono cadaveri di taglia Media o '
                . 'Piccola che diventano temporaneamente non morti.',
        ],
    ]),

    $spell([
        'key' => 'enervation',
        'name' => 'Debilitazione',
        'school_key' => 'necromancy',
        'page' => 155,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Un tentacolo oscuro prosciuga una creatura, '
            . 'infliggendole ripetutamente danni necrotici e curando '
            . 'l’incantatore di parte dei danni causati.',
        'higher_levels' => 'I danni aumentano di 1d8 per ogni slot di '
            . 'livello superiore al 5°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'negative_energy_flood',
        'name' => 'Flusso di Energia Negativa',
        'school_key' => 'necromancy',
        'page' => 157,
        'range' => 18.288,
        'verbal_component' => true,
        'material_component' => true,
        'material_description' => 'Un osso spezzato e un quadretto '
            . 'di seta nera.',
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Invia energia negativa contro una creatura: '
            . 'danneggia i viventi e conferisce punti ferita '
            . 'temporanei ai non morti.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'immolation',
        'name' => 'Immolazione',
        'school_key' => 'evocation',
        'page' => 158,
        'range' => 27.432,
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Avvolge una creatura visibile nelle fiamme, '
            . 'infliggendo danni iniziali e ulteriori danni nei turni '
            . 'successivi finché il bersaglio non si salva.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'maelstrom',
        'name' => 'Maelstrom',
        'school_key' => 'evocation',
        'page' => 160,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un foglio di carta o una foglia '
            . 'a forma di imbuto.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'half',
        'description' => 'Crea una massa d’acqua turbinante che rende '
            . 'il terreno difficile, danneggia le creature e le '
            . 'trascina verso il proprio centro.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cylinder',
            'area_size_meters' => 9.144,
            'area_secondary_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'La misura principale rappresenta il raggio '
                . 'e la seconda misura rappresenta la profondità.',
        ],
    ]),

    $spell([
        'key' => 'wall_of_light',
        'name' => 'Muro di Luce',
        'school_key' => 'evocation',
        'page' => 162,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Uno specchietto.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'attack_type' => 'ranged',
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Crea un muro luminoso che danneggia e può '
            . 'accecare le creature; l’incantatore può consumarne '
            . 'porzioni per scagliare raggi radiosi.',
        'higher_levels' => 'Il danno aumenta di 1d8 per ogni slot di '
            . 'livello superiore al 5°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'wall',
            'area_size_meters' => 18.288,
            'area_secondary_size_meters' => 3.048,
            'notes' => 'Il muro può essere spesso fino a 1,524 metri '
                . 'e orientato orizzontalmente, verticalmente o '
                . 'diagonalmente.',
        ],
    ]),

    $spell([
        'key' => 'far_step',
        'name' => 'Passo Remoto',
        'school_key' => 'conjuration',
        'page' => 164,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Permette all’incantatore di teletrasportarsi '
            . 'fino a 18,288 metri al lancio e di ripetere il '
            . 'teletrasporto con un’azione bonus a ogni turno.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'infernal_calling',
        'name' => 'Richiamo Infernale',
        'school_key' => 'conjuration',
        'page' => 165,
        'casting_time_type' => 'minute',
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un rubino del valore di almeno '
            . '999 mo.',
        'material_cost' => 999,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Evoca un diavolo dai Nove Inferi che deve '
            . 'essere controllato tramite comandi e prove di Carisma, '
            . 'salvo l’uso del suo talismano.',
        'higher_levels' => 'Il grado di sfida massimo del diavolo '
            . 'aumenta di 1 per ogni slot di livello superiore al 5°.',
        'target' => [
            'target_type' => 'special',
            'requires_sight' => true,
            'notes' => 'Il diavolo compare in uno spazio libero '
                . 'visibile entro gittata.',
        ],
    ]),

    $spell([
        'key' => 'synaptic_static',
        'name' => 'Scossa Sinaptica',
        'school_key' => 'enchantment',
        'page' => 165,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'INT',
        'save_success_damage' => 'half',
        'description' => 'Genera un’esplosione psichica che danneggia '
            . 'le creature e può confonderne i pensieri, penalizzando '
            . 'attacchi, prove e concentrazione.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 6.096,
        ],
    ]),

    $spell([
        'key' => 'transmute_rock',
        'name' => 'Trasmutare Roccia',
        'school_key' => 'transmutation',
        'page' => 169,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Argilla e acqua oppure sabbia, '
            . 'calce e acqua.',
        'duration_type' => 'until_dispelled',
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Trasforma roccia non magica in fango oppure '
            . 'fango e sabbie mobili non magici in roccia, modificando '
            . 'il terreno e intrappolando le creature.',
        'notes' => 'Le due trasformazioni possono richiedere tiri '
            . 'salvezza su Forza o Destrezza.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 12.192,
            'requires_sight' => true,
        ],
    ]),
];
