<?php

use App\Models\Ruleset;
use App\Models\SourceBook;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SourceBookSeeder;

test('i seeder creano tre manuali senza duplicati', function () {
    $this->seed(RulesetSeeder::class);
    $this->seed(SourceBookSeeder::class);

    $this->seed(RulesetSeeder::class);
    $this->seed(SourceBookSeeder::class);

    expect(
        Ruleset::where('key', 'dnd5e_2014')->count()
    )->toBe(1);

    expect(SourceBook::count())->toBe(3);

    expect(SourceBook::pluck('slug')->all())
        ->toContain('phb-2014')
        ->toContain('dmg-2014')
        ->toContain('mm-2014');

    $playerHandbook = SourceBook::where('slug', 'phb-2014')
        ->firstOrFail();

    expect($playerHandbook->ruleset->key)
        ->toBe('dnd5e_2014');
});
