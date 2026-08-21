<?php

use App\Models\CampaignOptionalRule;
use App\Models\OptionalRule;
use App\Models\User;
use Database\Seeders\OptionalRuleSeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce i cataloghi richiesti dalle regole opzionali
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea il regolamento, i manuali e le regole opzionali
    $this->seed([
        RulesetSeeder::class,
        SourceBookSeeder::class,
        OptionalRuleSeeder::class,
    ]);
});

//Verifica la relazione tra la campagna e il proprietario
it('collega una campagna al proprietario', function () {
    //Crea l'utente che possiede la campagna
    $owner = User::factory()->create();

    //Crea la campagna attraverso la relazione con l'utente
    $campaign = $owner->campaigns()->create([
        'name' => 'Campagna di prova',
        'description' => 'Campagna creata soltanto per il test.',
    ]);

    //Recupera la regola opzionale disponibile
    $optionalRule = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    //Verifica il collegamento dalla campagna al proprietario
    expect($campaign->owner->is($owner))->toBeTrue();

    //Verifica la relazione inversa dall'utente alla campagna
    expect(
        $owner->campaigns()
            ->whereKey($campaign->id)
            ->exists()
    )->toBeTrue();

    //Verifica che la regola non sia attivata automaticamente
    expect($campaign->optionalRules()->count())->toBe(0)
        ->and($optionalRule->default_enabled)->toBeFalse();
});

//Verifica l'attivazione e la configurazione di una regola opzionale
it('attiva una regola opzionale per una campagna', function () {
    //Crea l'utente proprietario
    $owner = User::factory()->create();

    //Crea la campagna attraverso la relazione con l'utente
    $campaign = $owner->campaigns()->create([
        'name' => 'Campagna con regole opzionali',
        'description' => null,
    ]);

    //Recupera la regola di personalizzazione dell'origine
    $optionalRule = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    //Attiva la regola e registra la sua configurazione
    $campaign->optionalRules()->attach(
        $optionalRule->id,
        [
            'enabled' => true,
            'configuration' => [
                'maximum_ability_score' => 20,
                'require_distinct_destinations' => true,
            ],
            'notes' => 'Regola autorizzata per questa campagna.',
        ]
    );

    //Ricarica la relazione dopo l'inserimento
    $campaign->load('optionalRules');

    //Recupera la regola collegata alla campagna
    $attachedRule = $campaign->optionalRules
        ->firstWhere('key', 'customize_origin');

    //Verifica che la regola sia stata recuperata
    expect($attachedRule)->not->toBeNull();

    //Verifica l'utilizzo del modello pivot personalizzato
    expect($attachedRule->pivot)
        ->toBeInstanceOf(CampaignOptionalRule::class);

    //Verifica le conversioni del booleano e della configurazione JSON
    expect($attachedRule->pivot->enabled)->toBeTrue()
        ->and($attachedRule->pivot->configuration)->toBe([
            'maximum_ability_score' => 20,
            'require_distinct_destinations' => true,
        ])
        ->and($attachedRule->pivot->notes)
        ->toBe('Regola autorizzata per questa campagna.');

    //Verifica la relazione inversa dalla regola alla campagna
    expect(
        $optionalRule->campaigns()
            ->whereKey($campaign->id)
            ->exists()
    )->toBeTrue();
});

//Verifica il vincolo univoco del collegamento
it('rifiuta la stessa regola opzionale duplicata nella campagna', function () {
    //Crea una campagna di riferimento
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna senza duplicati',
            'description' => null,
        ]);

    //Recupera la regola opzionale da collegare
    $optionalRule = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    //Inserisce il primo collegamento valido
    $campaign->optionalRules()->attach(
        $optionalRule->id,
        [
            'enabled' => true,
        ]
    );

    //Verifica che lo stesso collegamento non possa essere duplicato
    expect(
        fn () => $campaign->optionalRules()->attach(
            $optionalRule->id,
            [
                'enabled' => true,
            ]
        )
    )->toThrow(QueryException::class);
});

//Verifica la cancellazione a cascata del collegamento
it('elimina le regole attivate insieme alla campagna', function () {
    //Crea la campagna che verrà eliminata
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna da eliminare',
            'description' => null,
        ]);

    //Recupera la regola opzionale da attivare
    $optionalRule = OptionalRule::query()
        ->where('key', 'customize_origin')
        ->firstOrFail();

    //Attiva la regola per la campagna
    $campaign->optionalRules()->attach(
        $optionalRule->id,
        [
            'enabled' => true,
        ]
    );

    //Memorizza l'identificativo del collegamento
    $pivotId = DB::table('campaign_optional_rules')
        ->value('id');

    //Cancella la campagna
    $campaign->delete();

    //Verifica che il collegamento sia stato eliminato
    expect(
        DB::table('campaign_optional_rules')
            ->where('id', $pivotId)
            ->exists()
    )->toBeFalse();

    //Verifica che la regola del catalogo sia ancora disponibile
    expect(
        OptionalRule::query()
            ->whereKey($optionalRule->id)
            ->exists()
    )->toBeTrue();
});
