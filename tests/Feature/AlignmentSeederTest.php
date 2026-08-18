<?php

use App\Models\Alignment;
use App\Models\Ruleset;
use Database\Seeders\AlignmentSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ripristina il database di test prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione completa e senza duplicati degli allineamenti
it('crea i nove allineamenti senza duplicati', function () {
    //Crea il regolamento richiesto dagli allineamenti
    $this->seed(RulesetSeeder::class);

    //Esegue due volte il seeder per verificarne l'idempotenza
    $this->seed(AlignmentSeeder::class);
    $this->seed(AlignmentSeeder::class);

    //Recupera il regolamento D&D 5e 2014
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera gli allineamenti del regolamento nell'ordine previsto
    $alignments = Alignment::query()
        ->where('ruleset_id', $ruleset->id)
        ->orderBy('sort_order')
        ->get();

    //Verifica che siano stati creati esattamente nove allineamenti
    expect($alignments)->toHaveCount(9);

    //Verifica le chiavi e il loro ordine
    expect($alignments->pluck('key')->all())->toBe([
        'lawful_good',
        'neutral_good',
        'chaotic_good',
        'lawful_neutral',
        'neutral',
        'chaotic_neutral',
        'lawful_evil',
        'neutral_evil',
        'chaotic_evil',
    ]);

    //Recupera l'allineamento Legale Buono
    $lawfulGood = $alignments->firstWhere(
        'key',
        'lawful_good'
    );

    //Verifica i due assi di Legale Buono
    expect(
        $lawfulGood->only([
            'ethical_axis',
            'moral_axis',
        ])
    )->toBe([
        'ethical_axis' => 'lawful',
        'moral_axis' => 'good',
    ]);

    //Recupera l'allineamento Neutrale
    $neutral = $alignments->firstWhere(
        'key',
        'neutral'
    );

    //Verifica che Neutrale occupi il centro di entrambi gli assi
    expect(
        $neutral->only([
            'ethical_axis',
            'moral_axis',
        ])
    )->toBe([
        'ethical_axis' => 'neutral',
        'moral_axis' => 'neutral',
    ]);

    //Verifica che Senza Allineamento non sia registrato
    //come se fosse un decimo allineamento
    expect(
        $alignments->contains('key', 'unaligned')
    )->toBeFalse();
});
