<?php

use App\Models\Race;
use App\Models\RaceMovement;
use App\Models\RacePhysicalTrait;
use App\Models\RaceSize;
use App\Models\Subrace;
use App\Models\SubraceMovement;
use App\Models\SubracePhysicalTrait;
use Database\Seeders\RaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione completa del catalogo delle razze
it('crea razze e sottorazze senza duplicati', function () {
    /** @var \Tests\TestCase $this */

    //Esegue due volte il seeder per verificarne l'idempotenza
    $this->seed(RaceSeeder::class);
    $this->seed(RaceSeeder::class);

    //Recupera le razze nel loro ordine ufficiale
    $races = Race::query()
        ->orderBy('sort_order')
        ->get();

    //Verifica la quantità delle razze principali
    expect($races)->toHaveCount(9);

    //Verifica le chiavi e l'ordine delle razze
    expect($races->pluck('key')->all())->toBe([
        'dwarf',
        'elf',
        'halfling',
        'human',
        'dragonborn',
        'gnome',
        'half_elf',
        'half_orc',
        'tiefling',
    ]);

    //Verifica i nomi italiani delle razze
    expect($races->pluck('name')->all())->toBe([
        'Nano',
        'Elfo',
        'Halfling',
        'Umano',
        'Dragonide',
        'Gnomo',
        'Mezzelfo',
        'Mezzorco',
        'Tiefling',
    ]);

    //Verifica che il secondo seeding non abbia creato duplicati
    expect(Race::query()->count())->toBe(9)
        ->and(Subrace::query()->count())->toBe(10)
        ->and(RaceSize::query()->count())->toBe(9)
        ->and(RaceMovement::query()->count())->toBe(9)
        ->and(SubraceMovement::query()->count())->toBe(1)
        ->and(RacePhysicalTrait::query()->count())->toBe(9)
        ->and(SubracePhysicalTrait::query()->count())->toBe(5);

    //Verifica che tutte le razze abbiano una descrizione
    expect(
        Race::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);

    //Verifica che tutte le razze appartengano al regolamento corretto
    expect(
        Race::query()
            ->whereHas(
                'ruleset',
                fn ($query) => $query->where(
                    'key',
                    'dnd5e_2014'
                )
            )
            ->count()
    )->toBe(9);

    //Verifica che tutte le razze utilizzino il tipo Umanoide
    expect(
        Race::query()
            ->whereHas(
                'creatureType',
                fn ($query) => $query->where(
                    'key',
                    'humanoid'
                )
            )
            ->count()
    )->toBe(9);
});

//Verifica le sottorazze associate alle rispettive razze
it('collega correttamente le sottorazze alle razze', function () {
    /** @var \Tests\TestCase $this */

    //Inserisce il catalogo delle razze
    $this->seed(RaceSeeder::class);

    //Recupera la razza nanica
    $dwarf = Race::query()
        ->where('key', 'dwarf')
        ->firstOrFail();

    //Verifica le sottorazze naniche
    expect($dwarf->subraces->pluck('key')->all())->toBe([
        'hill_dwarf',
        'mountain_dwarf',
    ]);

    //Recupera la razza elfica
    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    //Verifica le sottorazze elfiche
    expect($elf->subraces->pluck('key')->all())->toBe([
        'high_elf',
        'wood_elf',
        'drow',
    ]);

    //Recupera la razza halfling
    $halfling = Race::query()
        ->where('key', 'halfling')
        ->firstOrFail();

    //Verifica le sottorazze halfling
    expect($halfling->subraces->pluck('key')->all())->toBe([
        'lightfoot_halfling',
        'stout_halfling',
    ]);

    //Recupera la razza gnomesca
    $gnome = Race::query()
        ->where('key', 'gnome')
        ->firstOrFail();

    //Verifica le sottorazze gnomesche
    expect($gnome->subraces->pluck('key')->all())->toBe([
        'forest_gnome',
        'rock_gnome',
    ]);

    //Recupera la variante dell'Umano
    $variantHuman = Subrace::query()
        ->where('key', 'variant_human')
        ->firstOrFail();

    //Verifica la relazione molti-a-uno (BelongsTo):
    //la variante appartiene alla razza Umano
    expect($variantHuman->race->key)->toBe('human');

    //Verifica che la variante sostituisca i bonus della razza
    expect($variantHuman->is_variant)->toBeTrue()
        ->and($variantHuman->replaces_race_ability_bonuses)
        ->toBeTrue()
        ->and($variantHuman->selectable)->toBeTrue()
        ->and($variantHuman->requires_dm_permission)->toBeTrue();
});

//Verifica taglie e velocità delle razze
it('assegna le taglie e le velocità corrette', function () {
    /** @var \Tests\TestCase $this */

    //Inserisce il catalogo delle razze
    $this->seed(RaceSeeder::class);

    //Recupera alcune razze rappresentative
    $dwarf = Race::query()
        ->where('key', 'dwarf')
        ->firstOrFail();

    $elf = Race::query()
        ->where('key', 'elf')
        ->firstOrFail();

    $halfling = Race::query()
        ->where('key', 'halfling')
        ->firstOrFail();

    $gnome = Race::query()
        ->where('key', 'gnome')
        ->firstOrFail();

    //Verifica le taglie delle razze
    expect($dwarf->sizeAssignment->size->name)->toBe('Media')
        ->and($elf->sizeAssignment->size->name)->toBe('Media')
        ->and($halfling->sizeAssignment->size->name)->toBe('Piccola')
        ->and($gnome->sizeAssignment->size->name)->toBe('Piccola');

    //Verifica le velocità terrestri di base
    expect($dwarf->movements->first()->speed_meters)->toBe('7.500')
        ->and($elf->movements->first()->speed_meters)->toBe('9.000')
        ->and($halfling->movements->first()->speed_meters)->toBe('7.500')
        ->and($gnome->movements->first()->speed_meters)->toBe('7.500');

    //Verifica il tipo della velocità assegnata
    expect($elf->movements->first()->movementType->name)
        ->toBe('Terrestre');

    //Recupera l'Elfo dei Boschi
    $woodElf = Subrace::query()
        ->where('key', 'wood_elf')
        ->firstOrFail();

    //Verifica la velocità specifica della sottorazza
    expect($woodElf->movements)->toHaveCount(1)
        ->and($woodElf->movements->first()->speed_meters)
        ->toBe('10.500')
        ->and($woodElf->movements->first()->movementType->name)
        ->toBe('Terrestre');
});

//Verifica i dati utilizzati per generare altezza e peso
it('crea le formule fisiche delle razze', function () {
    /** @var \Tests\TestCase $this */

    //Inserisce il catalogo delle razze
    $this->seed(RaceSeeder::class);

    //Recupera i tratti fisici dell'Halfling
    $halfling = Race::query()
        ->where('key', 'halfling')
        ->firstOrFail();

    $halflingPhysicalTraits = $halfling->physicalTraits;

    //Verifica la formula dell'altezza dell'Halfling
    expect($halflingPhysicalTraits->base_height_cm)
        ->toBe('78.740')
        ->and($halflingPhysicalTraits->height_modifier_dice_count)
        ->toBe(2)
        ->and($halflingPhysicalTraits->height_modifier_die_size)
        ->toBe(4)
        ->and($halflingPhysicalTraits->height_modifier_unit_cm)
        ->toBe('2.540');

    //Verifica il modificatore fisso utilizzato per il peso
    expect($halflingPhysicalTraits->weight_modifier_dice_count)
        ->toBeNull()
        ->and($halflingPhysicalTraits->weight_modifier_die_size)
        ->toBeNull()
        ->and($halflingPhysicalTraits->weight_modifier_fixed_kg)
        ->toBe('0.453592')
        ->and($halflingPhysicalTraits->weight_uses_height_modifier)
        ->toBeTrue();

    //Verifica un esempio di calcolo con un risultato dei dadi pari a 5
    expect(
        $halflingPhysicalTraits->calculateHeightCm(5)
    )->toBe(91.44);

    expect(
        $halflingPhysicalTraits->calculateWeightKg(
            null,
            5
        )
    )->toBe(18.144);

    //Recupera i tratti fisici dell'Umano
    $human = Race::query()
        ->where('key', 'human')
        ->firstOrFail();

    $humanPhysicalTraits = $human->physicalTraits;

    //Verifica che l'Umano utilizzi i dadi per il peso
    expect($humanPhysicalTraits->weight_modifier_dice_count)
        ->toBe(2)
        ->and($humanPhysicalTraits->weight_modifier_die_size)
        ->toBe(4)
        ->and($humanPhysicalTraits->weight_modifier_fixed_kg)
        ->toBeNull()
        ->and($humanPhysicalTraits->weight_uses_height_modifier)
        ->toBeTrue();

    //Recupera i tratti fisici del Nano delle Colline
    $hillDwarf = Subrace::query()
        ->where('key', 'hill_dwarf')
        ->firstOrFail();

    //Verifica la configurazione fisica specifica della sottorazza
    expect($hillDwarf->physicalTraits)->not->toBeNull()
        ->and($hillDwarf->physicalTraits->base_height_cm)
        ->toBe('111.760')
        ->and($hillDwarf->physicalTraits->height_modifier_dice_count)
        ->toBe(2)
        ->and($hillDwarf->physicalTraits->height_modifier_die_size)
        ->toBe(4);
});
