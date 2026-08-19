<?php

use App\Models\ChallengeRating;
use App\Models\CreatureStatBlock;
use Database\Seeders\ChallengeRatingSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Support\Facades\Schema;

//Verifica l'utilizzo del grado di sfida da parte degli stat block
it('collega uno stat block al grado di sfida e calcola i valori', function () {
    //Crea il regolamento e il catalogo dei gradi di sfida
    $this->seed([
        RulesetSeeder::class,
        ChallengeRatingSeeder::class,
    ]);

    //Recupera il grado di sfida 5
    $challengeRating = ChallengeRating::query()
        ->where('key', 'cr_5')
        ->firstOrFail();

    //Crea uno stat block senza valori personalizzati
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura di prova',
        'challenge_rating_id' => $challengeRating->id,
    ]);

    //Verifica la nuova struttura della tabella
    expect(
        Schema::hasColumns(
            'creature_stat_blocks',
            [
                'challenge_rating_id',
                'experience_points_override',
                'proficiency_bonus_override',
            ]
        )
    )->toBeTrue()
        ->and(
            Schema::hasColumn(
                'creature_stat_blocks',
                'challenge_rating'
            )
        )->toBeFalse()
        ->and(
            Schema::hasColumn(
                'creature_stat_blocks',
                'proficiency_bonus'
            )
        )->toBeFalse();

    //Verifica la relazione molti-a-uno con il grado di sfida
    expect($statBlock->challengeRating->is($challengeRating))
        ->toBeTrue();

    //Verifica la relazione inversa dal grado di sfida
    expect(
        $challengeRating
            ->creatureStatBlocks()
            ->whereKey($statBlock->id)
            ->exists()
    )->toBeTrue();

    //Verifica i valori calcolati dal grado di sfida
    expect($statBlock->proficiency_bonus)->toBe(3)
        ->and($statBlock->experience_points)->toBe(1800);
});

//Verifica la possibilità di gestire le eccezioni di uno stat block
it('usa gli override senza modificare il grado di sfida', function () {
    //Crea il regolamento e il catalogo dei gradi di sfida
    $this->seed([
        RulesetSeeder::class,
        ChallengeRatingSeeder::class,
    ]);

    //Recupera il grado di sfida 0
    $challengeRating = ChallengeRating::query()
        ->where('key', 'cr_0')
        ->firstOrFail();

    //Crea uno stat block con PE e bonus personalizzati
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura personalizzata di prova',
        'challenge_rating_id' => $challengeRating->id,
        'experience_points_override' => 10,
        'proficiency_bonus_override' => 4,
    ]);

    //Verifica che gli override abbiano la precedenza
    expect($statBlock->experience_points)->toBe(10)
        ->and($statBlock->proficiency_bonus)->toBe(4);

    //Verifica che i valori del catalogo non siano stati modificati
    expect($challengeRating->experience_points)->toBe(0)
        ->and($challengeRating->proficiency_bonus)->toBe(2);
});
