<?php

//Valori condivisi dagli incantesimi di 7° livello di Xanathar
$defaults = [
    'level' => 7,
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

//Restituisce i 4 incantesimi di 7° livello di Xanathar
return [
    $spell([
        'key' => 'crown_of_stars',
        'name' => 'Corona di Stelle',
        'school_key' => 'evocation',
        'page' => 153,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'attack_type' => 'ranged',
        'description' => 'Crea sette scintille stellari orbitanti che '
            . 'l’incantatore può scagliare come attacchi con '
            . 'incantesimo a distanza per infliggere danni radiosi.',
        'higher_levels' => 'Crea due scintille aggiuntive per ogni '
            . 'slot di livello superiore al 7°.',
        'target' => [
            'target_type' => 'self',
            'target_count' => 1,
            'can_target_self' => true,
            'notes' => 'Ogni scintilla può essere scagliata contro '
                . 'una creatura o un oggetto entro 36,576 metri.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Corona di Stelle',
                'application_type' => 'on_hit',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Crea sette scintille stellari orbitanti che l’incantatore può scagliare come attacchi con incantesimo a distanza per infliggere danni radiosi.',
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
                        'dice_count' => 4,
                        'die_size' => 12,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Formula per una singola scintilla scagliata con azione bonus e attacco con incantesimo a distanza.',
                'scalings' => [
                    [
                        'key' => 'base_mote_count',
                        'target_field' => 'mote_count',
                        'source_type' => 'fixed',
                        'fixed_value' => 7,
                        'operation' => 'set',
                        'sort_order' => 0,
                    ],
                    [
                        'key' => 'higher_slot_mote_count',
                        'target_field' => 'mote_count',
                        'source_type' => 'spell_slot_level',
                        'operation' => 'add',
                        'minimum_source' => 8,
                        'source_offset' => -7,
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
        'key' => 'power_word_pain',
        'name' => 'Parola del Potere Dolore',
        'school_key' => 'enchantment',
        'page' => 163,
        'range' => 18.288,
        'verbal_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Infligge dolori lancinanti a una creatura '
            . 'con non più di 100 punti ferita, limitandone il '
            . 'movimento e penalizzando attacchi, prove e salvezze.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'Il bersaglio può ripetere il tiro salvezza '
                . 'su Costituzione alla fine di ogni suo turno.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Parola del Potere Dolore',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Infligge dolori lancinanti a una creatura con non più di 100 punti ferita, limitandone il movimento e penalizzando attacchi, prove e salvezze.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 1,
                        'condition' => 'La creatura deve avere 100 PF o meno, non essere immune allo charme e rimanere soggetta all’incantesimo. Per i TS sono esclusi quelli su Costituzione.',
                    ],
                    [
                        'roll_type' => 'ability_check',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 2,
                        'condition' => 'La creatura deve avere 100 PF o meno, non essere immune allo charme e rimanere soggetta all’incantesimo. Per i TS sono esclusi quelli su Costituzione.',
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 3,
                        'condition' => 'La creatura deve avere 100 PF o meno, non essere immune allo charme e rimanere soggetta all’incantesimo. Per i TS sono esclusi quelli su Costituzione.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'temple_of_the_gods',
        'name' => 'Tempio degli Dèi',
        'school_key' => 'conjuration',
        'page' => 168,
        'casting_time_type' => 'hour',
        'range' => 36.576,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un simbolo sacro del valore di '
            . 'almeno 5 mo.',
        'material_cost' => 5,
        'duration_type' => 'hour',
        'duration_value' => 24,
        'description' => 'Fa materializzare un tempio consacrato che '
            . 'protegge i suoi occupanti, ostacola determinati tipi '
            . 'di creature e potenzia le cure magiche.',
        'notes' => 'Lanciato nello stesso luogo ogni giorno per un '
            . 'anno, il tempio diventa permanente.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cube',
            'area_size_meters' => 36.576,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Tempio degli Dèi',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Fa materializzare un tempio consacrato che protegge i suoi occupanti, ostacola determinati tipi di creature e potenzia le cure magiche.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 24,
                        'duration_unit' => 'hour',
                        'sort_order' => 1,
                    ],
                ],
                'healings' => [
                    [
                        'key' => 'healing',
                        'healing_type' => 'hit_points',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'modifier_source_type' => 'caster_ability_modifier',
                        'condition' => 'Guarigione aggiuntiva per una creatura nel tempio che recupera PF grazie a un incantesimo di livello 1 o superiore.',
                        'notes' => 'Usa il modificatore di Saggezza del creatore del tempio, minimo 1; non il modificatore di chi lancia la cura.',
                        'modifier_ability' => 'SAG',
                        'scalings' => [
                            [
                                'key' => 'minimum_one',
                                'target_field' => 'healing_total',
                                'source_type' => 'fixed',
                                'fixed_value' => 1,
                                'operation' => 'minimum',
                            ],
                        ],
                    ],
                ],
                'roll_modifiers' => [
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'penalty',
                        'sort_order' => 1,
                        'dice_count' => 1,
                        'die_size' => 4,
                        'condition' => 'Solo creature dei tipi ostacolati dal tempio che siano riuscite a entrarvi.',
                    ],
                    [
                        'roll_type' => 'ability_check',
                        'modifier_type' => 'penalty',
                        'sort_order' => 2,
                        'dice_count' => 1,
                        'die_size' => 4,
                        'condition' => 'Solo creature dei tipi ostacolati dal tempio che siano riuscite a entrarvi.',
                    ],
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'penalty',
                        'sort_order' => 3,
                        'dice_count' => 1,
                        'die_size' => 4,
                        'condition' => 'Solo creature dei tipi ostacolati dal tempio che siano riuscite a entrarvi.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'whirlwind',
        'name' => 'Turbine',
        'school_key' => 'evocation',
        'page' => 171,
        'range' => 91.44,
        'verbal_component' => true,
        'material_component' => true,
        'material_description' => 'Una pagliuzza.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Crea un turbine mobile che danneggia le '
            . 'creature, risucchia gli oggetti e può trattenere e '
            . 'sollevare le creature di taglia Grande o inferiore.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'cylinder',
            'area_size_meters' => 3.048,
            'area_secondary_size_meters' => 9.144,
            'requires_sight' => true,
            'notes' => 'La misura principale rappresenta il raggio '
                . 'e la seconda misura rappresenta l’altezza.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Turbine',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea un turbine mobile che danneggia le creature, risucchia gli oggetti e può trattenere e sollevare le creature di taglia Grande o inferiore.',
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
                        'dice_count' => 10,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Alla comparsa, prima entrata del turbine nello spazio di una creatura in un turno (inclusa la sua comparsa) o prima entrata della creatura nel turbine: TS Destrezza, metà se riesce; il TS Forza per essere trattenuti è distinto.',
                'roll_modifiers' => [
                    [
                        'roll_type' => 'other',
                        'modifier_type' => 'special',
                        'sort_order' => 1,
                        'dice_count' => 3,
                        'die_size' => 6,
                        'notes' => 'Solo dopo una fuga riuscita: distanza di espulsione pari al risultato × 3 metri, in direzione casuale.',
                    ],
                ],
                'forced_movements' => [
                    [
                        'key' => 'movement_1',
                        'movement_type' => 'move',
                        'origin_type' => 'area_center',
                        'direction_type' => 'special',
                        'distance' => 1.5,
                        'up_to_distance' => false,
                        'straight_line' => true,
                        'stops_at_obstacle' => true,
                        'opportunity_attack_rule' => 'does_not_provoke',
                        'sort_order' => 1,
                        'condition' => 'All’inizio del turno, se trattenuta: viene sollevata di 1,5 metri, fino alla cima del turbine.',
                    ],
                    [
                        'key' => 'movement_2',
                        'movement_type' => 'special',
                        'origin_type' => 'area_center',
                        'direction_type' => 'random_direction',
                        'distance' => null,
                        'up_to_distance' => false,
                        'straight_line' => true,
                        'stops_at_obstacle' => true,
                        'opportunity_attack_rule' => 'does_not_provoke',
                        'sort_order' => 2,
                        'condition' => 'Dopo una prova riuscita per liberarsi: distanza 3d6 × 3 metri, usando il tiro ausiliario di questo effetto.',
                    ],
                ],
            ],
        ],
    ]),
];
