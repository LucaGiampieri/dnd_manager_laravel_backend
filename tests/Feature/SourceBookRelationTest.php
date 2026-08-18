<?php

use App\Models\Ruleset;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ripristina il database di test prima di ogni test
uses(RefreshDatabase::class);

//Verifica il collegamento editoriale tra due pubblicazioni
it('collega una pubblicazione alla sua versione originale', function () {
    //Crea il regolamento necessario per i manuali
    $this->seed(RulesetSeeder::class);

    //Recupera il regolamento D&D 5e 2014
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Crea una pubblicazione originale fittizia
    $original = $ruleset->sourceBooks()->create([
        'title' => 'Manuale originale di prova',
        'original_title' => 'Original Test Book',
        'slug' => 'original-test-book',
        'abbreviation' => 'OTB',
        'type' => 'supplement',
        'edition' => '5e',
        'language' => 'en',
        'publisher' => 'Editore di prova',
        'is_official' => true,
        'is_playtest' => false,
        'is_active' => true,
    ]);

    //Crea una traduzione italiana fittizia
    $translation = $ruleset->sourceBooks()->create([
        'title' => 'Traduzione italiana di prova',
        'original_title' => 'Original Test Book',
        'slug' => 'traduzione-test-book',
        'abbreviation' => 'TTB',
        'type' => 'supplement',
        'edition' => '5e',
        'language' => 'it',
        'publisher' => 'Editore di prova',
        'is_official' => true,
        'is_playtest' => false,
        'is_active' => true,
    ]);

    //Collega la traduzione alla pubblicazione originale
    $relation = $translation
        ->outgoingSourceBookRelations()
        ->create([
            'related_source_book_id' => $original->id,
            'relation_type' => 'translation_of',
            'notes' => 'Pubblicazioni create soltanto per il test.',
        ]);

    //Verifica il manuale da cui parte la relazione
    expect(
        $relation->sourceBook->is($translation)
    )->toBeTrue();

    //Verifica il manuale verso cui punta la relazione
    expect(
        $relation->relatedSourceBook->is($original)
    )->toBeTrue();

    //Verifica la relazione in uscita dalla traduzione
    expect(
        $translation->outgoingSourceBookRelations()->count()
    )->toBe(1);

    //Verifica la relazione in entrata nell'originale
    expect(
        $original->incomingSourceBookRelations()->count()
    )->toBe(1);

    //Verifica il tipo editoriale della relazione
    expect($relation->relation_type)->toBe('translation_of');

    //Verifica che il database impedisca la relazione duplicata
    expect(
        fn () => $translation
            ->outgoingSourceBookRelations()
            ->create([
                'related_source_book_id' => $original->id,
                'relation_type' => 'translation_of',
                'notes' => 'Tentativo di duplicazione.',
            ])
    )->toThrow(QueryException::class);

    //Elimina la pubblicazione originale
    $original->delete();

    //Verifica la cancellazione automatica della relazione
    expect($relation->fresh())->toBeNull();
});
