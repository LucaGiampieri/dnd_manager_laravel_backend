<?php

use App\Models\Race;
use App\Models\RaceAbilityBonus;
use App\Models\Subrace;
use App\Models\SubraceAbilityBonus;
use Database\Seeders\ElementalEvilRaceAbilityBonusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce razze e bonus EEPC prima di ogni verifica
beforeEach(function () {
    $this->seed(
        ElementalEvilRaceAbilityBonusSeeder::class
    );
});

//Verifica la quantità e l'idempotenza dei bonus
it('crea i bonus del male elementale senza duplicati', function () {
    //Esegue nuovamente il seeder
    $this->seed(
        ElementalEvilRaceAbilityBonusSeeder::class
    );

    //Recupera gli identificativi delle razze EEPC
    $raceIds = Race::query()
        ->where('version_key', 'eepc_2015')
        ->pluck('id');

    //Recupera gli identificativi delle sottorazze EEPC
    $subraceIds = Subrace::query()
        ->where('version_key', 'eepc_2015')
        ->pluck('id');

    //Le tre razze principali possiedono cinque bonus totali
    expect(
        RaceAbilityBonus::query()
            ->whereIn('race_id', $raceIds)
            ->count()
    )->toBe(5);

    //Le cinque sottorazze possiedono un bonus ciascuna
    expect(
        SubraceAbilityBonus::query()
            ->whereIn('subrace_id', $subraceIds)
            ->count()
    )->toBe(5);
});

//Verifica i bonus delle razze principali
it('assegna i bonus corretti alle razze principali', function () {
    //Recupera gli Aarakocra
    $aarakocra = Race::query()
        ->where('key', 'aarakocra_eepc_2015')
        ->firstOrFail();

    //Organizza i bonus usando le abbreviazioni
    $aarakocraBonuses = $aarakocra
        ->abilityBonuses()
        ->with('ability')
        ->get()
        ->mapWithKeys(
            fn ($bonus) => [
                $bonus->ability->short_name => $bonus->bonus,
            ]
        )
        ->sortKeys()
        ->all();

    expect($aarakocraBonuses)->toBe([
        'DES' => 2,
        'SAG' => 1,
    ]);

    //Recupera i Genasi
    $genasi = Race::query()
        ->where('key', 'genasi_eepc_2015')
        ->firstOrFail();

    $genasiBonuses = $genasi
        ->abilityBonuses()
        ->with('ability')
        ->get()
        ->mapWithKeys(
            fn ($bonus) => [
                $bonus->ability->short_name => $bonus->bonus,
            ]
        )
        ->sortKeys()
        ->all();

    expect($genasiBonuses)->toBe([
        'COS' => 2,
    ]);

    //Recupera i Goliath
    $goliath = Race::query()
        ->where('key', 'goliath_eepc_2015')
        ->firstOrFail();

    $goliathBonuses = $goliath
        ->abilityBonuses()
        ->with('ability')
        ->get()
        ->mapWithKeys(
            fn ($bonus) => [
                $bonus->ability->short_name => $bonus->bonus,
            ]
        )
        ->sortKeys()
        ->all();

    expect($goliathBonuses)->toBe([
        'COS' => 1,
        'FOR' => 2,
    ]);
});

//Verifica i bonus delle sottorazze
it('assegna i bonus corretti alle sottorazze', function () {
    //Definisce i bonus attesi
    $expectedBonuses = [
        'water_genasi_eepc_2015' => [
            'SAG' => 1,
        ],
        'air_genasi_eepc_2015' => [
            'DES' => 1,
        ],
        'fire_genasi_eepc_2015' => [
            'INT' => 1,
        ],
        'earth_genasi_eepc_2015' => [
            'FOR' => 1,
        ],
        'deep_gnome_eepc_2015' => [
            'DES' => 1,
        ],
    ];

    //Verifica separatamente ogni sottorazza
    foreach (
        $expectedBonuses as $subraceKey => $expectedBonus
    ) {
        $subrace = Subrace::query()
            ->where('key', $subraceKey)
            ->firstOrFail();

        $bonuses = $subrace
            ->abilityBonuses()
            ->with('ability')
            ->get()
            ->mapWithKeys(
                fn ($bonus) => [
                    $bonus->ability->short_name =>
                        $bonus->bonus,
                ]
            )
            ->sortKeys()
            ->all();

        expect($bonuses)->toBe($expectedBonus);
    }
});

//Verifica l'ereditarietà dello Gnomo delle Profondità
it('combina i bonus dello gnomo con quelli della sottorazza', function () {
    //Recupera lo Gnomo delle Profondità
    $deepGnome = Subrace::query()
        ->where('key', 'deep_gnome_eepc_2015')
        ->firstOrFail();

    //Recupera il bonus ereditato dalla razza Gnomo
    $gnomeBonuses = $deepGnome
        ->race
        ->abilityBonuses()
        ->with('ability')
        ->get()
        ->mapWithKeys(
            fn ($bonus) => [
                $bonus->ability->short_name => $bonus->bonus,
            ]
        )
        ->all();

    //Recupera il bonus specifico della sottorazza
    $deepGnomeBonuses = $deepGnome
        ->abilityBonuses()
        ->with('ability')
        ->get()
        ->mapWithKeys(
            fn ($bonus) => [
                $bonus->ability->short_name => $bonus->bonus,
            ]
        )
        ->all();

    expect($gnomeBonuses)->toBe([
        'INT' => 2,
    ])
        ->and($deepGnomeBonuses)->toBe([
            'DES' => 1,
        ]);
});

//Verifica la compatibilità con la regola opzionale di Tasha
it('rende i bonus riassegnabili tramite la regola di Tasha', function () {
    //Recupera gli identificativi dei contenuti EEPC
    $raceIds = Race::query()
        ->where('version_key', 'eepc_2015')
        ->pluck('id');

    $subraceIds = Subrace::query()
        ->where('version_key', 'eepc_2015')
        ->pluck('id');

    //Recupera tutti i bonus EEPC
    $raceBonuses = RaceAbilityBonus::query()
        ->whereIn('race_id', $raceIds)
        ->get();

    $subraceBonuses = SubraceAbilityBonus::query()
        ->whereIn('subrace_id', $subraceIds)
        ->get();

    //Tutti i bonus possono essere riassegnati
    //soltanto quando la campagna abilita la regola opzionale
    expect($raceBonuses)->toHaveCount(5)
        ->and(
            $raceBonuses
                ->where('can_be_reassigned', true)
                ->count()
        )
        ->toBe(5)
        ->and($subraceBonuses)->toHaveCount(5)
        ->and(
            $subraceBonuses
                ->where('can_be_reassigned', true)
                ->count()
        )
        ->toBe(5);

    //Le note ricordano la dipendenza dalla regola opzionale
    foreach (
        $raceBonuses->concat($subraceBonuses) as $bonus
    ) {
        expect($bonus->notes)
            ->toContain(
                'Personalizzazione dell’origine'
            );
    }
});
