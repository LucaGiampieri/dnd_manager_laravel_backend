<?php

//Valori condivisi dagli incantesimi di 2° livello di Xanathar
$defaults = [
    'level' => 2,
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

//Restituisce i 12 incantesimi di 2° livello di Xanathar
return [
    $spell([
        'key' => 'mind_spike',
        'name' => 'Aculeo Mentale',
        'school_key' => 'divination',
        'page' => 151,
        'range' => 18.288,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'half',
        'description' => 'Penetra nella mente di una creatura '
            . 'visibile, infligge danni psichici e, se il tiro '
            . 'fallisce, permette di seguirne la posizione.',
        'higher_levels' => 'Il danno aumenta di 1d8 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'dust_devil',
        'name' => 'Diavoletto di Polvere',
        'school_key' => 'conjuration',
        'page' => 156,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un pizzico di polvere.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'half',
        'description' => 'Evoca un piccolo vortice mobile che '
            . 'danneggia e spinge le creature vicine e può sollevare '
            . 'una nube di detriti.',
        'higher_levels' => 'Il danno aumenta di 1d8 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Il vortice influenza le creature che terminano '
                . 'il turno entro 1,524 metri da esso.',
        ],
    ]),

    $spell([
        'key' => 'shadow_blade',
        'name' => 'Lama d’Ombra',
        'school_key' => 'illusion',
        'page' => 161,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Crea nella mano dell’incantatore una spada '
            . 'magica di ombra solida che infligge danni psichici ed '
            . 'è più efficace nella luce fioca o nell’oscurità.',
        'higher_levels' => 'Il danno diventa 3d8 con slot di 3° o 4°, '
            . '4d8 con slot di 5° o 6° e 5d8 dal 7°.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'La lama può essere lanciata e fatta ricomparire '
                . 'nella mano con un’azione bonus.',
        ],
    ]),

    $spell([
        'key' => 'pyrotechnics',
        'name' => 'Pirotecnica',
        'school_key' => 'transmutation',
        'page' => 165,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Estingue una fiamma non magica visibile per '
            . 'generare fuochi d’artificio accecanti oppure una '
            . 'nube di fumo oscurante.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'special',
            'requires_sight' => true,
            'notes' => 'Bersaglia una fiamma in un cubo di 1,524 metri; '
                . 'crea un lampo entro 3,048 metri oppure fumo in una '
                . 'sfera del raggio di 6,096 metri.',
        ],
    ]),

    $spell([
        'key' => 'snillocs_snowball_swarm',
        'name' => 'Sciame di Palle di Neve di Snilloc',
        'school_key' => 'evocation',
        'page' => 166,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un pezzo di ghiaccio o una '
            . 'scheggia di pietra bianca.',
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Fa esplodere una raffica di palle di neve '
            . 'magiche in un punto entro gittata, infliggendo danni '
            . 'da freddo alle creature vicine.',
        'higher_levels' => 'Il danno aumenta di 1d6 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 1.524,
        ],
    ]),

    $spell([
        'key' => 'skywrite',
        'name' => 'Scritta Celeste',
        'school_key' => 'transmutation',
        'page' => 167,
        'range_type' => 'sight',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'ritual' => true,
        'description' => 'Forma nel cielo fino a dieci parole composte '
            . 'da nuvole, visibili finché l’incantesimo permane o un '
            . 'vento forte le disperde.',
        'target' => [
            'target_type' => 'special',
            'requires_sight' => true,
            'notes' => 'Bersaglia una parte visibile del cielo.',
        ],
    ]),

    $spell([
        'key' => 'dragons_breath',
        'name' => 'Soffio del Drago',
        'school_key' => 'transmutation',
        'page' => 169,
        'casting_time_type' => 'bonus_action',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un peperoncino.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Conferisce a una creatura consenziente la '
            . 'capacità di esalare ripetutamente un cono di energia '
            . 'elementale scelto dall’incantatore.',
        'higher_levels' => 'Il danno aumenta di 1d6 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'La creatura toccata può produrre un cono di '
                . '4,572 metri usando la propria azione.',
        ],
    ]),

    $spell([
        'key' => 'healing_spirit',
        'name' => 'Spirito Guaritore',
        'school_key' => 'conjuration',
        'page' => 169,
        'casting_time_type' => 'bonus_action',
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Evoca uno spirito naturale mobile che cura '
            . 'le creature quando entrano nel suo spazio o vi '
            . 'iniziano il turno.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'Lo spirito non può curare costrutti o non morti '
                . 'e può essere mosso di 9,144 metri.',
        ],
    ]),

    $spell([
        'key' => 'maximilians_earthen_grasp',
        'name' => 'Stretta della Terra di Maximilian',
        'school_key' => 'transmutation',
        'page' => 169,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una mano in miniatura modellata '
            . 'in argilla.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Fa emergere dal terreno una mano di terra '
            . 'compatta che afferra, trattiene e può stritolare una '
            . 'creatura vicina.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'square',
            'area_size_meters' => 1.524,
            'requires_sight' => true,
            'notes' => 'La mano occupa uno spazio sul terreno e '
                . 'bersaglia una creatura entro 1,524 metri da essa; '
                . 'i successivi tiri contro lo stritolamento dimezzano '
                . 'il danno quando superati.',
        ],
    ]),

    $spell([
        'key' => 'aganazzars_scorcher',
        'name' => 'Vampa di Aganazzar',
        'school_key' => 'evocation',
        'page' => 172,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una scaglia di drago rosso.',
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Proietta dall’incantatore una linea di '
            . 'fiamme rombanti che infligge danni da fuoco alle '
            . 'creature attraversate.',
        'higher_levels' => 'Il danno aumenta di 1d8 per ogni slot di '
            . 'livello superiore al 2°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'line',
            'area_size_meters' => 9.144,
            'area_secondary_size_meters' => 1.524,
            'notes' => 'La linea ha origine dall’incantatore.',
        ],
    ]),

    $spell([
        'key' => 'warding_wind',
        'name' => 'Vento di Interdizione',
        'school_key' => 'evocation',
        'page' => 172,
        'range_type' => 'self',
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Genera un forte vento attorno '
            . 'all’incantatore che assorda, estingue piccole fiamme, '
            . 'ostacola il movimento e gli attacchi a distanza.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 3.048,
            'can_target_self' => true,
            'notes' => 'L’emanazione resta centrata sull’incantatore '
                . 'e si muove insieme a lui.',
        ],
    ]),

    $spell([
        'key' => 'earthbind',
        'name' => 'Vincolo della Terra',
        'school_key' => 'transmutation',
        'page' => 172,
        'range' => 91.44,
        'verbal_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'none',
        'description' => 'Avvolge una creatura visibile con energia '
            . 'magica e, se fallisce il tiro salvezza, annulla la sua '
            . 'velocità di volare facendola scendere gradualmente.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),
];
