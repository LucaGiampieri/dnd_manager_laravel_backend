<?php

use App\Models\OptionalRule;
use App\Models\SourceBook;
use App\Models\User;
use Database\Seeders\OptionalRuleSeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce i cataloghi richiesti dai test
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea il regolamento, i manuali e le regole opzionali
    $this->seed([
        RulesetSeeder::class,
        SourceBookSeeder::class,
        OptionalRuleSeeder::class,
    ]);
});

//Verifica che siano necessari sia la regola sia il manuale
it('rende disponibile la regola soltanto insieme al suo manuale', function () {
    //Crea la campagna attraverso il suo proprietario
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna con regole opzionali',
            'description' => null,
        ]);

    //Recupera la regola di personalizzazione dell'origine
    $optionalRule = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    //Recupera il manuale di Tasha
    $tasha = SourceBook::query()
        ->where('slug', 'tcoe-2020')
        ->firstOrFail();

    //Verifica che inizialmente la regola non sia disponibile
    expect($optionalRule->isEnabledFor($campaign))->toBeFalse()
        ->and($optionalRule->isAvailableFor($campaign))
        ->toBeFalse();

    //Attiva soltanto la regola opzionale
    $campaign->optionalRules()->attach(
        $optionalRule->id,
        [
            'enabled' => true,
        ]
    );

    //Verifica che la regola sia attivata ma ancora indisponibile
    expect($optionalRule->isEnabledFor($campaign))->toBeTrue()
        ->and($optionalRule->isAvailableFor($campaign))
        ->toBeFalse();

    //Collega Tasha lasciandolo disattivato
    $campaign->sourceBooks()->attach(
        $tasha->id,
        [
            'enabled' => false,
        ]
    );

    //Verifica che un manuale disattivato non sia sufficiente
    expect($optionalRule->isAvailableFor($campaign))
        ->toBeFalse();

    //Abilita il manuale di Tasha nella campagna
    $campaign->sourceBooks()->updateExistingPivot(
        $tasha->id,
        [
            'enabled' => true,
        ]
    );

    //Verifica che regola e manuale attivi la rendano disponibile
    expect($optionalRule->isAvailableFor($campaign))
        ->toBeTrue();

    //Disattiva nuovamente la regola opzionale
    $campaign->optionalRules()->updateExistingPivot(
        $optionalRule->id,
        [
            'enabled' => false,
        ]
    );

    //Verifica che la regola non sia più disponibile
    expect($optionalRule->isEnabledFor($campaign))->toBeFalse()
        ->and($optionalRule->isAvailableFor($campaign))
        ->toBeFalse();
});

//Verifica lo stato globale della regola nel catalogo
it('rifiuta una regola disattivata nel catalogo', function () {
    //Crea una campagna di riferimento
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna con regola inattiva',
            'description' => null,
        ]);

    //Recupera la regola opzionale
    $optionalRule = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    //Recupera il manuale principale della regola
    $tasha = SourceBook::query()
        ->where('slug', 'tcoe-2020')
        ->firstOrFail();

    //Attiva sia la regola sia il manuale
    $campaign->optionalRules()->attach(
        $optionalRule->id,
        [
            'enabled' => true,
        ]
    );

    $campaign->sourceBooks()->attach(
        $tasha->id,
        [
            'enabled' => true,
        ]
    );

    //Verifica che la regola sia inizialmente disponibile
    expect($optionalRule->isAvailableFor($campaign))
        ->toBeTrue();

    //Disattiva la regola nel catalogo generale
    $optionalRule->update([
        'is_active' => false,
    ]);

    //Verifica che la campagna non possa più utilizzarla
    expect($optionalRule->isAvailableFor($campaign))
        ->toBeFalse();
});

//Verifica lo stato globale del manuale nel catalogo
it('rifiuta una regola proveniente da un manuale inattivo', function () {
    //Crea una campagna di riferimento
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna con manuale inattivo',
            'description' => null,
        ]);

    //Recupera la regola opzionale
    $optionalRule = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    //Recupera il manuale di Tasha
    $tasha = SourceBook::query()
        ->where('slug', 'tcoe-2020')
        ->firstOrFail();

    //Attiva la regola nella campagna
    $campaign->optionalRules()->attach(
        $optionalRule->id,
        [
            'enabled' => true,
        ]
    );

    //Abilita il manuale nella campagna
    $campaign->sourceBooks()->attach(
        $tasha->id,
        [
            'enabled' => true,
        ]
    );

    //Disattiva il manuale nel catalogo generale
    $tasha->update([
        'is_active' => false,
    ]);

    //Verifica che la regola non sia più disponibile
    expect($optionalRule->isAvailableFor($campaign))
        ->toBeFalse();
});
