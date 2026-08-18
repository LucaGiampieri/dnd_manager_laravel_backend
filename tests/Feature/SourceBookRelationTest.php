<?php

use Illuminate\Database\QueryException;
use App\Models\Ruleset;
use Database\Seeders\RulesetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('collega una pubblicazione alla sua versione originale', function () {
    $this->seed(RulesetSeeder::class);

    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

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

    $relation = $translation
        ->outgoingSourceBookRelations()
        ->create([
            'related_source_book_id' => $original->id,
            'relation_type' => 'translation_of',
            'notes' => 'Pubblicazioni create soltanto per il test.',
        ]);

    expect($relation->sourceBook->is($translation))->toBeTrue()
        ->and($relation->relatedSourceBook->is($original))->toBeTrue()
        ->and($translation->outgoingSourceBookRelations()->count())->toBe(1)
        ->and($original->incomingSourceBookRelations()->count())->toBe(1)
        ->and($relation->relation_type)->toBe('translation_of');

    expect(
        fn () => $translation
        ->outgoingSourceBookRelations()
        ->create([
            'related_source_book_id' => $original->id,
            'relation_type' => 'translation_of',
            'notes' => 'Tentativo di duplicazione.',
        ])
    )->toThrow(QueryException::class);

    $original->delete();

    expect($relation->fresh())->toBeNull();
});
