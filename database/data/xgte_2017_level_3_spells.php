<?php

//Valori condivisi dagli incantesimi di 3° livello di Xanathar
$defaults = [
    'level' => 3,
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

//Restituisce i 12 incantesimi di 3° livello di Xanathar
return [
    $spell([
        'key' => 'erupting_earth',
        'name' => 'Eruzione Terrestre',
        'school_key' => 'transmutation',
        'page' => 156,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un frammento d’ossidiana.',
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Fa eruttare terra e pietre in un cubo, '
            . 'danneggiando le creature e lasciando terreno difficile '
            . 'finché l’area non viene sgombrata.',
        'higher_levels' => 'Il danno aumenta di 1d12 per ogni slot di '
            . 'livello superiore al 3°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 6.096,
            'can_target_objects' => true,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'summon_lesser_demons',
        'name' => 'Evoca Demoni Minori',
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
        'description' => 'Evoca in spazi visibili un gruppo casuale '
            . 'di demoni minori, ostili a tutte le creature e '
            . 'controllati dal Dungeon Master.',
        'higher_levels' => 'Evoca il doppio dei demoni con slot di 6° '
            . 'o 7° livello e il triplo con slot di 8° o 9°.',
        'notes' => 'Il componente viene consumato soltanto quando '
            . 'viene usato per creare il cerchio protettivo.',
        'target' => [
            'target_type' => 'special',
            'requires_sight' => true,
            'notes' => 'Evoca due, quattro oppure otto demoni in spazi '
                . 'liberi visibili entro gittata.',
        ],
    ]),

    $spell([
        'key' => 'flame_arrows',
        'name' => 'Frecce Infuocate',
        'school_key' => 'transmutation',
        'page' => 158,
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Incanta una faretra affinché le prime dodici '
            . 'munizioni estratte infliggano danni da fuoco extra '
            . 'quando colpiscono.',
        'higher_levels' => 'Incanta due munizioni aggiuntive per ogni '
            . 'slot di livello superiore al 3°.',
        'target' => [
            'target_type' => 'object',
            'target_count' => 1,
            'can_target_objects' => true,
            'notes' => 'Bersaglia una faretra contenente frecce o '
                . 'quadrelli.',
        ],
    ]),

    $spell([
        'key' => 'melfs_minute_meteors',
        'name' => 'Minuscole Meteore di Melf',
        'school_key' => 'evocation',
        'page' => 162,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Salnitro, zolfo e pece di pino '
            . 'mescolati in una biglia.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Crea sei piccole meteore orbitanti che '
            . 'l’incantatore può scagliare una o due alla volta, '
            . 'producendo esplosioni di fuoco.',
        'higher_levels' => 'Crea due meteore aggiuntive per ogni slot '
            . 'di livello superiore al 3°.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'Ogni meteora può raggiungere un punto entro '
                . '36,576 metri ed esplode in un raggio di 1,524 metri.',
        ],
    ]),

    $spell([
        'key' => 'wall_of_water',
        'name' => 'Muro d’Acqua',
        'school_key' => 'evocation',
        'page' => 163,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia d’acqua.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Evoca un muro d’acqua che ostacola il '
            . 'movimento e gli attacchi a distanza, riduce il fuoco '
            . 'e può essere congelato.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'wall',
            'area_size_meters' => 9.144,
            'area_secondary_size_meters' => 3.048,
            'requires_sight' => true,
            'notes' => 'Può essere un muro lungo 9,144 metri e alto '
                . '3,048 oppure un muro circolare del diametro di '
                . '6,096 metri e alto 6,096 metri.',
        ],
    ]),

    $spell([
        'key' => 'wall_of_sand',
        'name' => 'Muro di Sabbia',
        'school_key' => 'evocation',
        'page' => 163,
        'range' => 27.432,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una manciata di sabbia.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Evoca un muro di sabbia turbinante che '
            . 'blocca la visuale, acceca chi vi si trova e rallenta '
            . 'fortemente il movimento al suo interno.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'wall',
            'area_size_meters' => 9.144,
            'area_secondary_size_meters' => 3.048,
            'requires_sight' => true,
            'notes' => 'Il muro può essere alto e spesso fino a '
                . '3,048 metri.',
        ],
    ]),

    $spell([
        'key' => 'enemies_abound',
        'name' => 'Nemici in Abbondanza',
        'school_key' => 'enchantment',
        'page' => 163,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'INT',
        'save_success_damage' => 'none',
        'description' => 'Confunde una creatura visibile, impedendole '
            . 'di distinguere amici e nemici e obbligandola a '
            . 'scegliere casualmente i propri bersagli.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Una creatura immune alla condizione di '
                . 'spaventato supera automaticamente il tiro.',
        ],
    ]),

    $spell([
        'key' => 'tidal_wave',
        'name' => 'Onda di Marea',
        'school_key' => 'conjuration',
        'page' => 163,
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una goccia d’acqua.',
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Evoca un’onda che travolge un’area, infligge '
            . 'danni contundenti, può far cadere le creature ed '
            . 'estingue le fiamme non protette.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'rectangle',
            'area_size_meters' => 9.144,
            'area_secondary_size_meters' => 3.048,
            'can_target_objects' => true,
            'notes' => 'L’onda può essere lunga 9,144 metri, larga '
                . '3,048 metri e alta 3,048 metri.',
        ],
    ]),

    $spell([
        'key' => 'thunder_step',
        'name' => 'Passo del Tuono',
        'school_key' => 'conjuration',
        'page' => 164,
        'range' => 27.432,
        'verbal_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Teletrasporta l’incantatore, eventualmente '
            . 'con una creatura consenziente, e genera un’esplosione '
            . 'tonante nello spazio lasciato.',
        'higher_levels' => 'Il danno aumenta di 1d10 per ogni slot di '
            . 'livello superiore al 3°.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 3.048,
            'can_target_self' => true,
            'can_target_objects' => true,
            'requires_sight' => true,
            'notes' => 'L’area è centrata sullo spazio lasciato; la '
                . 'destinazione deve essere uno spazio libero visibile.',
        ],
    ]),

    $spell([
        'key' => 'tiny_servant',
        'name' => 'Servitore Minuscolo',
        'school_key' => 'transmutation',
        'page' => 167,
        'casting_time_type' => 'minute',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 8,
        'description' => 'Anima un oggetto Minuscolo non magico, '
            . 'trasformandolo in un servitore controllabile con '
            . 'comandi mentali.',
        'higher_levels' => 'Anima due oggetti aggiuntivi per ogni slot '
            . 'di livello superiore al 3°.',
        'target' => [
            'target_type' => 'object',
            'target_count' => 1,
            'can_target_objects' => true,
            'notes' => 'L’oggetto non deve essere fissato, trasportato '
                . 'o già magico.',
        ],
    ]),

    $spell([
        'key' => 'catnap',
        'name' => 'Sonnellino',
        'school_key' => 'enchantment',
        'page' => 169,
        'range' => 9.144,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un pizzico di sabbia.',
        'duration_type' => 'minute',
        'duration_value' => 10,
        'description' => 'Fa addormentare fino a tre creature '
            . 'consenzienti; chi completa l’intera durata ottiene i '
            . 'benefici di un riposo breve.',
        'higher_levels' => 'Bersaglia una creatura aggiuntiva per ogni '
            . 'slot di livello superiore al 3°.',
        'target' => [
            'target_type' => 'creatures',
            'target_count' => 3,
            'can_target_self' => true,
            'requires_sight' => true,
        ],
    ]),

    $spell([
        'key' => 'life_transference',
        'name' => 'Trasferimento di Vita',
        'school_key' => 'necromancy',
        'page' => 170,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'description' => 'Sacrifica parte della salute '
            . 'dell’incantatore per curare una creatura visibile del '
            . 'doppio dei danni necrotici subiti.',
        'higher_levels' => 'Il danno subito aumenta di 1d8 per ogni '
            . 'slot di livello superiore al 3°.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],
    ]),
];
