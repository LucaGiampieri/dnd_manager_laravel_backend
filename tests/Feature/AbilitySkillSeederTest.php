<?php

use App\Models\Ability;
use App\Models\Skill;
use Database\Seeders\AbilitySeeder;
use Database\Seeders\SkillSeeder;

test('i seeder creano caratteristiche e skill senza duplicati', function () {
    //Esegue una prima volta i seeder delle caratteristiche e delle abilità
    $this->seed(AbilitySeeder::class);
    $this->seed(SkillSeeder::class);

    //Esegue nuovamente gli stessi seeder
    //per verificare che siano idempotenti e non generino duplicati
    $this->seed(AbilitySeeder::class);
    $this->seed(SkillSeeder::class);

    //Verifica che siano state create esattamente sei caratteristiche
    expect(Ability::count())->toBe(6);

    //Verifica che siano state create esattamente diciotto abilità
    expect(Skill::count())->toBe(18);

    //Recupera due abilità collegate a caratteristiche differenti
    $acrobatics = Skill::query()
        ->where('name', 'Acrobazia')
        ->firstOrFail();

    $athletics = Skill::query()
        ->where('name', 'Atletica')
        ->firstOrFail();

    //Relazione molti-a-uno (BelongsTo):
    //verifica che Acrobazia appartenga alla Destrezza
    expect($acrobatics->ability->short_name)->toBe('DES');

    //Relazione molti-a-uno (BelongsTo):
    //verifica che Atletica appartenga alla Forza
    expect($athletics->ability->short_name)->toBe('FOR');
});
