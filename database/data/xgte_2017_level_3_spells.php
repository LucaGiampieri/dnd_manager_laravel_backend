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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Eruzione Terrestre',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Fa eruttare terra e pietre in un cubo, danneggiando le creature e lasciando terreno difficile finché l’area non viene sgombrata.',
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
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 3,
                        'die_size' => 12,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 4,
                                'source_offset' => -3,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Evoca Demoni Minori',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Evoca in spazi visibili un gruppo casuale di demoni minori, ostili a tutte le creature e controllati dal Dungeon Master.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 1,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'other',
                        'modifier_type' => 'special',
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'notes' => '1-2: due demoni GS 1 o meno; 3-4: quattro GS 1/2 o meno; 5-6: otto GS 1/4 o meno.',
                    ],
                ],
                'scalings' => [
                    [
                        'key' => 'spell_slot_level_6_summon_count_multiplier',
                        'target_field' => 'summon_count_multiplier',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'set',
                        'minimum_source' => 6,
                        'maximum_source' => 7,
                        'multiplier' => 0,
                        'flat_value' => 2,
                        'sort_order' => 1,
                    ],
                    [
                        'key' => 'spell_slot_level_8_summon_count_multiplier',
                        'target_field' => 'summon_count_multiplier',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'set',
                        'minimum_source' => 8,
                        'maximum_source' => null,
                        'multiplier' => 0,
                        'flat_value' => 3,
                        'sort_order' => 2,
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Frecce Infuocate',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Incanta una faretra affinché le prime dodici munizioni estratte infliggano danni da fuoco extra quando colpiscono.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 1,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Formula aggiuntiva per ciascuna munizione incantata che colpisce, non un danno unico per l’intera faretra.',
                'scalings' => [
                    [
                        'key' => 'base_ammunition_count',
                        'target_field' => 'ammunition_count',
                        'source_type' => 'fixed',
                        'fixed_value' => 12,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_ammunition_count',
                        'target_field' => 'ammunition_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 4,
                        'source_offset' => -3,
                        'multiplier' => 2,
                        'divisor' => 1,
                        'rounding' => 'none',
                        'sort_order' => 1,
                        'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Minuscole Meteore di Melf',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Crea sei piccole meteore orbitanti che l’incantatore può scagliare una o due alla volta, producendo esplosioni di fuoco.',
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
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Formula per ciascuna meteora scagliata: creature entro 1,5 metri dal punto, TS Destrezza, metà se riesce.',
                'scalings' => [
                    [
                        'key' => 'base_meteor_count',
                        'target_field' => 'meteor_count',
                        'source_type' => 'fixed',
                        'fixed_value' => 6,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_meteor_count',
                        'target_field' => 'meteor_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 4,
                        'source_offset' => -3,
                        'multiplier' => 2,
                        'divisor' => 1,
                        'rounding' => 'none',
                        'sort_order' => 1,
                        'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Muro d’Acqua',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Evoca un muro d’acqua che ostacola il movimento e gli attacchi a distanza, riduce il fuoco e può essere congelato.',
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
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 1,
                        'condition' => 'Solo attacchi con arma a distanza che attraversano il muro.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Muro di Sabbia',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Evoca un muro di sabbia turbinante che blocca la visuale, acceca chi vi si trova e rallenta fortemente il movimento al suo interno.',
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
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Nemici in Abbondanza',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Confunde una creatura visibile, impedendole di distinguere amici e nemici e obbligandola a scegliere casualmente i propri bersagli.',
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
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Onda di Marea',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Evoca un’onda che travolge un’area, infligge danni contundenti, può far cadere le creature ed estingue le fiamme non protette.',
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
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 4,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Passo del Tuono',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Teletrasporta l’incantatore, eventualmente con una creatura consenziente, e genera un’esplosione tonante nello spazio lasciato.',
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
                        'key' => 'damage_thunder',
                        'damage_type' => 'Tuono',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 3,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 4,
                                'source_offset' => -3,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Servitore Minuscolo',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Anima un oggetto Minuscolo non magico, trasformandolo in un servitore controllabile con comandi mentali.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 8,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
                'damages' => [
                    [
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 4,
                        'flat_bonus' => 3,
                    ],
                ],
                'condition' => 'Attacco Schianto del servitore minuscolo evocato; bonus al tiro per colpire +5, portata 1,5 metri.',
                'scalings' => [
                    [
                        'key' => 'base_servant_count',
                        'target_field' => 'servant_count',
                        'source_type' => 'fixed',
                        'fixed_value' => 1,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_servant_count',
                        'target_field' => 'servant_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 4,
                        'source_offset' => -3,
                        'multiplier' => 2,
                        'divisor' => 1,
                        'rounding' => 'none',
                        'sort_order' => 1,
                        'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Sonnellino',
                'application_type' => 'special',
                'target_scope' => 'targets',
                'ends_with_source' => true,
                'description' => 'Fa addormentare fino a tre creature consenzienti; chi completa l’intera durata ottiene i benefici di un riposo breve.',
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
                'scalings' => [
                    [
                        'key' => 'base_target_count',
                        'target_field' => 'target_count',
                        'source_type' => 'fixed',
                        'fixed_value' => 3,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_target_count',
                        'target_field' => 'target_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 4,
                        'source_offset' => -3,
                        'multiplier' => 1,
                        'divisor' => 1,
                        'rounding' => 'none',
                        'sort_order' => 1,
                        'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Trasferimento di Vita',
                'application_type' => 'automatic',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Sacrifica parte della salute dell’incantatore per curare una creatura visibile del doppio dei danni necrotici subiti.',
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
                        'dice_count' => 4,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 4,
                                'source_offset' => -3,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'condition' => 'Danni all’incantatore, non al bersaglio curato. Non possono essere ridotti in alcun modo (errata ufficiale).',
                    ],
                ],
            ],
            [
                'key' => 'spell_effect_2',
                'name' => 'Guarigione del destinatario',
                'application_type' => 'automatic',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Sacrifica parte della salute dell’incantatore per curare una creatura visibile del doppio dei danni necrotici subiti.',
                'sort_order' => 2,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'healings' => [
                    [
                        'key' => 'healing',
                        'healing_type' => 'hit_points',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'modifier_source_type' => 'other',
                        'notes' => 'Usare i danni effettivamente subiti per questo effetto, non un nuovo tiro. La guarigione è il doppio dei danni subiti dall’incantatore.',
                        'modifier_multiplier' => 2,
                        'condition' => 'Dopo che il danno di questo effetto è stato effettivamente inflitto; destinatario: una creatura visibile diversa dall’incantatore.',
                    ],
                ],
            ],
        ],
    ]),
];
