<?php

use App\Models\Ability;
use App\Models\Skill;
use Database\Seeders\AbilitySeeder;
use Database\Seeders\SkillSeeder;

test('i seeder creano caratteristiche e skill senza duplicati', function () {
    $this->seed(AbilitySeeder::class);
    $this->seed(SkillSeeder::class);

    $this->seed(AbilitySeeder::class);
    $this->seed(SkillSeeder::class);

    expect(Ability::count())->toBe(6);
    expect(Skill::count())->toBe(18);

    $acrobatics = Skill::where('name', 'Acrobazia')->firstOrFail();
    $athletics = Skill::where('name', 'Atletica')->firstOrFail();

    expect($acrobatics->ability->short_name)->toBe('DES');
    expect($athletics->ability->short_name)->toBe('FOR');
});
