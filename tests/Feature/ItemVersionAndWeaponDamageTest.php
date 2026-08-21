<?php

use App\Models\DamageType;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Ruleset;
use Database\Seeders\DamageTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara il regolamento e i cataloghi utilizzati dai test
beforeEach(function () {
    $this->seed([
        RulesetSeeder::class,
        DamageTypeSeeder::class,
    ]);

    //Recupera il regolamento D&D 5e del 2014
    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera un tipo di danno utilizzato dalle armi di prova
    $this->damageType = DamageType::query()
        ->firstOrFail();

    //Crea la tipologia delle armi
    $this->weaponType = ItemType::query()->create([
        'key' => 'weapon',
        'name' => 'Arma',
        'sort_order' => 10,
    ]);
});

//Verifica i valori automatici degli oggetti personalizzati
it('assegna valori di versione agli oggetti personalizzati', function () {
    //Crea un oggetto senza specificare il versionamento
    $item = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'custom_weapon',
        'name' => 'Arma personalizzata',
        'item_type_id' => $this->weaponType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    //Verifica i valori generati automaticamente
    expect($item->canonical_key)->toBe('custom_weapon')
        ->and($item->version_key)->toBe('custom')
        ->and($item->is_legacy)->toBeFalse();
});

//Verifica la presenza di versioni differenti dello stesso oggetto
it('permette versioni differenti dello stesso oggetto', function () {
    //Crea la versione originale di una spada
    Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_sword_phb_2014',
        'canonical_key' => 'test_sword',
        'version_key' => 'phb_2014',
        'is_legacy' => false,
        'name' => 'Spada di prova',
        'item_type_id' => $this->weaponType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    //Crea una seconda versione meccanica della stessa spada
    Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_sword_revised',
        'canonical_key' => 'test_sword',
        'version_key' => 'revised',
        'is_legacy' => false,
        'name' => 'Spada di prova revisionata',
        'item_type_id' => $this->weaponType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 20,
    ]);

    //Verifica che entrambe le versioni siano disponibili
    expect(
        Item::query()
            ->where('canonical_key', 'test_sword')
            ->count()
    )->toBe(2);

    //Verifica che la stessa versione non possa essere duplicata
    expect(
        fn () => Item::query()->create([
            'ruleset_id' => $this->ruleset->id,
            'key' => 'third_test_sword',
            'canonical_key' => 'test_sword',
            'version_key' => 'phb_2014',
            'name' => 'Duplicato della spada',
            'item_type_id' => $this->weaponType->id,
            'is_stackable' => false,
            'is_magical' => false,
            'requires_attunement' => false,
            'sort_order' => 30,
        ])
    )->toThrow(QueryException::class);
});

//Verifica danni basati sui dadi e danni fissi
it('rappresenta formule con dadi e danni fissi', function () {
    //Crea un'arma di prova
    $weapon = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'fixed_damage_weapon',
        'name' => 'Arma con danno fisso',
        'item_type_id' => $this->weaponType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    //Crea il profilo meccanico dell'arma
    $profile = $weapon->weaponProfile()->create([
        'weapon_category' => 'martial',
        'attack_type' => 'ranged',
        'range' => 7.5,
        'long_range' => 30,
        'uses_ammunition' => true,
        'capacity' => null,
    ]);

    //Crea un normale danno basato su un dado
    $diceDamage = $profile->damages()->create([
        'damage_type_id' => $this->damageType->id,
        'dice_count' => 1,
        'die_size' => 8,
        'bonus' => 0,
        'primary' => true,
        'sort_order' => 10,
    ]);

    //Crea un danno fisso privo di dadi
    $fixedDamage = $profile->damages()->create([
        'damage_type_id' => $this->damageType->id,
        'dice_count' => null,
        'die_size' => null,
        'bonus' => 1,
        'primary' => false,
        'sort_order' => 20,
    ]);

    //Verifica le formule calcolate
    expect($diceDamage->formula)->toBe('1d8')
        ->and($fixedDamage->formula)->toBe('1');
});

//Verifica le combinazioni di danno non valide
it('rifiuta formule del danno incomplete o vuote', function () {
    //Crea un'arma e il relativo profilo
    $weapon = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'invalid_damage_weapon',
        'name' => 'Arma con danno non valido',
        'item_type_id' => $this->weaponType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    $profile = $weapon->weaponProfile()->create([
        'weapon_category' => 'simple',
        'attack_type' => 'melee',
        'range' => 1.5,
        'long_range' => null,
        'uses_ammunition' => false,
        'capacity' => null,
    ]);

    //Rifiuta una formula che indica soltanto il numero dei dadi
    expect(
        fn () => $profile->damages()->create([
            'damage_type_id' => $this->damageType->id,
            'dice_count' => 1,
            'die_size' => null,
            'bonus' => 0,
            'primary' => true,
            'sort_order' => 10,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Rifiuta un danno privo sia di dadi sia di valore fisso
    expect(
        fn () => $profile->damages()->create([
            'damage_type_id' => $this->damageType->id,
            'dice_count' => null,
            'die_size' => null,
            'bonus' => 0,
            'primary' => true,
            'sort_order' => 10,
        ])
    )->toThrow(\InvalidArgumentException::class);
});
