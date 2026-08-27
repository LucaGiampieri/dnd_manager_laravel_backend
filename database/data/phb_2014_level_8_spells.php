<?php

//Valori comuni applicati agli incantesimi di 8° livello
$defaults = [
    'level' => 8,
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

//Costruisce un incantesimo applicando i valori predefiniti del bersaglio
$spell = static function (array $data) use ($defaults): array {
    $data['target'] = array_replace([
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

    return array_replace($defaults, $data);
};

//Restituisce tutti i 18 incantesimi di 8° livello del PHB 2014
return [
    $spell([
        'key' => 'antipathy_sympathy',
        'name' => 'Antipatia/Simpatia',
        'school_key' => 'enchantment',
        'page' => 214,
        'casting_time_type' => 'hour',
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un frammento di allume immerso nell’aceto oppure una goccia di miele, in base all’effetto scelto.',
        'duration_type' => 'day',
        'duration_value' => 10,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Infonde in un bersaglio un’aura che respinge o attira un tipo di creatura intelligente scelto dall’incantatore.',
        'target' => [
            'target_type' => 'special',
            'target_count' => 1,
            'can_target_objects' => true,
            'notes' => 'Può bersagliare una creatura Enorme o più piccola, un oggetto oppure un’area cubica con spigolo massimo di 60,96 metri.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Antipatia/Simpatia',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Infonde in un bersaglio un’aura che respinge o attira un tipo di creatura intelligente scelto dall’incantatore.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 10,
                        'duration_unit' => 'day',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'holy_aura',
        'name' => 'Aura Sacra',
        'school_key' => 'abjuration',
        'page' => 216,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un minuscolo reliquiario del valore di almeno 1.000 mo contenente una reliquia sacra.',
        'material_cost' => 1000,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Avvolge gli alleati vicini in una luce divina che li protegge dagli attacchi e migliora i loro tiri salvezza.',
        'materials' => [
            [
                'key' => 'holy_reliquary',
                'name' => 'Reliquiario sacro',
                'description' => 'Un piccolo reliquiario contenente una reliquia sacra.',
                'cost_amount' => 1000,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => false,
                'focus_replaceable' => false,
                'sort_order' => 1,
            ],
        ],
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'emanation',
            'area_size_meters' => 9.144,
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Aura Sacra',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Avvolge gli alleati vicini in una luce divina che li protegge dagli attacchi e migliora i loro tiri salvezza.',
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
                'roll_modifiers' => [
                    [
                        'roll_type' => 'saving_throw',
                        'modifier_type' => 'advantage',
                        'sort_order' => 1,
                        'condition' => 'Tutti i tiri salvezza.',
                    ],
                    [
                        'roll_type' => 'attack',
                        'modifier_type' => 'disadvantage',
                        'sort_order' => 2,
                        'condition' => 'Attacchi contro le creature protette.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'antimagic_field',
        'name' => 'Campo Anti-Magia',
        'school_key' => 'abjuration',
        'page' => 219,
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un pizzico di polvere di ferro o di limatura di ferro.',
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'description' => 'Crea attorno all’incantatore una sfera mobile che sopprime incantesimi, oggetti magici ed effetti soprannaturali.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 3.048,
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Campo Anti-Magia',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea attorno all’incantatore una sfera mobile che sopprime incantesimi, oggetti magici ed effetti soprannaturali.',
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
            ],
        ],
    ]),

    $spell([
        'key' => 'clone',
        'name' => 'Clone',
        'school_key' => 'necromancy',
        'page' => 222,
        'casting_time_type' => 'hour',
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un diamante da almeno 1.000 mo, un cubo di carne della creatura e un involucro sigillabile da almeno 2.000 mo; diamante e carne sono consumati.',
        'material_consumed' => true,
        'material_cost' => 3000,
        'description' => 'Fa maturare in 120 giorni un duplicato inerte di una creatura vivente, pronto a riceverne l’anima dopo la morte.',
        'materials' => [
            [
                'key' => 'diamond',
                'name' => 'Diamante',
                'description' => 'Un diamante del valore di almeno 1.000 mo.',
                'cost_amount' => 1000,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => true,
                'focus_replaceable' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'flesh_cube',
                'name' => 'Cubo di carne',
                'description' => 'Un cubo di carne della creatura da clonare con spigolo di almeno 2,5 centimetri.',
                'consumed' => true,
                'focus_replaceable' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'sealed_vessel',
                'name' => 'Involucro sigillabile',
                'description' => 'Un recipiente sigillabile abbastanza grande da contenere una creatura Media.',
                'cost_amount' => 2000,
                'currency_code' => 'mo',
                'cost_is_minimum' => true,
                'consumed' => false,
                'focus_replaceable' => false,
                'sort_order' => 3,
            ],
        ],
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'notes' => 'Richiede un campione di carne di una creatura vivente; il duplicato rimane nel suo involucro fino alla morte dell’originale.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Clone',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Fa maturare in 120 giorni un duplicato inerte di una creatura vivente, pronto a riceverne l’anima dopo la morte.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'control_weather',
        'name' => 'Controllare Tempo Atmosferico',
        'school_key' => 'transmutation',
        'page' => 225,
        'casting_time_value' => 10,
        'casting_time_type' => 'minute',
        'range_type' => 'self',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Incenso bruciato e frammenti di terra e legno mescolati nell’acqua.',
        'duration_type' => 'hour',
        'duration_value' => 8,
        'concentration' => true,
        'description' => 'Permette di modificare gradualmente precipitazioni, temperatura e vento in una vasta zona attorno all’incantatore.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'circle',
            'area_size_meters' => 8046.72,
            'can_target_self' => true,
            'notes' => 'L’effetto richiede che l’incantatore si trovi all’aperto e abbia una linea diretta verso il cielo.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Controllare Tempo Atmosferico',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Permette di modificare gradualmente precipitazioni, temperatura e vento in una vasta zona attorno all’incantatore.',
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
            ],
        ],
    ]),

    $spell([
        'key' => 'dominate_monster',
        'name' => 'Dominare Mostri',
        'school_key' => 'enchantment',
        'page' => 231,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'SAG',
        'save_success_damage' => 'none',
        'description' => 'Affascina una creatura e stabilisce un legame telepatico attraverso il quale l’incantatore può impartirle comandi.',
        'higher_levels' => 'Con uno slot di 9° livello la concentrazione può durare fino a 8 ore.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Dominare Mostri',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Affascina una creatura e stabilisce un legame telepatico attraverso il quale l’incantatore può impartirle comandi.',
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
            ],
        ],
    ]),

    $spell([
        'key' => 'sunburst',
        'name' => 'Esplosione Solare',
        'school_key' => 'evocation',
        'page' => 232,
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Fuoco e un frammento di pietra del sole.',
        'saving_throw' => 'COS',
        'save_success_damage' => 'half',
        'description' => 'Un lampo di luce solare infligge 12d6 danni radiosi e può accecare le creature nell’area.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 18.288,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Esplosione Solare',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Un lampo di luce solare infligge 12d6 danni radiosi e può accecare le creature nell’area.',
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
                        'key' => 'damage_radiant',
                        'damage_type' => 'Radioso',
                        'is_primary' => true,
                        'sort_order' => 1,
                        'dice_count' => 12,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'animal_shapes',
        'name' => 'Forme Animali',
        'school_key' => 'transmutation',
        'page' => 237,
        'range' => 9.144,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 24,
        'concentration' => true,
        'description' => 'Trasforma un qualsiasi numero di creature consenzienti in bestie Grandi o più piccole con grado di sfida non superiore a 4.',
        'target' => [
            'target_type' => 'creature',
            'requires_sight' => true,
            'notes' => 'Può influenzare un qualsiasi numero di creature consenzienti entro gittata.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Forme Animali',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Trasforma un qualsiasi numero di creature consenzienti in bestie Grandi o più piccole con grado di sfida non superiore a 4.',
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
            ],
        ],
    ]),

    $spell([
        'key' => 'maze',
        'name' => 'Labirinto',
        'school_key' => 'conjuration',
        'page' => 247,
        'range' => 18.288,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 10,
        'concentration' => true,
        'description' => 'Esilia una creatura in un semipiano labirintico dal quale può tentare di fuggire con una prova di Intelligenza.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Labirinto',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Esilia una creatura in un semipiano labirintico dal quale può tentare di fuggire con una prova di Intelligenza.',
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
        'key' => 'glibness',
        'name' => 'Loquacità',
        'school_key' => 'transmutation',
        'page' => 248,
        'range_type' => 'self',
        'verbal_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'description' => 'Garantisce grande eloquenza, impedisce risultati bassi nelle prove di Carisma e inganna le magie che rilevano le menzogne.',
        'target' => [
            'target_type' => 'self',
            'can_target_self' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Loquacità',
                'application_type' => 'special',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'description' => 'Garantisce grande eloquenza, impedisce risultati bassi nelle prove di Carisma e inganna le magie che rilevano le menzogne.',
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
                        'roll_type' => 'ability_check',
                        'modifier_type' => 'minimum',
                        'sort_order' => 1,
                        'value' => 15,
                        'ability' => 'CAR',
                        'notes' => 'Sostituisce con 15 il risultato del d20 per le prove di Carisma, non imposta a 15 il totale della prova.',
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'incendiary_cloud',
        'name' => 'Nube Incendiaria',
        'school_key' => 'conjuration',
        'page' => 256,
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'half',
        'description' => 'Crea una nube di fumo e tizzoni che oscura l’area, infligge danni da fuoco e si sposta a ogni round.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 6.096,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Nube Incendiaria',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Crea una nube di fumo e tizzoni che oscura l’area, infligge danni da fuoco e si sposta a ogni round.',
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
                        'dice_count' => 10,
                        'die_size' => 8,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                    ],
                ],
                'condition' => 'Alla comparsa della nube, prima entrata in un turno o fine del turno al suo interno: TS Destrezza, metà del danno se riesce.',
            ],
        ],
    ]),

    $spell([
        'key' => 'power_word_stun',
        'name' => 'Parola del Potere Stordire',
        'school_key' => 'enchantment',
        'page' => 258,
        'range' => 18.288,
        'verbal_component' => true,
        'saving_throw' => 'COS',
        'save_success_damage' => 'none',
        'description' => 'Stordisce una creatura con non più di 150 punti ferita; il bersaglio può terminare l’effetto superando un tiro salvezza.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Parola del Potere Stordire',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Stordisce una creatura con non più di 150 punti ferita; il bersaglio può terminare l’effetto superando un tiro salvezza.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'instantaneous',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'sort_order' => 1,
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'feeblemind',
        'name' => 'Regressione Mentale',
        'school_key' => 'enchantment',
        'page' => 269,
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Una manciata di argilla, cristallo, vetro o sfere minerali.',
        'saving_throw' => 'INT',
        'save_success_damage' => 'none',
        'description' => 'Danneggia la mente di una creatura e, se il tiro salvezza fallisce, riduce drasticamente Intelligenza e Carisma.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
            'requires_sight' => true,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Regressione Mentale',
                'application_type' => 'automatic',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Danneggia la mente di una creatura e, se il tiro salvezza fallisce, riduce drasticamente Intelligenza e Carisma.',
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
                        'dice_count' => 4,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'I danni si applicano prima del TS Intelligenza e anche se questo riesce; il tiro riguarda le capacità mentali.',
            ],
        ],
    ]),

    $spell([
        'key' => 'demiplane',
        'name' => 'Semipiano',
        'school_key' => 'conjuration',
        'page' => 276,
        'range' => 18.288,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 1,
        'description' => 'Apre su una superficie una porta d’ombra collegata a una stanza extradimensionale o a un semipiano già conosciuto.',
        'target' => [
            'target_type' => 'point',
            'target_count' => 1,
            'requires_sight' => true,
            'notes' => 'La porta deve apparire su una superficie solida e piatta; il semipiano misura 9,144 metri in ogni dimensione.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Semipiano',
                'application_type' => 'special',
                'target_scope' => 'special',
                'ends_with_source' => true,
                'description' => 'Apre su una superficie una porta d’ombra collegata a una stanza extradimensionale o a un semipiano già conosciuto.',
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
            ],
        ],
    ]),

    $spell([
        'key' => 'telepathy',
        'name' => 'Telepatia',
        'school_key' => 'evocation',
        'page' => 283,
        'range_type' => 'unlimited',
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un paio di anelli d’argento concatenati.',
        'duration_type' => 'hour',
        'duration_value' => 24,
        'description' => 'Stabilisce un legame telepatico con una creatura consenziente e familiare sullo stesso piano di esistenza.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Telepatia',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Stabilisce un legame telepatico con una creatura consenziente e familiare sullo stesso piano di esistenza.',
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
            ],
        ],
    ]),

    $spell([
        'key' => 'earthquake',
        'name' => 'Terremoto',
        'school_key' => 'evocation',
        'page' => 285,
        'range' => 152.4,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => true,
        'material_description' => 'Un pizzico di terriccio, un frammento di pietra e un pezzo d’argilla.',
        'duration_type' => 'minute',
        'duration_value' => 1,
        'concentration' => true,
        'saving_throw' => 'DES',
        'save_success_damage' => 'none',
        'description' => 'Scuote violentemente il terreno, apre crepacci e danneggia le strutture in una vasta area.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'circle',
            'area_size_meters' => 30.48,
            'requires_sight' => true,
        ],
        'notes' => 'L’incantesimo richiede anche tiri salvezza su Costituzione per mantenere la concentrazione nell’area.',

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Terremoto',
                'application_type' => 'special',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Scuote violentemente il terreno, apre crepacci e danneggia le strutture in una vasta area.',
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
                        'dice_count' => 5,
                        'die_size' => 6,
                        'flat_bonus' => 0,
                    ],
                ],
                'condition' => 'Solo per una struttura che crolla: una creatura entro metà della sua altezza effettua un TS Destrezza, metà danno se riesce.',
            ],
        ],
    ]),

    $spell([
        'key' => 'tsunami',
        'name' => 'Tsunami',
        'school_key' => 'conjuration',
        'page' => 288,
        'casting_time_type' => 'minute',
        'range_type' => 'sight',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'round',
        'duration_value' => 6,
        'concentration' => true,
        'saving_throw' => 'FOR',
        'save_success_damage' => 'half',
        'description' => 'Genera un immenso muro d’acqua che avanza, travolge le creature e perde altezza e potenza a ogni round.',
        'target' => [
            'target_type' => 'area',
            'area_shape' => 'wall',
            'area_size_meters' => 91.44,
            'area_secondary_size_meters' => 15.24,
            'requires_sight' => true,
            'notes' => 'Il muro può essere lungo e alto fino a 91,44 metri e spesso fino a 15,24 metri.',
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Tsunami',
                'application_type' => 'failed_save',
                'target_scope' => 'area',
                'ends_with_source' => true,
                'description' => 'Genera un immenso muro d’acqua che avanza, travolge le creature e perde altezza e potenza a ogni round.',
                'sort_order' => 1,
                'durations' => [
                    [
                        'key' => 'spell_duration',
                        'duration_type' => 'fixed',
                        'notes' => 'Segue la durata e l’eventuale concentrazione dell’incantesimo; valgono le interruzioni specifiche e gli aumenti di durata con lo slot.',
                        'duration_value' => 6,
                        'duration_unit' => 'round',
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
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'notes' => 'Tiro salvezza riuscito: metà dei danni, arrotondata per difetto; la formula indica il danno pieno.',
                        'condition' => 'Solo quando compare l’onda: TS Forza, metà del danno se riesce.',
                    ],
                    [
                        'key' => 'advancing_wave',
                        'damage_type' => 'Contundente',
                        'is_primary' => false,
                        'sort_order' => 2,
                        'dice_count' => 5,
                        'die_size' => 10,
                        'flat_bonus' => 0,
                        'condition' => 'Nei round successivi, quando l’onda si muove nello spazio di una creatura Enorme o inferiore: TS Forza, nessun danno se riesce.',
                        'scalings' => [
                            [
                                'key' => 'following_rounds',
                                'target_field' => 'dice_count',
                                'source_type' => 'other',
                                'operation' => 'set',
                                'multiplier' => -1,
                                'flat_value' => 7,
                                'minimum_source' => 2,
                                'maximum_source' => 6,
                                'minimum_result' => 1,
                                'notes' => 'Input: numero del round dell’incantesimo (2–6); danni 5d10, 4d10, 3d10, 2d10, 1d10.',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]),

    $spell([
        'key' => 'mind_blank',
        'name' => 'Vuoto Mentale',
        'school_key' => 'abjuration',
        'page' => 289,
        'range_type' => 'touch',
        'verbal_component' => true,
        'somatic_component' => true,
        'duration_type' => 'hour',
        'duration_value' => 24,
        'description' => 'Protegge una creatura consenziente da danni psichici, divinazione, lettura del pensiero e condizione di affascinato.',
        'target' => [
            'target_type' => 'creature',
            'target_count' => 1,
        ],

        //Effetti strutturati: formule, condizioni e progressioni.
        'effects' => [
            [
                'key' => 'spell_effect',
                'name' => 'Vuoto Mentale',
                'application_type' => 'special',
                'target_scope' => 'target',
                'ends_with_source' => true,
                'description' => 'Protegge una creatura consenziente da danni psichici, divinazione, lettura del pensiero e condizione di affascinato.',
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
            ],
        ],
    ]),
];
