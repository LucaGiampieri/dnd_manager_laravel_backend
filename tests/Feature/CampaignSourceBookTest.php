<?php

use App\Models\CampaignSourceBook;
use App\Models\SourceBook;
use App\Models\User;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce il regolamento e i manuali richiesti dai test
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea il regolamento e il catalogo dei manuali
    $this->seed([
        RulesetSeeder::class,
        SourceBookSeeder::class,
    ]);
});

//Verifica l'attivazione di un manuale per una campagna
it('rende disponibile un manuale in una campagna', function () {
    //Crea la campagna attraverso l'utente proprietario
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna con Tasha',
            'description' => null,
        ]);

    //Recupera il manuale di Tasha
    $tasha = SourceBook::query()
        ->where('slug', 'tcoe-2020')
        ->firstOrFail();

    //Rende il manuale disponibile nella campagna
    $campaign->sourceBooks()->attach(
        $tasha->id,
        [
            'enabled' => true,
            'notes' => 'Manuale autorizzato dal Dungeon Master.',
        ]
    );

    //Ricarica la relazione dopo l'inserimento
    $campaign->load('sourceBooks');

    //Recupera il manuale collegato alla campagna
    $attachedSourceBook = $campaign->sourceBooks
        ->firstWhere('slug', 'tcoe-2020');

    //Verifica che il manuale sia stato recuperato
    expect($attachedSourceBook)->not->toBeNull();

    //Verifica l'utilizzo del modello pivot personalizzato
    expect($attachedSourceBook->pivot)
        ->toBeInstanceOf(CampaignSourceBook::class);

    //Verifica il booleano e le note salvate nel collegamento
    expect($attachedSourceBook->pivot->enabled)->toBeTrue()
        ->and($attachedSourceBook->pivot->notes)
        ->toBe('Manuale autorizzato dal Dungeon Master.');

    //Verifica la relazione inversa dal manuale alla campagna
    expect(
        $tasha->campaigns()
            ->whereKey($campaign->id)
            ->exists()
    )->toBeTrue();
});

//Verifica che un manuale possa essere disattivato temporaneamente
it('mantiene un manuale disattivato nella configurazione', function () {
    //Crea una campagna di riferimento
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna con manuale disattivato',
            'description' => null,
        ]);

    //Recupera il manuale di Tasha
    $tasha = SourceBook::query()
        ->where('slug', 'tcoe-2020')
        ->firstOrFail();

    //Collega il manuale lasciandolo disattivato
    $campaign->sourceBooks()->attach(
        $tasha->id,
        [
            'enabled' => false,
            'notes' => 'Manuale temporaneamente non utilizzabile.',
        ]
    );

    //Recupera nuovamente il manuale collegato
    $attachedSourceBook = $campaign->sourceBooks()
        ->whereKey($tasha->id)
        ->firstOrFail();

    //Verifica che il valore sia convertito in booleano
    expect($attachedSourceBook->pivot->enabled)->toBeFalse();

    //Verifica che non venga contato tra i manuali abilitati
    expect(
        $campaign->sourceBooks()
            ->wherePivot('enabled', true)
            ->count()
    )->toBe(0);
});

//Verifica il vincolo univoco del collegamento
it('rifiuta lo stesso manuale duplicato nella campagna', function () {
    //Crea una campagna di riferimento
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna senza manuali duplicati',
            'description' => null,
        ]);

    //Recupera il manuale da collegare
    $tasha = SourceBook::query()
        ->where('slug', 'tcoe-2020')
        ->firstOrFail();

    //Inserisce il primo collegamento valido
    $campaign->sourceBooks()->attach(
        $tasha->id,
        [
            'enabled' => true,
        ]
    );

    //Verifica che lo stesso collegamento non possa essere duplicato
    expect(
        fn () => $campaign->sourceBooks()->attach(
            $tasha->id,
            [
                'enabled' => true,
            ]
        )
    )->toThrow(QueryException::class);
});

//Verifica la cancellazione a cascata del collegamento
it('elimina i manuali associati quando cancella la campagna', function () {
    //Crea la campagna che verrà eliminata
    $campaign = User::factory()
        ->create()
        ->campaigns()
        ->create([
            'name' => 'Campagna da eliminare',
            'description' => null,
        ]);

    //Recupera il manuale da collegare
    $tasha = SourceBook::query()
        ->where('slug', 'tcoe-2020')
        ->firstOrFail();

    //Collega il manuale alla campagna
    $campaign->sourceBooks()->attach(
        $tasha->id,
        [
            'enabled' => true,
        ]
    );

    //Memorizza l'identificativo del collegamento
    $pivotId = DB::table('campaign_source_books')
        ->value('id');

    //Cancella la campagna
    $campaign->delete();

    //Verifica che il collegamento sia stato eliminato
    expect(
        DB::table('campaign_source_books')
            ->where('id', $pivotId)
            ->exists()
    )->toBeFalse();

    //Verifica che il manuale sia rimasto nel catalogo
    expect(
        SourceBook::query()
            ->whereKey($tasha->id)
            ->exists()
    )->toBeTrue();
});
