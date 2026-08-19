<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class ChallengeRatingSeeder extends Seeder
{
    //Inserisce i gradi di sfida previsti dal regolamento D&D 5e 2014
    public function run(): void
    {
        //Recupera il regolamento al quale appartengono i gradi di sfida
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Definisce valore numerico, bonus di competenza e PE di ogni GS
        $challengeRatings = [
            [
                'key' => 'cr_0',
                'label' => '0',
                'numeric_value' => '0.000',
                'proficiency_bonus' => 2,
                'experience_points' => 0,
                'sort_order' => 1,
                'notes' => 'Il valore base è 0 PE. Uno stat block di GS 0 dotato di attacchi efficaci può utilizzare un override di 10 PE.',
            ],
            [
                'key' => 'cr_1_8',
                'label' => '1/8',
                'numeric_value' => '0.125',
                'proficiency_bonus' => 2,
                'experience_points' => 25,
                'sort_order' => 2,
                'notes' => null,
            ],
            [
                'key' => 'cr_1_4',
                'label' => '1/4',
                'numeric_value' => '0.250',
                'proficiency_bonus' => 2,
                'experience_points' => 50,
                'sort_order' => 3,
                'notes' => null,
            ],
            [
                'key' => 'cr_1_2',
                'label' => '1/2',
                'numeric_value' => '0.500',
                'proficiency_bonus' => 2,
                'experience_points' => 100,
                'sort_order' => 4,
                'notes' => null,
            ],
            [
                'key' => 'cr_1',
                'label' => '1',
                'numeric_value' => '1.000',
                'proficiency_bonus' => 2,
                'experience_points' => 200,
                'sort_order' => 5,
                'notes' => null,
            ],
            [
                'key' => 'cr_2',
                'label' => '2',
                'numeric_value' => '2.000',
                'proficiency_bonus' => 2,
                'experience_points' => 450,
                'sort_order' => 6,
                'notes' => null,
            ],
            [
                'key' => 'cr_3',
                'label' => '3',
                'numeric_value' => '3.000',
                'proficiency_bonus' => 2,
                'experience_points' => 700,
                'sort_order' => 7,
                'notes' => null,
            ],
            [
                'key' => 'cr_4',
                'label' => '4',
                'numeric_value' => '4.000',
                'proficiency_bonus' => 2,
                'experience_points' => 1100,
                'sort_order' => 8,
                'notes' => null,
            ],
            [
                'key' => 'cr_5',
                'label' => '5',
                'numeric_value' => '5.000',
                'proficiency_bonus' => 3,
                'experience_points' => 1800,
                'sort_order' => 9,
                'notes' => null,
            ],
            [
                'key' => 'cr_6',
                'label' => '6',
                'numeric_value' => '6.000',
                'proficiency_bonus' => 3,
                'experience_points' => 2300,
                'sort_order' => 10,
                'notes' => null,
            ],
            [
                'key' => 'cr_7',
                'label' => '7',
                'numeric_value' => '7.000',
                'proficiency_bonus' => 3,
                'experience_points' => 2900,
                'sort_order' => 11,
                'notes' => null,
            ],
            [
                'key' => 'cr_8',
                'label' => '8',
                'numeric_value' => '8.000',
                'proficiency_bonus' => 3,
                'experience_points' => 3900,
                'sort_order' => 12,
                'notes' => null,
            ],
            [
                'key' => 'cr_9',
                'label' => '9',
                'numeric_value' => '9.000',
                'proficiency_bonus' => 4,
                'experience_points' => 5000,
                'sort_order' => 13,
                'notes' => null,
            ],
            [
                'key' => 'cr_10',
                'label' => '10',
                'numeric_value' => '10.000',
                'proficiency_bonus' => 4,
                'experience_points' => 5900,
                'sort_order' => 14,
                'notes' => null,
            ],
            [
                'key' => 'cr_11',
                'label' => '11',
                'numeric_value' => '11.000',
                'proficiency_bonus' => 4,
                'experience_points' => 7200,
                'sort_order' => 15,
                'notes' => null,
            ],
            [
                'key' => 'cr_12',
                'label' => '12',
                'numeric_value' => '12.000',
                'proficiency_bonus' => 4,
                'experience_points' => 8400,
                'sort_order' => 16,
                'notes' => null,
            ],
            [
                'key' => 'cr_13',
                'label' => '13',
                'numeric_value' => '13.000',
                'proficiency_bonus' => 5,
                'experience_points' => 10000,
                'sort_order' => 17,
                'notes' => null,
            ],
            [
                'key' => 'cr_14',
                'label' => '14',
                'numeric_value' => '14.000',
                'proficiency_bonus' => 5,
                'experience_points' => 11500,
                'sort_order' => 18,
                'notes' => null,
            ],
            [
                'key' => 'cr_15',
                'label' => '15',
                'numeric_value' => '15.000',
                'proficiency_bonus' => 5,
                'experience_points' => 13000,
                'sort_order' => 19,
                'notes' => null,
            ],
            [
                'key' => 'cr_16',
                'label' => '16',
                'numeric_value' => '16.000',
                'proficiency_bonus' => 5,
                'experience_points' => 15000,
                'sort_order' => 20,
                'notes' => null,
            ],
            [
                'key' => 'cr_17',
                'label' => '17',
                'numeric_value' => '17.000',
                'proficiency_bonus' => 6,
                'experience_points' => 18000,
                'sort_order' => 21,
                'notes' => null,
            ],
            [
                'key' => 'cr_18',
                'label' => '18',
                'numeric_value' => '18.000',
                'proficiency_bonus' => 6,
                'experience_points' => 20000,
                'sort_order' => 22,
                'notes' => null,
            ],
            [
                'key' => 'cr_19',
                'label' => '19',
                'numeric_value' => '19.000',
                'proficiency_bonus' => 6,
                'experience_points' => 22000,
                'sort_order' => 23,
                'notes' => null,
            ],
            [
                'key' => 'cr_20',
                'label' => '20',
                'numeric_value' => '20.000',
                'proficiency_bonus' => 6,
                'experience_points' => 25000,
                'sort_order' => 24,
                'notes' => null,
            ],
            [
                'key' => 'cr_21',
                'label' => '21',
                'numeric_value' => '21.000',
                'proficiency_bonus' => 7,
                'experience_points' => 33000,
                'sort_order' => 25,
                'notes' => null,
            ],
            [
                'key' => 'cr_22',
                'label' => '22',
                'numeric_value' => '22.000',
                'proficiency_bonus' => 7,
                'experience_points' => 41000,
                'sort_order' => 26,
                'notes' => null,
            ],
            [
                'key' => 'cr_23',
                'label' => '23',
                'numeric_value' => '23.000',
                'proficiency_bonus' => 7,
                'experience_points' => 50000,
                'sort_order' => 27,
                'notes' => null,
            ],
            [
                'key' => 'cr_24',
                'label' => '24',
                'numeric_value' => '24.000',
                'proficiency_bonus' => 7,
                'experience_points' => 62000,
                'sort_order' => 28,
                'notes' => null,
            ],
            [
                'key' => 'cr_25',
                'label' => '25',
                'numeric_value' => '25.000',
                'proficiency_bonus' => 8,
                'experience_points' => 75000,
                'sort_order' => 29,
                'notes' => null,
            ],
            [
                'key' => 'cr_26',
                'label' => '26',
                'numeric_value' => '26.000',
                'proficiency_bonus' => 8,
                'experience_points' => 90000,
                'sort_order' => 30,
                'notes' => null,
            ],
            [
                'key' => 'cr_27',
                'label' => '27',
                'numeric_value' => '27.000',
                'proficiency_bonus' => 8,
                'experience_points' => 105000,
                'sort_order' => 31,
                'notes' => null,
            ],
            [
                'key' => 'cr_28',
                'label' => '28',
                'numeric_value' => '28.000',
                'proficiency_bonus' => 8,
                'experience_points' => 120000,
                'sort_order' => 32,
                'notes' => null,
            ],
            [
                'key' => 'cr_29',
                'label' => '29',
                'numeric_value' => '29.000',
                'proficiency_bonus' => 9,
                'experience_points' => 135000,
                'sort_order' => 33,
                'notes' => null,
            ],
            [
                'key' => 'cr_30',
                'label' => '30',
                'numeric_value' => '30.000',
                'proficiency_bonus' => 9,
                'experience_points' => 155000,
                'sort_order' => 34,
                'notes' => null,
            ],
        ];

        //Crea oppure aggiorna ogni grado di sfida senza duplicarlo
        foreach ($challengeRatings as $challengeRating) {
            $ruleset->challengeRatings()->updateOrCreate(
                [
                    'key' => $challengeRating['key'],
                ],
                [
                    'label' => $challengeRating['label'],
                    'numeric_value' =>
                        $challengeRating['numeric_value'],
                    'proficiency_bonus' =>
                        $challengeRating['proficiency_bonus'],
                    'experience_points' =>
                        $challengeRating['experience_points'],
                    'sort_order' =>
                        $challengeRating['sort_order'],
                    'notes' => $challengeRating['notes'],
                ]
            );
        }
    }
}
