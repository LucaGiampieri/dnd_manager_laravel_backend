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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Abilità Potenziata',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Conferisce a una creatura consenziente maestria in un’abilità in cui è già competente, raddoppiandone il bonus di competenza.',
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
                        'roll_type' => 'skill_check',
                        'modifier_type' => 'special',
                        'sort_order' => 1,
                        'notes' => 'Raddoppia il bonus di competenza per l’abilità scelta in cui il bersaglio è già competente; non si cumula con Maestria o altre fonti di raddoppio.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Alba',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea un cilindro mobile di luce solare intensa che infligge danni radiosi alle creature presenti al suo interno.',
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
                        'key' => 'damage_radiant',
                        'damage_type' => 'Radioso',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 4,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Alla comparsa del cilindro o alla fine del turno al suo interno: TS Costituzione, metà se riesce.',
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Arma Sacra',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Infondendo energia sacra in un’arma, le conferisce danni radiosi aggiuntivi e permette di liberare un’esplosione accecante.',
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
                        'key' => 'damage_radiant',
                        'damage_type' => 'Radioso',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                        'condition' => 'Ogni attacco effettuato con l’arma incantata che colpisce; non è la formula dell’esplosione finale.',
                    ],
                    [
                        'key' => 'burst',
                        'damage_type' => 'Radioso',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 4,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Congedando l’incantesimo con un’azione bonus: creature scelte visibili entro 9 metri dall’arma, TS Costituzione, metà se riesce. Il centro è l’arma (errata ufficiale).',
                    ],
                ],
                'condition' => 'L’arma infligge 2d8 extra a ogni colpo. L’esplosione finale usa invece la propria formula e il proprio tiro salvezza.',
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Collera della Natura',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Anima erba, alberi, radici e rocce in un’ampia zona naturale per ostacolare, trattenere e ferire i nemici dell’incantatore.',
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
                        'key' => 'trees',
                        'damage_type' => 'Tagliente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 4,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'All’inizio del turno dell’incantatore: nemici entro 3 metri da un albero, TS Destrezza, nessun danno se riesce.',
                    ],
                    [
                        'key' => 'rock',
                        'damage_type' => 'Contundente',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 3,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Con azione bonus: attacco con incantesimo a distanza mediante una roccia. Dopo il colpo, TS Forza per evitare di cadere prono.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Colpo del Vento d’Acciaio',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Scompare come il vento, effettua un attacco con incantesimo in mischia contro un massimo di cinque creature e può ricomparire vicino a una di esse.',
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
                        'key' => 'damage_force',
                        'damage_type' => 'Forza',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 6,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Controllare Venti',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Controlla l’aria in un grande cubo, creando folate orizzontali, correnti discendenti o correnti ascendenti modificabili durante la durata.',
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
                        'roll_type' => 'attack',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 1,
                        'condition' => 'Solo attacchi con arma a distanza ostacolati dalle Folate moderate/forti o dalla Corrente Discendente.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Danza Macabra',
                'application_type' => 'special',
                'target_scope' => 'targets',
                'ends_with_source' => true,
                'description' => 'Anima fino a cinque cadaveri come scheletri o zombi temporanei e permette di comandarli mentalmente con un’azione bonus.',
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
                        'roll_type' => 'attack',
                        'modifier_type' => 'special',
                        'sort_order' => 1,
                        'notes' => 'Gli zombi o scheletri animati ottengono un bonus pari al modificatore della caratteristica da incantatore del creatore.',
                    ],
                    [
                        'roll_type' => 'damage',
                        'modifier_type' => 'special',
                        'sort_order' => 2,
                        'notes' => 'Gli zombi o scheletri animati ottengono un bonus pari al modificatore della caratteristica da incantatore del creatore.',
                    ],
                ],
                'scalings' => [
                    [
                        'key' => 'base_corpse_count',
                        'target_field' => 'corpse_count',
                        'source_type' => 'fixed',
                        'fixed_value' => 5,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_corpse_count',
                        'target_field' => 'corpse_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 6,
                        'source_offset' => -5,
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
        'save_success_damage' => null,
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Debilitazione',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Un tentacolo oscuro prosciuga una creatura, infliggendole ripetutamente danni necrotici e curando l’incantatore di parte dei danni causati.',
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
                                'minimum_source' => 6,
                                'source_offset' => -5,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                        'notes' => 'Formula piena: 4d8 più 1d8 per ogni livello dello slot oltre il quinto.',
                        'condition' => 'Solo TS iniziale fallito o riattivazione del collegamento con un’azione.',
                    ],
                    [
                        'key' => 'successful_initial_save',
                        'damage_type' => 'Necrotico',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 2,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se il TS iniziale riesce: sostituisce la formula piena. Non è metà di un tiro di 4d8; cresce autonomamente di 1d8 per livello dello slot oltre il quinto.',
                        'scalings' => [
                            [
                                'key' => 'higher_slot_dice_count',
                                'target_field' => 'dice_count',
                                'source_type' => 'spell_slot_level',
                                'operation' => 'add',
                                'minimum_source' => 6,
                                'source_offset' => -5,
                                'multiplier' => 1,
                                'divisor' => 1,
                                'rounding' => 'none',
                                'sort_order' => 1,
                                'notes' => 'Incremento rispetto al valore base, non incremento rispetto allo slot precedente.',
                            ],
                        ],
                    ],
                ],
                'condition' => 'Al lancio il TS Destrezza determina quale formula usare. Dopo il fallimento, un’azione nei turni successivi infligge di nuovo il danno pieno; un successo iniziale termina l’incantesimo.',
            ],
            [
                'key' => 'spell_effect_2',
                'name' => 'Beneficio dell’incantatore',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Beneficio applicato all’incantatore; restano valide le condizioni delle singole formule.',
                'sort_order' => 2,
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
                'healings' => [
                    [
                        'key' => 'healing',
                        'healing_type' => 'hit_points',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'modifier_source_type' => 'other',
                        'notes' => 'Usare i danni effettivamente subiti per questo effetto, non un nuovo tiro. Arrotondare per difetto dopo aver dimezzato.',
                        'modifier_multiplier' => 0.5,
                        'condition' => 'Dopo che il danno di questo effetto è stato effettivamente inflitto; destinatario: l’incantatore.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Flusso di Energia Negativa',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Invia energia negativa contro una creatura: danneggia i viventi e conferisce punti ferita temporanei ai non morti.',
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
                        'dice_count' => 5,
                        'die_size' => 12,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                        'condition' => 'Solo se il bersaglio non è un non morto; TS Costituzione riuscito: metà.',
                    ],
                ],
                'healings' => [
                    [
                        'key' => 'healing',
                        'healing_type' => 'temporary_hit_points',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 5,
                        'die_size' => 12,
                        'flat_bonus' => 0,
                        'condition' => 'Solo se il bersaglio è un non morto: non si tira il danno e non si applica il tiro salvezza.',
                        'notes' => 'Concede metà del risultato di 5d12, arrotondata per difetto.',
                        'temporary_hit_point_rule' => 'replace_if_higher',
                        'scalings' => [
                            [
                                'key' => 'half_rolled_total',
                                'target_field' => 'healing_total',
                                'source_type' => 'fixed',
                                'fixed_value' => 0.5,
                                'operation' => 'multiply',
                                'rounding' => 'floor',
                                'notes' => 'Dimezzare il risultato totale del tiro, non il numero di dadi.',
                            ],
                        ],
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Immolazione',
                'application_type' => 'failed_save',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Avvolge una creatura visibile nelle fiamme, infliggendo danni iniziali e ulteriori danni nei turni successivi finché il bersaglio non si salva.',
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
                        'key' => 'damage_fire',
                        'damage_type' => 'Fuoco',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 8,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                    [
                        'key' => 'burning',
                        'damage_type' => 'Fuoco',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 4,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'condition' => 'Alla fine dei turni successivi, solo se il bersaglio fallisce il TS Destrezza; se riesce, l’incantesimo termina e non subisce questi danni.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Maelstrom',
                'application_type' => 'on_start_turn',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea una massa d’acqua turbinante che rende il terreno difficile, danneggia le creature e le trascina verso il proprio centro.',
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
                        'key' => 'damage_bludgeoning',
                        'damage_type' => 'Contundente',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 6,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Creatura che inizia il turno nell’acqua: TS Forza, nessun danno né trascinamento se riesce.',
                'forced_movements' => [
                    [
                        'key' => 'movement_1',
                        'movement_type' => 'pull',
                        'origin_type' => 'area_center',
                        'direction_type' => 'toward_origin',
                        'distance' => 3,
                        'up_to_distance' => false,
                        'straight_line' => true,
                        'stops_at_obstacle' => true,
                        'opportunity_attack_rule' => 'does_not_provoke',
                        'sort_order' => 1,
                        'condition' => 'Solo TS Forza fallito all’inizio del turno; verso il centro dell’acqua.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Muro di Luce',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea un muro luminoso che danneggia e può accecare le creature; l’incantatore può consumarne porzioni per scagliare raggi radiosi.',
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
                        'key' => 'damage_radiant',
                        'damage_type' => 'Radioso',
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
                                'minimum_source' => 6,
                                'source_offset' => -5,
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
                'condition' => 'Alla comparsa: TS Costituzione, metà se riesce. Fine del turno nella parete: danno senza tiro. Raggio scagliato usando un’azione: attacco a distanza, stessa formula e riduzione della parete di 3 metri.',
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Passo Remoto',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Permette all’incantatore di teletrasportarsi fino a 18,288 metri al lancio e di ripetere il teletrasporto con un’azione bonus a ogni turno.',
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Richiamo Infernale',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Evoca un diavolo dai Nove Inferi che deve essere controllato tramite comandi e prove di Carisma, salvo l’uso del suo talismano.',
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
                'scalings' => [
                    [
                        'key' => 'base_maximum_challenge_rating',
                        'target_field' => 'maximum_challenge_rating',
                        'source_type' => 'fixed',
                        'fixed_value' => 6,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_maximum_challenge_rating',
                        'target_field' => 'maximum_challenge_rating',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 6,
                        'source_offset' => -5,
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Scossa Sinaptica',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Genera un’esplosione psichica che danneggia le creature e può confonderne i pensieri, penalizzando attacchi, prove e concentrazione.',
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
                        'key' => 'damage_psychic',
                        'damage_type' => 'Psichico',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 8,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'penalty',
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'condition' => 'Solo se il bersaglio ha fallito il TS Intelligenza iniziale, fino a 1 minuto o finché supera un TS Intelligenza di fine turno.',
                    ],
                    [
                        'roll_type' => 'ability_check',
                        'modifier_type' => 'penalty',
                        'sort_order' => 2,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'condition' => 'Solo se il bersaglio ha fallito il TS Intelligenza iniziale, fino a 1 minuto o finché supera un TS Intelligenza di fine turno.',
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'penalty',
                        'sort_order' => 3,
                        'dice_count' => 1,
                        'die_size' => 6,
                        'ability' => 'COS',
                        'condition' => 'Esclusivamente tiri salvezza per mantenere la concentrazione, dopo il fallimento del tiro iniziale.',
                    ],
                ],
            ],
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

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Trasmutare Roccia',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Trasforma roccia non magica in fango oppure fango e sabbie mobili non magici in roccia, modificando il terreno e intrappolando le creature.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'until_source_ends',
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
                    ],
                ],
                'condition' => 'Solo Roccia in Fango su un soffitto: le creature sotto il fango cadente effettuano TS Destrezza, metà se riesce.',
            ],
        ],
    ]),
];
