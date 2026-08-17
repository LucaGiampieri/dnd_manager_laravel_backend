<?php

use App\Models\Ruleset;
use Illuminate\Support\Carbon;

test('un regolamento può avere molti manuali', function () {
    $ruleset = Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
        'revision' => '2014',
        'is_active' => true,
    ]);

    $playerHandbook = $ruleset->sourceBooks()->create([
        'title' => 'Manuale del Giocatore',
        'original_title' => 'Player’s Handbook',
        'slug' => 'manuale-del-giocatore',
        'abbreviation' => 'PHB',
        'type' => 'core_rulebook',
        'edition' => '5e',
        'language' => 'it',
        'publication_date' => '2020-05-10',
        'is_official' => true,
        'is_playtest' => false,
        'is_active' => true,
    ]);

    $ruleset->sourceBooks()->create([
        'title' => 'Manuale dei Mostri',
        'original_title' => 'Monster Manual',
        'slug' => 'manuale-dei-mostri',
        'abbreviation' => 'MM',
        'type' => 'core_rulebook',
        'edition' => '5e',
        'language' => 'it',
    ]);

    expect($ruleset->sourceBooks)->toHaveCount(2);

    expect($ruleset->sourceBooks->pluck('title')->all())
        ->toContain('Manuale del Giocatore')
        ->toContain('Manuale dei Mostri');

    expect($playerHandbook->ruleset->is($ruleset))->toBeTrue();

    expect($playerHandbook->publication_date)
        ->toBeInstanceOf(Carbon::class);

    expect($playerHandbook->publication_date->format('d/m/Y'))
        ->toBe('10/05/2020');

    expect($playerHandbook->is_official)->toBeTrue();
    expect($playerHandbook->is_playtest)->toBeFalse();
    expect($playerHandbook->is_active)->toBeTrue();
});
