<?php

use App\Models\OptionalRule;
use App\Models\SourceReference;
use Database\Seeders\OptionalRuleSeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara il regolamento e i manuali richiesti dal seeder
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea il regolamento principale
    $this->seed(RulesetSeeder::class);

    //Crea il catalogo dei manuali
    $this->seed(SourceBookSeeder::class);
});

//Verifica la creazione della regola opzionale
it('crea la personalizzazione dell origine senza duplicati', function () {
    /** @var \Tests\TestCase $this */

    //Esegue due volte il seeder per verificarne l'idempotenza
    $this->seed(OptionalRuleSeeder::class);
    $this->seed(OptionalRuleSeeder::class);

    //Recupera la regola opzionale
    $customizeOrigin = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    //Verifica che il seeder non abbia creato duplicati
    expect(OptionalRule::query()->count())->toBe(1);

    //Verifica i dati principali della regola
    expect($customizeOrigin->name)
        ->toBe('Personalizzazione dell’origine')
        ->and($customizeOrigin->category)
        ->toBe('character_creation')
        ->and($customizeOrigin->default_enabled)
        ->toBeFalse()
        ->and($customizeOrigin->is_active)
        ->toBeTrue()
        ->and($customizeOrigin->sort_order)
        ->toBe(10);

    //Verifica la relazione molti-a-uno (BelongsTo):
    //la regola appartiene al regolamento 2014
    expect($customizeOrigin->ruleset->key)
        ->toBe('dnd5e_2014');

    //Verifica la relazione inversa uno-a-molti (HasMany):
    //il regolamento contiene la regola opzionale
    expect(
        $customizeOrigin
            ->ruleset
            ->optionalRules()
            ->whereKey($customizeOrigin->id)
            ->exists()
    )->toBeTrue();
});

//Verifica il riferimento al manuale di Tasha
it('collega la personalizzazione dell origine al manuale di Tasha', function () {
    /** @var \Tests\TestCase $this */

    //Inserisce la regola opzionale
    $this->seed(OptionalRuleSeeder::class);

    //Recupera la regola e il suo riferimento principale
    $customizeOrigin = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    $sourceReference = $customizeOrigin
        ->sourceReferences()
        ->where('key', 'tcoe_2020_customize_origin')
        ->firstOrFail();

    //Verifica che esista un solo riferimento
    expect($customizeOrigin->sourceReferences)->toHaveCount(1);

    //Verifica il manuale collegato alla regola
    expect($sourceReference->sourceBook->slug)
        ->toBe('tcoe-2020')
        ->and($sourceReference->sourceBook->abbreviation)
        ->toBe('TCoE');

    //Verifica la posizione della regola nel manuale
    expect($sourceReference->reference_type)
        ->toBe('definition')
        ->and($sourceReference->page_start)
        ->toBe(7)
        ->and($sourceReference->page_end)
        ->toBe(8)
        ->and($sourceReference->section)
        ->toContain('Personalizzare la propria origine');

    //Verifica che sia il riferimento principale
    expect($sourceReference->is_primary)->toBeTrue()
        ->and($sourceReference->sort_order)->toBe(1);

    //Verifica la relazione polimorfica inversa (MorphTo):
    //il riferimento appartiene alla regola opzionale
    expect($sourceReference->sourceable->is($customizeOrigin))
        ->toBeTrue();
});

//Verifica la protezione del testo ufficiale
it('mantiene privato il testo ufficiale della regola opzionale', function () {
    /** @var \Tests\TestCase $this */

    //Inserisce la regola opzionale
    $this->seed(OptionalRuleSeeder::class);

    //Recupera il riferimento alla fonte
    $sourceReference = SourceReference::query()
        ->where('key', 'tcoe_2020_customize_origin')
        ->firstOrFail();

    //Verifica che il seeder non contenga il testo ufficiale
    expect($sourceReference->official_text)->toBeNull();

    //Verifica che il campo non sia esposto pubblicamente
    expect($sourceReference->toArray())
        ->not->toHaveKey('official_text');
});

//Verifica la cancellazione dei riferimenti insieme alla regola
it('elimina i riferimenti quando la regola opzionale viene cancellata', function () {
    /** @var \Tests\TestCase $this */

    //Inserisce la regola opzionale
    $this->seed(OptionalRuleSeeder::class);

    //Recupera la regola e il riferimento collegato
    $customizeOrigin = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    $sourceReference = $customizeOrigin
        ->sourceReferences()
        ->firstOrFail();

    //Memorizza l'identificativo prima della cancellazione
    $sourceReferenceId = $sourceReference->id;

    //Cancella la regola opzionale
    $customizeOrigin->delete();

    //Verifica la cancellazione del riferimento polimorfico
    expect(
        SourceReference::query()
            ->whereKey($sourceReferenceId)
            ->exists()
    )->toBeFalse();
});
