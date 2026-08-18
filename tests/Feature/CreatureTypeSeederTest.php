<?php

use App\Models\CreatureType;
use Database\Seeders\CreatureTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea i quattordici tipi di creatura senza duplicati', function () {
    $this->seed(CreatureTypeSeeder::class);
    $this->seed(CreatureTypeSeeder::class);

    $creatureTypes = CreatureType::query()
        ->orderBy('sort_order')
        ->get();

    expect($creatureTypes)->toHaveCount(14)
        ->and($creatureTypes->pluck('key')->all())->toBe([
            'aberration',
            'beast',
            'celestial',
            'construct',
            'dragon',
            'elemental',
            'fey',
            'fiend',
            'giant',
            'humanoid',
            'monstrosity',
            'ooze',
            'plant',
            'undead',
        ])
        ->and($creatureTypes->pluck('name')->all())->toBe([
            'Aberrazione',
            'Bestia',
            'Celestiale',
            'Costrutto',
            'Drago',
            'Elementale',
            'Folletto',
            'Immondo',
            'Gigante',
            'Umanoide',
            'Mostruosità',
            'Melma',
            'Vegetale',
            'Non Morto',
        ])
        ->and(
            CreatureType::query()
                ->whereNull('description')
                ->count()
        )->toBe(0);
});
