<?php

use App\Models\Ruleset;
use Illuminate\Support\Carbon;

test('un regolamento può avere molti manuali', function () {
    //Crea un regolamento utilizzato soltanto dal test
    $ruleset = Ruleset::create([
        'key' => 'dnd5e_2014',
        'name' => 'D&D 5e 2014',
        'edition' => '5e',
        'revision' => '2014',
        'is_active' => true,
    ]);

    //Relazione uno-a-molti (HasMany):
    //crea il primo manuale associandolo al regolamento
    $playerHandbook = $ruleset->sourceBooks()->create([
        'title' => 'Manuale del Giocatore',
        'original_title' => 'Player’s Handbook',
        'slug' => 'manuale-del-giocatore',
        'abbreviation' => 'PHB',
        'type' => 'core_rulebook',
        'edition' => '5e',
        'language' => 'it',

        //Data utilizzata esclusivamente per verificare il cast
        'publication_date' => '2020-05-10',

        'is_official' => true,
        'is_playtest' => false,
        'is_active' => true,
    ]);

    //Relazione uno-a-molti (HasMany):
    //crea un secondo manuale appartenente allo stesso regolamento
    $ruleset->sourceBooks()->create([
        'title' => 'Manuale dei Mostri',
        'original_title' => 'Monster Manual',
        'slug' => 'manuale-dei-mostri',
        'abbreviation' => 'MM',
        'type' => 'core_rulebook',
        'edition' => '5e',
        'language' => 'it',
    ]);

    //Verifica che il regolamento possieda esattamente due manuali
    expect($ruleset->sourceBooks)->toHaveCount(2);

    //Verifica che entrambi i manuali siano presenti nella relazione
    expect($ruleset->sourceBooks->pluck('title')->all())
        ->toContain('Manuale del Giocatore')
        ->toContain('Manuale dei Mostri');

    //Relazione molti-a-uno (BelongsTo):
    //verifica che il manuale appartenga al regolamento creato
    expect($playerHandbook->ruleset->is($ruleset))->toBeTrue();

    //Verifica che Laravel converta la data
    //in un oggetto Carbon tramite il cast del modello
    expect($playerHandbook->publication_date)
        ->toBeInstanceOf(Carbon::class);

    //Verifica che il valore della data sia rimasto corretto
    expect($playerHandbook->publication_date->format('d/m/Y'))
        ->toBe('10/05/2020');

    //Verifica la conversione dei tre campi booleani
    expect($playerHandbook->is_official)->toBeTrue();
    expect($playerHandbook->is_playtest)->toBeFalse();
    expect($playerHandbook->is_active)->toBeTrue();
});
