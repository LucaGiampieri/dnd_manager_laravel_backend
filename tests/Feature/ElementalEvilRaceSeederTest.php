<?php

use App\Models\MovementType;
use App\Models\Race;
use App\Models\Subrace;
use Database\Seeders\ElementalEvilRaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce le razze del manuale EEPC prima di ogni verifica
beforeEach(function () {
    $this->seed(ElementalEvilRaceSeeder::class);
});

//Verifica la quantità e l'idempotenza del seeder
it('crea le razze del male elementale senza duplicati', function () {
    //Esegue nuovamente il seeder
    $this->seed(ElementalEvilRaceSeeder::class);

    //Le nove razze PHB vengono affiancate
    //dalle tre razze principali dell'EEPC
    expect(Race::query()->count())->toBe(12);

    //Le dieci sottorazze PHB vengono affiancate
    //dalle quattro varianti dei Genasi
    //e dallo Gnomo delle Profondità
    expect(Subrace::query()->count())->toBe(15);

    //Recupera le tre razze principali del manuale
    $elementalEvilRaces = Race::query()
        ->where('version_key', 'eepc_2015')
        ->orderBy('sort_order')
        ->get();

    expect($elementalEvilRaces)->toHaveCount(3)
        ->and($elementalEvilRaces->pluck('key')->all())
        ->toBe([
            'aarakocra_eepc_2015',
            'genasi_eepc_2015',
            'goliath_eepc_2015',
        ])
        ->and(
            $elementalEvilRaces
                ->where('is_legacy', true)
                ->count()
        )
        ->toBe(3)
        ->and(
            $elementalEvilRaces
                ->where('selectable', true)
                ->count()
        )
        ->toBe(3)
        ->and(
            $elementalEvilRaces
                ->where('requires_dm_permission', true)
                ->count()
        )
        ->toBe(3);

    //Recupera tutte le sottorazze dell'EEPC
    $elementalEvilSubraces = Subrace::query()
        ->where('version_key', 'eepc_2015')
        ->get();

    expect($elementalEvilSubraces)->toHaveCount(5)
        ->and(
            $elementalEvilSubraces
                ->pluck('key')
                ->sort()
                ->values()
                ->all()
        )
        ->toBe([
            'air_genasi_eepc_2015',
            'deep_gnome_eepc_2015',
            'earth_genasi_eepc_2015',
            'fire_genasi_eepc_2015',
            'water_genasi_eepc_2015',
        ])
        ->and(
            $elementalEvilSubraces
                ->where('is_legacy', true)
                ->count()
        )
        ->toBe(5)
        ->and(
            $elementalEvilSubraces
                ->where('requires_dm_permission', true)
                ->count()
        )
        ->toBe(5);
});

//Verifica le versioni e i collegamenti tra razze e sottorazze
it('collega correttamente genasi e gnomo delle profondità', function () {
    //Recupera la versione EEPC dei Genasi
    $genasi = Race::query()
        ->where('key', 'genasi_eepc_2015')
        ->firstOrFail();

    expect($genasi->canonical_key)
        ->toBe('genasi')
        ->and($genasi->version_key)
        ->toBe('eepc_2015')
        ->and($genasi->is_legacy)
        ->toBeTrue();

    //Verifica le quattro sottorazze elementali
    expect(
        $genasi->subraces()
            ->orderBy('sort_order')
            ->pluck('key')
            ->all()
    )->toBe([
        'water_genasi_eepc_2015',
        'air_genasi_eepc_2015',
        'fire_genasi_eepc_2015',
        'earth_genasi_eepc_2015',
    ]);

    //Verifica che ogni sottorazza appartenga ai Genasi
    foreach ($genasi->subraces as $genasiSubrace) {
        expect($genasiSubrace->race->is($genasi))
            ->toBeTrue();
    }

    //Recupera lo Gnomo delle Profondità
    $deepGnome = Subrace::query()
        ->where('key', 'deep_gnome_eepc_2015')
        ->firstOrFail();

    //Verifica che sia collegato allo Gnomo del PHB
    expect($deepGnome->race->key)
        ->toBe('gnome')
        ->and($deepGnome->race->version_key)
        ->toBe('phb_2014')
        ->and($deepGnome->canonical_key)
        ->toBe('deep_gnome')
        ->and($deepGnome->version_key)
        ->toBe('eepc_2015');
});

//Verifica taglie e velocità delle nuove razze
it('assegna correttamente taglie e movimenti', function () {
    //Recupera i tipi di movimento necessari
    $movementTypes = MovementType::query()
        ->whereIn('name', [
            'Terrestre',
            'Volare',
            'Nuotare',
        ])
        ->get()
        ->keyBy('name');

    //Recupera gli Aarakocra
    $aarakocra = Race::query()
        ->where('key', 'aarakocra_eepc_2015')
        ->firstOrFail();

    //Recupera i due movimenti degli Aarakocra
    $aarakocraWalking = $aarakocra->movements
        ->firstWhere(
            'movement_type_id',
            $movementTypes['Terrestre']->id
        );

    $aarakocraFlying = $aarakocra->movements
        ->firstWhere(
            'movement_type_id',
            $movementTypes['Volare']->id
        );

    expect($aarakocra->sizeAssignment->size->name)
        ->toBe('Media')
        ->and($aarakocraWalking)
        ->not->toBeNull()
        ->and($aarakocraWalking->speed_meters)
        ->toBe('7.500')
        ->and($aarakocraFlying)
        ->not->toBeNull()
        ->and($aarakocraFlying->speed_meters)
        ->toBe('15.000')
        ->and($aarakocraFlying->condition)
        ->toContain('armatura media')
        ->and($aarakocraFlying->condition)
        ->toContain('pesante');

    //Recupera i Genasi
    $genasi = Race::query()
        ->where('key', 'genasi_eepc_2015')
        ->firstOrFail();

    $genasiWalking = $genasi->movements
        ->firstWhere(
            'movement_type_id',
            $movementTypes['Terrestre']->id
        );

    expect($genasi->sizeAssignment->size->name)
        ->toBe('Media')
        ->and($genasiWalking->speed_meters)
        ->toBe('9.000');

    //Recupera il Genasi dell'Acqua
    $waterGenasi = Subrace::query()
        ->where('key', 'water_genasi_eepc_2015')
        ->firstOrFail();

    //Verifica il movimento di nuoto aggiuntivo
    $swimming = $waterGenasi->movements
        ->firstWhere(
            'movement_type_id',
            $movementTypes['Nuotare']->id
        );

    expect($swimming)
        ->not->toBeNull()
        ->and($swimming->speed_meters)
        ->toBe('9.000');

    //Recupera il Goliath
    $goliath = Race::query()
        ->where('key', 'goliath_eepc_2015')
        ->firstOrFail();

    $goliathWalking = $goliath->movements
        ->firstWhere(
            'movement_type_id',
            $movementTypes['Terrestre']->id
        );

    expect($goliath->sizeAssignment->size->name)
        ->toBe('Media')
        ->and($goliathWalking->speed_meters)
        ->toBe('9.000');

    //Lo Gnomo delle Profondità eredita la taglia
    //e la velocità terrestre dallo Gnomo
    $deepGnome = Subrace::query()
        ->where('key', 'deep_gnome_eepc_2015')
        ->firstOrFail();

    $deepGnomeWalking = $deepGnome->race->movements
        ->firstWhere(
            'movement_type_id',
            $movementTypes['Terrestre']->id
        );

    expect($deepGnome->race->sizeAssignment->size->name)
        ->toBe('Piccola')
        ->and($deepGnomeWalking->speed_meters)
        ->toBe('7.500')
        ->and($deepGnome->movements()->count())
        ->toBe(0);
});

//Verifica i dati relativi a età, altezza e peso
it('crea i tratti fisici delle razze del male elementale', function () {
    //Recupera gli Aarakocra
    $aarakocra = Race::query()
        ->where('key', 'aarakocra_eepc_2015')
        ->firstOrFail();

    expect($aarakocra->physicalTraits->maturity_age_years)
        ->toEqual(3)
        ->and($aarakocra->physicalTraits->lifespan_years)
        ->toEqual(30)
        ->and($aarakocra->physicalTraits->max_height_cm)
        ->toEqual(150.0)
        ->and($aarakocra->physicalTraits->min_weight_kg)
        ->toEqual(40.0)
        ->and($aarakocra->physicalTraits->max_weight_kg)
        ->toEqual(50.0);

    //Recupera i Genasi
    $genasi = Race::query()
        ->where('key', 'genasi_eepc_2015')
        ->firstOrFail();

    expect($genasi->physicalTraits->maturity_age_years)
        ->toEqual(18)
        ->and($genasi->physicalTraits->lifespan_years)
        ->toEqual(120)
        ->and($genasi->physicalTraits->min_height_cm)
        ->toEqual(150.0)
        ->and($genasi->physicalTraits->max_height_cm)
        ->toEqual(180.0);

    //Recupera il Goliath
    $goliath = Race::query()
        ->where('key', 'goliath_eepc_2015')
        ->firstOrFail();

    expect($goliath->physicalTraits->maturity_age_years)
        ->toEqual(20)
        ->and($goliath->physicalTraits->lifespan_years)
        ->toEqual(100)
        ->and($goliath->physicalTraits->min_height_cm)
        ->toEqual(210.0)
        ->and($goliath->physicalTraits->max_height_cm)
        ->toEqual(240.0)
        ->and($goliath->physicalTraits->min_weight_kg)
        ->toEqual(140.0)
        ->and($goliath->physicalTraits->max_weight_kg)
        ->toEqual(170.0);

    //Recupera lo Gnomo delle Profondità
    $deepGnome = Subrace::query()
        ->where('key', 'deep_gnome_eepc_2015')
        ->firstOrFail();

    expect($deepGnome->physicalTraits->maturity_age_years)
        ->toEqual(25)
        ->and($deepGnome->physicalTraits->lifespan_years)
        ->toEqual(250)
        ->and($deepGnome->physicalTraits->min_height_cm)
        ->toEqual(90.0)
        ->and($deepGnome->physicalTraits->max_height_cm)
        ->toEqual(100.0)
        ->and($deepGnome->physicalTraits->min_weight_kg)
        ->toEqual(40.0)
        ->and($deepGnome->physicalTraits->max_weight_kg)
        ->toEqual(60.0);

    //Le varianti dei Genasi utilizzano i dati fisici
    //della razza principale
    $waterGenasi = Subrace::query()
        ->where('key', 'water_genasi_eepc_2015')
        ->firstOrFail();

    expect($waterGenasi->physicalTraits()->doesntExist())
        ->toBeTrue();
});
