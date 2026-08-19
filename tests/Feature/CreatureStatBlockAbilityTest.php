<?php

use App\Models\Ability;
use App\Models\CreatureStatBlock;
use App\Models\CreatureStatBlockAbility;
use Database\Seeders\AbilitySeeder;
use Illuminate\Database\QueryException;

//Verifica i sei punteggi di caratteristica di uno stat block
it('gestisce i punteggi di caratteristica di uno stat block', function () {
    //Crea il catalogo delle sei caratteristiche
    $this->seed(AbilitySeeder::class);

    //Crea lo stat block utilizzato dal test
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura di prova',
    ]);

    //Definisce i punteggi da assegnare
    $scores = [
        'FOR' => 18,
        'DES' => 14,
        'COS' => 16,
        'INT' => 7,
        'SAG' => 12,
        'CAR' => 9,
    ];

    //Definisce i modificatori attesi
    $expectedModifiers = [
        'FOR' => 4,
        'DES' => 2,
        'COS' => 3,
        'INT' => -2,
        'SAG' => 1,
        'CAR' => -1,
    ];

    //Recupera gli identificativi delle caratteristiche
    $abilityIds = Ability::query()
        ->pluck('id', 'short_name');

    //Assegna ogni punteggio allo stat block
    foreach ($scores as $shortName => $score) {
        $statBlock->abilityScores()->create([
            'ability_id' => $abilityIds->get($shortName),
            'score' => $score,
        ]);
    }

    //Ricarica lo stat block con caratteristiche e punteggi
    $statBlock->load('abilityScores.ability');

    //Organizza i punteggi utilizzando le abbreviazioni
    $abilityScores = $statBlock->abilityScores
        ->mapWithKeys(
            fn (CreatureStatBlockAbility $abilityScore) => [
                $abilityScore->ability->short_name =>
                    $abilityScore,
            ]
        );

    //Verifica che siano presenti tutte le sei caratteristiche
    expect($abilityScores)->toHaveCount(6);

    //Verifica punteggio, modificatore e relazione
    foreach ($scores as $shortName => $score) {
        $abilityScore = $abilityScores->get($shortName);

        expect($abilityScore->score)->toBe($score)
            ->and($abilityScore->modifier)
            ->toBe($expectedModifiers[$shortName])
            ->and($abilityScore->creatureStatBlock->is($statBlock))
            ->toBeTrue();
    }

    //Verifica la relazione inversa dalla caratteristica
    $strength = Ability::query()
        ->where('short_name', 'FOR')
        ->firstOrFail();

    expect(
        $strength
            ->creatureStatBlockAbilities()
            ->where('creature_stat_block_id', $statBlock->id)
            ->exists()
    )->toBeTrue();
});

//Verifica tutti i modificatori possibili del regolamento
it('calcola correttamente i modificatori da 1 a 30', function () {
    //Definisce esplicitamente ogni modificatore atteso
    $expectedModifiers = [
        1 => -5,
        2 => -4,
        3 => -4,
        4 => -3,
        5 => -3,
        6 => -2,
        7 => -2,
        8 => -1,
        9 => -1,
        10 => 0,
        11 => 0,
        12 => 1,
        13 => 1,
        14 => 2,
        15 => 2,
        16 => 3,
        17 => 3,
        18 => 4,
        19 => 4,
        20 => 5,
        21 => 5,
        22 => 6,
        23 => 6,
        24 => 7,
        25 => 7,
        26 => 8,
        27 => 8,
        28 => 9,
        29 => 9,
        30 => 10,
    ];

    //Controlla il calcolo per ogni punteggio consentito
    foreach ($expectedModifiers as $score => $modifier) {
        $abilityScore = new CreatureStatBlockAbility([
            'score' => $score,
        ]);

        expect($abilityScore->modifier)->toBe($modifier);
    }
});

//Verifica i limiti e il vincolo di unicità
it('rifiuta punteggi non validi e caratteristiche duplicate', function () {
    //Crea le caratteristiche richieste dal test
    $this->seed(AbilitySeeder::class);

    //Recupera la caratteristica Forza
    $strength = Ability::query()
        ->where('short_name', 'FOR')
        ->firstOrFail();

    //Crea lo stat block utilizzato dal test
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura con valori controllati',
    ]);

    //Inserisce il primo punteggio valido di Forza
    $statBlock->abilityScores()->create([
        'ability_id' => $strength->id,
        'score' => 10,
    ]);

    //Verifica che la stessa caratteristica non sia duplicabile
    expect(
        fn () => $statBlock->abilityScores()->create([
            'ability_id' => $strength->id,
            'score' => 12,
        ])
    )->toThrow(QueryException::class);

    //Verifica che il punteggio zero venga rifiutato
    expect(
        fn () => CreatureStatBlockAbility::query()->create([
            'creature_stat_block_id' => $statBlock->id,
            'ability_id' => $strength->id,
            'score' => 0,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che un punteggio superiore a 30 venga rifiutato
    expect(
        fn () => CreatureStatBlockAbility::query()->create([
            'creature_stat_block_id' => $statBlock->id,
            'ability_id' => $strength->id,
            'score' => 31,
        ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica la cancellazione automatica dei punteggi
it('elimina i punteggi quando viene cancellato lo stat block', function () {
    //Crea il catalogo delle caratteristiche
    $this->seed(AbilitySeeder::class);

    //Recupera la caratteristica Forza
    $strength = Ability::query()
        ->where('short_name', 'FOR')
        ->firstOrFail();

    //Crea uno stat block con un punteggio associato
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura da eliminare',
    ]);

    $abilityScore = $statBlock->abilityScores()->create([
        'ability_id' => $strength->id,
        'score' => 15,
    ]);

    //Verifica che il punteggio sia stato salvato
    expect($abilityScore->exists)->toBeTrue();

    //Elimina lo stat block proprietario
    $statBlock->delete();

    //Verifica la cancellazione automatica del punteggio
    expect(
        CreatureStatBlockAbility::query()
            ->whereKey($abilityScore->id)
            ->exists()
    )->toBeFalse();
});
