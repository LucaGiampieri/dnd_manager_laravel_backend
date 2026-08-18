<?php

use App\Models\Alignment;
use App\Models\CreatureStatBlock;
use Database\Seeders\AlignmentSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ripristina il database di test prima di ogni test
uses(RefreshDatabase::class);

//Verifica gli allineamenti ammessi e il caso senza allineamento
it('gestisce gli allineamenti di uno stat block', function () {
    //Crea il regolamento e i nove allineamenti
    $this->seed([
        RulesetSeeder::class,
        AlignmentSeeder::class,
    ]);

    //Crea uno stat block che ammette qualsiasi allineamento malvagio
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura malvagia di prova',
        'alignment' => 'Qualsiasi allineamento malvagio',
        'alignment_mode' => 'allowed_set',
    ]);

    //Recupera i tre allineamenti malvagi
    $evilAlignments = Alignment::query()
        ->whereIn('key', [
            'lawful_evil',
            'neutral_evil',
            'chaotic_evil',
        ])
        ->get()
        ->keyBy('key');

    //Collega i tre allineamenti allo stat block
    $statBlock->alignments()->attach([
        $evilAlignments['lawful_evil']->id => [
            'is_typical' => false,
            'sort_order' => 1,
        ],
        $evilAlignments['neutral_evil']->id => [
            'is_typical' => true,
            'sort_order' => 2,
        ],
        $evilAlignments['chaotic_evil']->id => [
            'is_typical' => false,
            'sort_order' => 3,
        ],
    ]);

    //Ricarica dal database gli allineamenti collegati
    $statBlock->load('alignments');

    //Verifica la modalità utilizzata
    expect($statBlock->alignment_mode)->toBe('allowed_set');

    //Verifica che il testo originale sia stato conservato
    expect($statBlock->alignment)->toBe(
        'Qualsiasi allineamento malvagio'
    );

    //Verifica che siano presenti tre allineamenti ammessi
    expect($statBlock->alignments)->toHaveCount(3);

    //Verifica le chiavi e l'ordine degli allineamenti
    expect($statBlock->alignments->pluck('key')->all())->toBe([
        'lawful_evil',
        'neutral_evil',
        'chaotic_evil',
    ]);

    //Recupera l'allineamento indicato come tipico
    $typicalAlignment = $statBlock->alignments
        ->firstWhere('key', 'neutral_evil');

    //Verifica il valore booleano salvato nella tabella di collegamento
    expect($typicalAlignment->pivot->is_typical)->toBeTrue();

    //Verifica la relazione inversa dall'allineamento allo stat block
    expect(
        $evilAlignments['neutral_evil']
            ->creatureStatBlocks()
            ->whereKey($statBlock->id)
            ->exists()
    )->toBeTrue();

    //Crea uno stat block privo di allineamento
    $unalignedStatBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura senza allineamento di prova',
        'alignment' => 'Senza Allineamento',
        'alignment_mode' => 'unaligned',
    ]);

    //Verifica che il caso senza allineamento non abbia righe nella pivot
    expect(
        $unalignedStatBlock->alignments()->count()
    )->toBe(0);
});
