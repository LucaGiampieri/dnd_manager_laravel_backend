<?php

use App\Models\EffectDefinitionMovementCostModifier;
use App\Models\Ruleset;
use Database\Seeders\MovementCostRuleSeeder;
use Database\Seeders\MovementTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per isolare completamente le regole di movimento
uses(RefreshDatabase::class);

it('crea le regole base dei costi di movimento senza duplicati', function () {
    //Crea il regolamento e i tipi di movimento
    //necessari al seeder delle regole
    $this->seed(RulesetSeeder::class);
    $this->seed(MovementTypeSeeder::class);

    //Esegue due volte il seeder per verificare
    //che non vengano creati modificatori duplicati
    $this->seed(MovementCostRuleSeeder::class);
    $this->seed(MovementCostRuleSeeder::class);

    //Recupera il regolamento D&D 5e 2014
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Relazione uno-a-molti polimorfica (MorphMany):
    //recupera la definizione delle regole di movimento
    $effect = $ruleset->effectDefinitions()
        ->where('key', 'core_movement_cost_rules')
        ->firstOrFail();

    //Relazione uno-a-molti (HasMany):
    //recupera tutti i modificatori associati alla definizione
    $modifiers = $effect->movementCostModifiers()
        ->orderBy('context_key')
        ->get();

    //Recupera il costo aggiuntivo dello strisciare
    $crawling = $modifiers->firstWhere(
        'context_key',
        'crawling'
    );

    //Recupera il costo aggiuntivo del terreno difficile
    $difficultTerrain = $modifiers->firstWhere(
        'context_key',
        'difficult_terrain'
    );

    //Recupera il costo aggiuntivo dello scalare
    $climbing = $modifiers->firstWhere(
        'context_key',
        'climbing'
    );

    //Recupera il costo aggiuntivo del nuotare
    $swimming = $modifiers->firstWhere(
        'context_key',
        'swimming'
    );

    //Recupera il costo necessario per alzarsi da prono
    $standing = $modifiers->firstWhere(
        'context_key',
        'standing_from_prone'
    );

    //Verifica che siano stati creati esattamente sei modificatori
    expect(
        EffectDefinitionMovementCostModifier::count()
    )->toBe(6);

    //Verifica le chiavi dei contesti in ordine alfabetico
    expect($modifiers->pluck('context_key')->all())->toBe([
        'climbing',
        'crawling',
        'difficult_terrain',
        'squeezing',
        'standing_from_prone',
        'swimming',
    ]);

    //Verifica che strisciare aggiunga un costo
    //per ogni unità di distanza percorsa
    expect($crawling->cost_basis)->toBe('per_distance');
    expect($crawling->operation)->toBe('add');
    expect($crawling->value)->toBe('1.000');

    //Verifica che il terreno difficile aggiunga
    //un’ulteriore unità al costo del movimento
    expect($difficultTerrain->value)->toBe('1.000');

    //Relazione molti-a-uno (BelongsTo):
    //verifica che una velocità di scalata annulli il costo aggiuntivo
    expect(
        $climbing->waivedByMovementType->name
    )->toBe('Scalare');

    //Relazione molti-a-uno (BelongsTo):
    //verifica che una velocità di nuoto annulli il costo aggiuntivo
    expect(
        $swimming->waivedByMovementType->name
    )->toBe('Nuotare');

    //Verifica che alzarsi da prono utilizzi
    //la metà della velocità totale della creatura
    expect(
        $standing->cost_basis
    )->toBe('total_speed_fraction');

    expect($standing->value)->toBe('0.500');

    //Verifica che percorrere una distanza strisciando
    //costi il doppio della normale distanza
    expect(
        1 + (float) $crawling->value
    )->toBe(2.0);

    //Verifica che strisciare su terreno difficile
    //porti il costo complessivo a tre unità
    expect(
        1
        + (float) $crawling->value
        + (float) $difficultTerrain->value
    )->toBe(3.0);
});
