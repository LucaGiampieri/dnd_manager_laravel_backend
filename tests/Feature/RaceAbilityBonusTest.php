<?php

use App\Models\Ability;
use App\Models\Race;
use App\Models\RaceAbilityBonus;
use App\Models\Subrace;
use App\Models\SubraceAbilityBonus;
use Database\Seeders\AbilitySeeder;
use Database\Seeders\RaceSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara le razze e le caratteristiche richieste dai test
beforeEach(function () {
    /** @var \Tests\TestCase $this */

    //Crea il catalogo delle caratteristiche
    $this->seed(AbilitySeeder::class);

    //Crea il catalogo delle razze e delle sottorazze
    $this->seed(RaceSeeder::class);
});

//Verifica i bonus fissi concessi da razze e sottorazze
it('gestisce i bonus di caratteristica di razze e sottorazze', function () {
    //Recupera la razza Elfo
    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    //Recupera la sottorazza Elfo Alto
    $highElf = Subrace::query()
        ->where('key', 'high_elf')
        ->firstOrFail();

    //Recupera le caratteristiche interessate dai bonus
    $dexterity = Ability::query()
        ->where('short_name', 'DES')
        ->firstOrFail();

    $intelligence = Ability::query()
        ->where('short_name', 'INT')
        ->firstOrFail();

    //Assegna Destrezza +2 alla razza Elfo
    $raceBonus = $elf->abilityBonuses()->create([
        'ability_id' => $dexterity->id,
        'bonus' => 2,
        'notes' => 'Bonus creato soltanto per il test.',
    ]);

    //Assegna Intelligenza +1 alla sottorazza Elfo Alto
    $subraceBonus = $highElf->abilityBonuses()->create([
        'ability_id' => $intelligence->id,
        'bonus' => 1,
        'notes' => 'Bonus creato soltanto per il test.',
    ]);

    //Ricarica le relazioni utilizzate dalle verifiche
    $elf->load('abilityBonuses.ability');
    $highElf->load('abilityBonuses.ability');

    //Verifica il bonus concesso dalla razza
    expect($elf->abilityBonuses)->toHaveCount(1)
        ->and($elf->abilityBonuses->first()->bonus)->toBe(2)
        ->and($elf->abilityBonuses->first()->ability->short_name)
        ->toBe('DES');

    //Verifica il bonus concesso dalla sottorazza
    expect($highElf->abilityBonuses)->toHaveCount(1)
        ->and($highElf->abilityBonuses->first()->bonus)->toBe(1)
        ->and($highElf->abilityBonuses->first()->ability->short_name)
        ->toBe('INT');

    //Verifica la relazione molti-a-uno (BelongsTo):
    //il bonus razziale appartiene alla razza Elfo
    expect($raceBonus->race->is($elf))->toBeTrue();

    //Verifica la relazione molti-a-uno (BelongsTo):
    //il bonus della sottorazza appartiene all'Elfo Alto
    expect($subraceBonus->subrace->is($highElf))->toBeTrue();

    //Verifica la relazione inversa dalla caratteristica
    expect(
        $dexterity
            ->raceAbilityBonuses()
            ->whereKey($raceBonus->id)
            ->exists()
    )->toBeTrue();

    //Verifica la relazione inversa dalla caratteristica
    expect(
        $intelligence
            ->subraceAbilityBonuses()
            ->whereKey($subraceBonus->id)
            ->exists()
    )->toBeTrue();
});

//Verifica che un bonus privo di effetto non venga salvato
it('rifiuta bonus di caratteristica uguali a zero', function () {
    //Recupera una razza e una caratteristica
    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    $dexterity = Ability::query()
        ->where('short_name', 'DES')
        ->firstOrFail();

    //Verifica il controllo applicato ai bonus razziali
    expect(
        fn () => $elf->abilityBonuses()->create([
            'ability_id' => $dexterity->id,
            'bonus' => 0,
        ])
    )->toThrow(
        \InvalidArgumentException::class,
        'Il bonus di caratteristica deve essere diverso da zero.'
    );

    //Recupera una sottorazza
    $highElf = Subrace::query()
        ->where('key', 'high_elf')
        ->firstOrFail();

    //Verifica il controllo applicato ai bonus delle sottorazze
    expect(
        fn () => $highElf->abilityBonuses()->create([
            'ability_id' => $dexterity->id,
            'bonus' => 0,
        ])
    )->toThrow(
        \InvalidArgumentException::class,
        'Il bonus di caratteristica deve essere diverso da zero.'
    );
});

//Verifica i vincoli univoci dei bonus automatici
it('rifiuta bonus duplicati per la stessa caratteristica', function () {
    //Recupera la razza Elfo
    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    //Recupera la sottorazza Elfo Alto
    $highElf = Subrace::query()
        ->where('key', 'high_elf')
        ->firstOrFail();

    //Recupera la caratteristica Destrezza
    $dexterity = Ability::query()
        ->where('short_name', 'DES')
        ->firstOrFail();

    //Crea il primo bonus della razza
    $elf->abilityBonuses()->create([
        'ability_id' => $dexterity->id,
        'bonus' => 2,
    ]);

    //Verifica che la razza non possa assegnare nuovamente
    //un bonus alla stessa caratteristica
    expect(
        fn () => $elf->abilityBonuses()->create([
            'ability_id' => $dexterity->id,
            'bonus' => 1,
        ])
    )->toThrow(QueryException::class);

    //Crea il primo bonus della sottorazza
    $highElf->abilityBonuses()->create([
        'ability_id' => $dexterity->id,
        'bonus' => 1,
    ]);

    //Verifica che la sottorazza non possa assegnare nuovamente
    //un bonus alla stessa caratteristica
    expect(
        fn () => $highElf->abilityBonuses()->create([
            'ability_id' => $dexterity->id,
            'bonus' => 2,
        ])
    )->toThrow(QueryException::class);
});

//Verifica il supporto di eventuali penalità
it('permette modificatori negativi per contenuti particolari', function () {
    //Recupera la razza e la caratteristica utilizzate dal test
    $race = Race::query()
        ->where('key', 'half_orc')
        ->firstOrFail();

    $intelligence = Ability::query()
        ->where('short_name', 'INT')
        ->firstOrFail();

    //Crea un modificatore negativo di esempio
    $abilityModifier = $race->abilityBonuses()->create([
        'ability_id' => $intelligence->id,
        'bonus' => -2,
        'notes' => 'Penalità creata soltanto per il test.',
    ]);

    //Verifica che il valore negativo venga conservato
    expect($abilityModifier->bonus)->toBe(-2);
});

//Verifica la cancellazione a cascata dei bonus
it('elimina i bonus quando viene cancellata la razza', function () {
    //Recupera la razza e una sua sottorazza
    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    $highElf = Subrace::query()
        ->where('key', 'high_elf')
        ->firstOrFail();

    //Recupera due caratteristiche
    $dexterity = Ability::query()
        ->where('short_name', 'DES')
        ->firstOrFail();

    $intelligence = Ability::query()
        ->where('short_name', 'INT')
        ->firstOrFail();

    //Crea un bonus appartenente alla razza
    $raceBonus = $elf->abilityBonuses()->create([
        'ability_id' => $dexterity->id,
        'bonus' => 2,
    ]);

    //Crea un bonus appartenente alla sottorazza
    $subraceBonus = $highElf->abilityBonuses()->create([
        'ability_id' => $intelligence->id,
        'bonus' => 1,
    ]);

    //Memorizza gli identificativi prima della cancellazione
    $raceBonusId = $raceBonus->id;
    $subraceBonusId = $subraceBonus->id;

    //Cancella la razza principale
    $elf->delete();

    //Verifica la cancellazione del bonus razziale
    expect(
        RaceAbilityBonus::query()
            ->whereKey($raceBonusId)
            ->exists()
    )->toBeFalse();

    //Verifica la cancellazione del bonus della sottorazza
    expect(
        SubraceAbilityBonus::query()
            ->whereKey($subraceBonusId)
            ->exists()
    )->toBeFalse();
});
