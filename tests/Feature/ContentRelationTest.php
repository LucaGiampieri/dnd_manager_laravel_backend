<?php

use App\Models\ContentRelation;
use App\Models\CreatureTag;
use App\Models\CreatureType;
use Database\Seeders\CreatureTagSeeder;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ripristina il database di test prima di ogni test
uses(RefreshDatabase::class);

//Verifica una relazione polimorfica tra due contenuti differenti
it('collega due contenuti in entrambe le direzioni', function () {
    //Crea il regolamento, i tipi e i tag delle creature
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
        CreatureTagSeeder::class,
    ]);

    //Recupera il tag Demone
    $demon = CreatureTag::query()
        ->where('key', 'demon')
        ->firstOrFail();

    //Recupera il tipo di creatura Immondo
    $fiend = CreatureType::query()
        ->where('key', 'fiend')
        ->firstOrFail();

    //Crea una relazione in uscita dal tag verso il tipo
    $relation = $demon
        ->outgoingContentRelations()
        ->create([
            'related_content_type' => $fiend->getMorphClass(),
            'related_content_id' => $fiend->getKey(),
            'relation_type' => 'derived_from',
            'notes' => 'Relazione utilizzata soltanto come esempio nel test.',
        ]);

    //Verifica il contenuto da cui parte la relazione
    expect($relation->content->is($demon))->toBeTrue();

    //Verifica il contenuto verso cui punta la relazione
    expect($relation->relatedContent->is($fiend))->toBeTrue();

    //Verifica la relazione in uscita
    expect(
        $demon->outgoingContentRelations()->count()
    )->toBe(1);

    //Verifica la relazione in entrata
    expect(
        $fiend->incomingContentRelations()->count()
    )->toBe(1);

    //Verifica il tipo della relazione
    expect($relation->relation_type)->toBe('derived_from');

    //Verifica che il database impedisca la stessa relazione duplicata
    expect(
        fn () => $demon
            ->outgoingContentRelations()
            ->create([
                'related_content_type' => $fiend->getMorphClass(),
                'related_content_id' => $fiend->getKey(),
                'relation_type' => 'derived_from',
                'notes' => 'Tentativo di duplicazione.',
            ])
    )->toThrow(QueryException::class);
});

//Verifica la pulizia delle relazioni quando un contenuto viene eliminato
it('elimina le relazioni quando un contenuto viene cancellato', function () {
    //Crea il regolamento, i tipi e i tag delle creature
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
        CreatureTagSeeder::class,
    ]);

    //Recupera il tag Demone
    $demon = CreatureTag::query()
        ->where('key', 'demon')
        ->firstOrFail();

    //Recupera il tipo di creatura Immondo
    $fiend = CreatureType::query()
        ->where('key', 'fiend')
        ->firstOrFail();

    //Crea una relazione che parte da Demone
    $demon->outgoingContentRelations()->create([
        'related_content_type' => $fiend->getMorphClass(),
        'related_content_id' => $fiend->getKey(),
        'relation_type' => 'derived_from',
    ]);

    //Crea una relazione che arriva a Demone
    $fiend->outgoingContentRelations()->create([
        'related_content_type' => $demon->getMorphClass(),
        'related_content_id' => $demon->getKey(),
        'relation_type' => 'other',
    ]);

    //Verifica che entrambe le relazioni siano presenti
    expect(ContentRelation::query()->count())->toBe(2);

    //Elimina il contenuto collegato in entrambe le direzioni
    $demon->delete();

    //Verifica che non rimangano relazioni orfane
    expect(ContentRelation::query()->count())->toBe(0);
});
