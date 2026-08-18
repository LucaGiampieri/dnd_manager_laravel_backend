<?php

use App\Models\ContentRelation;
use Illuminate\Database\QueryException;
use App\Models\CreatureTag;
use App\Models\CreatureType;
use Database\Seeders\CreatureTagSeeder;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('collega due contenuti in entrambe le direzioni', function () {
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
        CreatureTagSeeder::class,
    ]);

    $demon = CreatureTag::query()
        ->where('key', 'demon')
        ->firstOrFail();

    $fiend = CreatureType::query()
        ->where('key', 'fiend')
        ->firstOrFail();

    $relation = $demon
        ->outgoingContentRelations()
        ->create([
            'related_content_type' => $fiend->getMorphClass(),
            'related_content_id' => $fiend->getKey(),
            'relation_type' => 'derived_from',
            'notes' => 'Relazione utilizzata soltanto come esempio nel test.',
        ]);

    expect($relation->content->is($demon))->toBeTrue()
        ->and($relation->relatedContent->is($fiend))->toBeTrue()
        ->and($demon->outgoingContentRelations()->count())->toBe(1)
        ->and($fiend->incomingContentRelations()->count())->toBe(1)
        ->and($relation->relation_type)->toBe('derived_from');

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

it('elimina le relazioni quando un contenuto viene cancellato', function () {
    $this->seed([
        RulesetSeeder::class,
        CreatureTypeSeeder::class,
        CreatureTagSeeder::class,
    ]);

    $demon = CreatureTag::query()
        ->where('key', 'demon')
        ->firstOrFail();

    $fiend = CreatureType::query()
        ->where('key', 'fiend')
        ->firstOrFail();

    $demon->outgoingContentRelations()->create([
        'related_content_type' => $fiend->getMorphClass(),
        'related_content_id' => $fiend->getKey(),
        'relation_type' => 'derived_from',
    ]);

    $fiend->outgoingContentRelations()->create([
        'related_content_type' => $demon->getMorphClass(),
        'related_content_id' => $demon->getKey(),
        'relation_type' => 'other',
    ]);

    expect(ContentRelation::query()->count())->toBe(2);

    $demon->delete();

    expect(ContentRelation::query()->count())->toBe(0);
});
