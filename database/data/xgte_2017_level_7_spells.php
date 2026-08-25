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
    ]),
];
