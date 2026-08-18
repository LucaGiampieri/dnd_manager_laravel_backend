<?php

use App\Models\Condition;
use App\Models\ConditionLevel;
use Database\Seeders\ConditionSeeder;
use Database\Seeders\RulesetSeeder;

test('il seeder crea le condizioni e i livelli dello sfinimento senza duplicati', function () {
    //Crea prima il regolamento necessario alle condizioni
    //ed esegue una prima volta il seeder delle condizioni
    $this->seed(RulesetSeeder::class);
    $this->seed(ConditionSeeder::class);

    //Ripete entrambi i seeder per verificare
    //che non vengano creati record duplicati
    $this->seed(RulesetSeeder::class);
    $this->seed(ConditionSeeder::class);

    //Verifica che esistano esattamente quindici condizioni
    expect(Condition::count())->toBe(15);

    //Verifica che esistano esattamente sei livelli di Sfinimento
    expect(ConditionLevel::count())->toBe(6);

    //Recupera la condizione progressiva dello Sfinimento
    $exhaustion = Condition::query()
        ->where('key', 'exhaustion')
        ->firstOrFail();

    //Relazione molti-a-uno (BelongsTo):
    //verifica che lo Sfinimento appartenga al regolamento corretto
    expect($exhaustion->ruleset->key)->toBe('dnd5e_2014');

    //Verifica che lo Sfinimento sia riconosciuto
    //come una condizione basata su livelli
    expect($exhaustion->is_level_based)->toBeTrue();

    //Verifica che il livello massimo previsto sia sei
    expect($exhaustion->maximum_level)->toBe(6);

    //Relazione uno-a-molti (HasMany):
    //verifica che siano presenti tutti i livelli nell’ordine corretto
    expect($exhaustion->levels->pluck('level')->all())
        ->toBe([1, 2, 3, 4, 5, 6]);

    //Verifica che soltanto il sesto livello sia terminale
    expect(
        $exhaustion->levels
            ->where('is_terminal', true)
            ->pluck('level')
            ->all()
    )->toBe([6]);
});
