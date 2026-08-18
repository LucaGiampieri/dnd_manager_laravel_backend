<?php

use App\Models\CreatureType;
use App\Models\SourceBook;
use Database\Seeders\CreatureTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('collega un contenuto al manuale e alle sue pagine', function () {
    $this->seed([
        RulesetSeeder::class,
        SourceBookSeeder::class,
        CreatureTypeSeeder::class,
    ]);

    $sourceBook = SourceBook::query()
        ->where('slug', 'mm-2014')
        ->firstOrFail();

    $creatureType = CreatureType::query()
        ->where('key', 'undead')
        ->firstOrFail();

    $sourceReference = $creatureType
        ->sourceReferences()
        ->create([
            'source_book_id' => $sourceBook->id,
            'key' => 'mm_2014_it_definition',
            'reference_type' => 'definition',
            'page_start' => 10,
            'page_end' => 11,
            'section' => 'Tipi di creatura',
            'is_primary' => true,
            'sort_order' => 1,
            'official_text' => null,
            'notes' => 'Pagine utilizzate soltanto come esempio nel test.',
        ]);

    expect($sourceReference->sourceable->is($creatureType))->toBeTrue()
        ->and($sourceReference->sourceBook->is($sourceBook))->toBeTrue()
        ->and($creatureType->sourceReferences()->count())->toBe(1)
        ->and($sourceBook->sourceReferences()->count())->toBe(1)
        ->and($sourceReference->page_start)->toBe(10)
        ->and($sourceReference->page_end)->toBe(11)
        ->and($sourceReference->is_primary)->toBeTrue()
        ->and($sourceReference->official_text)->toBeNull();

    $creatureType->delete();

    expect($sourceReference->fresh())->toBeNull();
});
