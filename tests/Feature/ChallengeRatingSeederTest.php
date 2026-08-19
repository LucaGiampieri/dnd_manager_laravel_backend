<?php

use App\Models\ChallengeRating;
use Database\Seeders\ChallengeRatingSeeder;
use Database\Seeders\RulesetSeeder;

//Verifica la creazione completa del catalogo dei gradi di sfida
it('crea tutti i gradi di sfida senza duplicati', function () {
    //Crea il regolamento richiesto dal catalogo
    $this->seed(RulesetSeeder::class);

    //Esegue due volte il seeder per verificarne l'idempotenza
    $this->seed(ChallengeRatingSeeder::class);
    $this->seed(ChallengeRatingSeeder::class);

    //Definisce tutti i valori ufficiali attesi
    $expectedChallengeRatings = [
        'cr_0' => ['0', '0.000', 2, 0],
        'cr_1_8' => ['1/8', '0.125', 2, 25],
        'cr_1_4' => ['1/4', '0.250', 2, 50],
        'cr_1_2' => ['1/2', '0.500', 2, 100],
        'cr_1' => ['1', '1.000', 2, 200],
        'cr_2' => ['2', '2.000', 2, 450],
        'cr_3' => ['3', '3.000', 2, 700],
        'cr_4' => ['4', '4.000', 2, 1100],
        'cr_5' => ['5', '5.000', 3, 1800],
        'cr_6' => ['6', '6.000', 3, 2300],
        'cr_7' => ['7', '7.000', 3, 2900],
        'cr_8' => ['8', '8.000', 3, 3900],
        'cr_9' => ['9', '9.000', 4, 5000],
        'cr_10' => ['10', '10.000', 4, 5900],
        'cr_11' => ['11', '11.000', 4, 7200],
        'cr_12' => ['12', '12.000', 4, 8400],
        'cr_13' => ['13', '13.000', 5, 10000],
        'cr_14' => ['14', '14.000', 5, 11500],
        'cr_15' => ['15', '15.000', 5, 13000],
        'cr_16' => ['16', '16.000', 5, 15000],
        'cr_17' => ['17', '17.000', 6, 18000],
        'cr_18' => ['18', '18.000', 6, 20000],
        'cr_19' => ['19', '19.000', 6, 22000],
        'cr_20' => ['20', '20.000', 6, 25000],
        'cr_21' => ['21', '21.000', 7, 33000],
        'cr_22' => ['22', '22.000', 7, 41000],
        'cr_23' => ['23', '23.000', 7, 50000],
        'cr_24' => ['24', '24.000', 7, 62000],
        'cr_25' => ['25', '25.000', 8, 75000],
        'cr_26' => ['26', '26.000', 8, 90000],
        'cr_27' => ['27', '27.000', 8, 105000],
        'cr_28' => ['28', '28.000', 8, 120000],
        'cr_29' => ['29', '29.000', 9, 135000],
        'cr_30' => ['30', '30.000', 9, 155000],
    ];

    //Recupera i gradi di sfida nel loro ordine ufficiale
    $challengeRatings = ChallengeRating::query()
        ->orderBy('sort_order')
        ->get()
        ->keyBy('key');

    //Verifica quantità, chiavi e ordine progressivo
    expect($challengeRatings)->toHaveCount(34)
        ->and($challengeRatings->keys()->all())
        ->toBe(array_keys($expectedChallengeRatings))
        ->and($challengeRatings->pluck('sort_order')->all())
        ->toBe(range(1, 34));

    //Verifica tutti i valori meccanici di ogni grado di sfida
    foreach (
        $expectedChallengeRatings as $key => [
            $label,
            $numericValue,
            $proficiencyBonus,
            $experiencePoints,
        ]
    ) {
        $challengeRating = $challengeRatings->get($key);

        expect($challengeRating->label)->toBe($label)
            ->and($challengeRating->numeric_value)
            ->toBe($numericValue)
            ->and($challengeRating->proficiency_bonus)
            ->toBe($proficiencyBonus)
            ->and($challengeRating->experience_points)
            ->toBe($experiencePoints);
    }

    //Verifica il collegamento con il regolamento corretto
    expect($challengeRatings->get('cr_30')->ruleset->key)
        ->toBe('dnd5e_2014');

    //Verifica la nota relativa all'eccezione dei mostri di GS 0
    expect($challengeRatings->get('cr_0')->notes)
        ->toContain('10 PE');
});
