<?php

use App\Models\CreatureStatBlock;
use App\Models\CreatureStatBlockHitPoint;
use Illuminate\Database\QueryException;

//Verifica formula, media e relazioni dei Punti Ferita
it('gestisce la formula standard dei Punti Ferita', function () {
    //Crea lo stat block utilizzato dal test
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura con Punti Ferita standard',
    ]);

    //Crea una formula equivalente a 3d8 + 3
    $hitPoints = $statBlock->hitPoints()->create([
        'average_hit_points' => 16,
        'hit_dice_count' => 3,
        'hit_die_size' => 8,
        'hit_dice_modifier' => 3,
    ]);

    //Verifica formula, media calcolata e media effettiva
    expect($hitPoints->hit_dice_formula)->toBe('3d8 + 3')
        ->and($hitPoints->calculated_average_hit_points)
        ->toBe(16)
        ->and($hitPoints->effective_average_hit_points)
        ->toBe(16);

    //Verifica la relazione dalla definizione allo stat block
    expect($hitPoints->creatureStatBlock->is($statBlock))
        ->toBeTrue();

    //Verifica relazione e attributi calcolati dello stat block
    $freshStatBlock = $statBlock->fresh();

    expect($freshStatBlock->hitPoints->is($hitPoints))
        ->toBeTrue()
        ->and($freshStatBlock->average_hit_points)
        ->toBe(16)
        ->and($freshStatBlock->hit_dice_formula)
        ->toBe('3d8 + 3');
});

//Verifica formule con malus e senza modificatore
it('calcola formule con bonus zero e modificatori negativi', function () {
    //Crea uno stat block con formula priva di modificatore
    $firstStatBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura senza modificatore',
    ]);

    $withoutModifier = $firstStatBlock->hitPoints()->create([
        'hit_dice_count' => 2,
        'hit_die_size' => 8,
    ]);

    //Verifica formula e media di 2d8
    expect($withoutModifier->hit_dice_formula)
        ->toBe('2d8')
        ->and($withoutModifier->calculated_average_hit_points)
        ->toBe(9)
        ->and($withoutModifier->effective_average_hit_points)
        ->toBe(9);

    //Crea uno stat block con modificatore negativo
    $secondStatBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura con modificatore negativo',
    ]);

    $withPenalty = $secondStatBlock->hitPoints()->create([
        'hit_dice_count' => 4,
        'hit_die_size' => 10,
        'hit_dice_modifier' => -4,
    ]);

    //Verifica formula e media di 4d10 - 4
    expect($withPenalty->hit_dice_formula)
        ->toBe('4d10 - 4')
        ->and($withPenalty->calculated_average_hit_points)
        ->toBe(18)
        ->and($withPenalty->effective_average_hit_points)
        ->toBe(18);
});

//Verifica PF fissi e formule particolari
it('gestisce Punti Ferita fissi e calcoli speciali', function () {
    //Crea uno stat block con un valore fisso
    $fixedStatBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura con PF fissi',
    ]);

    $fixedHitPoints = $fixedStatBlock->hitPoints()->create([
        'average_hit_points' => 5,
        'notes' => 'La creatura utilizza un valore fisso.',
    ]);

    //Verifica che il valore fisso sia utilizzato direttamente
    expect($fixedHitPoints->hit_dice_formula)->toBeNull()
        ->and($fixedHitPoints->calculated_average_hit_points)
        ->toBeNull()
        ->and($fixedHitPoints->effective_average_hit_points)
        ->toBe(5);

    //Crea uno stat block con un calcolo non standard
    $specialStatBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura con calcolo speciale',
    ]);

    $specialHitPoints = $specialStatBlock->hitPoints()->create([
        'special_calculation' =>
            'I PF dipendono da una capacità speciale.',
    ]);

    //Verifica che la formula speciale venga conservata
    expect($specialHitPoints->hit_dice_formula)
        ->toBe('I PF dipendono da una capacità speciale.')
        ->and($specialHitPoints->calculated_average_hit_points)
        ->toBeNull()
        ->and($specialHitPoints->effective_average_hit_points)
        ->toBeNull();
});

//Verifica i vincoli e la validazione dei Punti Ferita
it('rifiuta definizioni duplicate o non valide', function () {
    //Crea lo stat block utilizzato dal test
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura con PF controllati',
    ]);

    //Verifica che i PF medi non possano essere zero
    expect(
        fn () => $statBlock->hitPoints()->create([
            'average_hit_points' => 0,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che numero e tipo dei dadi siano inseparabili
    expect(
        fn () => $statBlock->hitPoints()->create([
            'hit_dice_count' => 2,
        ])
    )->toThrow(\InvalidArgumentException::class);

    expect(
        fn () => $statBlock->hitPoints()->create([
            'hit_die_size' => 8,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che il numero dei dadi sia positivo
    expect(
        fn () => $statBlock->hitPoints()->create([
            'hit_dice_count' => 0,
            'hit_die_size' => 8,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che il tipo di dado sia consentito
    expect(
        fn () => $statBlock->hitPoints()->create([
            'hit_dice_count' => 2,
            'hit_die_size' => 7,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che non sia possibile salvare una riga vuota
    expect(
        fn () => $statBlock->hitPoints()->create([])
    )->toThrow(\InvalidArgumentException::class);

    //Inserisce la prima definizione valida
    $statBlock->hitPoints()->create([
        'average_hit_points' => 10,
        'hit_dice_count' => 2,
        'hit_die_size' => 8,
        'hit_dice_modifier' => 1,
    ]);

    //Verifica il vincolo uno-a-uno dello stat block
    expect(
        fn () => $statBlock->hitPoints()->create([
            'average_hit_points' => 20,
        ])
    )->toThrow(QueryException::class);
});

//Verifica la cancellazione automatica dei Punti Ferita
it('elimina i Punti Ferita insieme allo stat block', function () {
    //Crea lo stat block e la sua definizione dei PF
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura da eliminare',
    ]);

    $hitPoints = $statBlock->hitPoints()->create([
        'average_hit_points' => 11,
        'hit_dice_count' => 2,
        'hit_die_size' => 8,
        'hit_dice_modifier' => 2,
    ]);

    //Verifica che la definizione sia stata salvata
    expect($hitPoints->exists)->toBeTrue();

    //Elimina lo stat block proprietario
    $statBlock->delete();

    //Verifica la cancellazione automatica dei PF
    expect(
        CreatureStatBlockHitPoint::query()
            ->whereKey($hitPoints->id)
            ->exists()
    )->toBeFalse();
});
