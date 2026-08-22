<?php

use App\Models\Item;
use App\Models\ItemContainerProfile;
use App\Models\ItemConsumableProfile;
use App\Models\ItemMagicApplicability;
use App\Models\ItemType;
use App\Models\Ruleset;
use Database\Seeders\ItemTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Crea il regolamento e le tipologie utilizzati dai test
beforeEach(function () {
    //Inserisce i cataloghi necessari
    $this->seed([
        RulesetSeeder::class,
        ItemTypeSeeder::class,
    ]);

    //Recupera il regolamento della quinta edizione 2014
    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera le tipologie utilizzate dai test
    $this->potionType = ItemType::query()
        ->where('key', 'potion')
        ->firstOrFail();

    $this->wondrousType = ItemType::query()
        ->where('key', 'wondrous_item')
        ->firstOrFail();

    $this->weaponType = ItemType::query()
        ->where('key', 'weapon')
        ->firstOrFail();

    //Prepara una funzione per creare oggetti di prova
    $this->createItem = function (
        string $key,
        string $name,
        ItemType $itemType,
        bool $isMagical = true
    ): Item {
        return Item::query()->create([
            'ruleset_id' => $this->ruleset->id,
            'key' => $key,
            'canonical_key' => $key,
            'version_key' => 'test',
            'is_legacy' => false,
            'name' => $name,
            'item_type_id' => $itemType->id,
            'description' => 'Oggetto utilizzato soltanto nel test.',
            'weight_kg' => null,
            'is_stackable' => false,
            'rarity' => $isMagical ? 'uncommon' : 'common',
            'is_magical' => $isMagical,
            'requires_attunement' => false,
            'requirements' => null,
            'notes' => null,
            'sort_order' => 1,
        ]);
    };
});

//Verifica le regole dei consumabili
it('gestisce i profili degli oggetti consumabili', function () {
    //Crea una pozione magica di prova
    $potion = ($this->createItem)(
        'test_healing_potion',
        'Pozione di prova',
        $this->potionType
    );

    //Crea il profilo di utilizzo della pozione
    $profile = $potion->consumableProfile()->create([
        'activation_type' => 'drink',
        'activation_action' => 'action',
        'activation_value' => 1,
        'target_scope' => 'self_or_creature',
        'uses_per_item' => 1,
        'consumed_on_use' => true,
        'leaves_container' => true,
        'special_rules' => null,
        'notes' => null,
    ]);

    //Verifica la relazione e i valori meccanici
    expect($profile->item->is($potion))->toBeTrue()
        ->and($potion->consumableProfile->is($profile))->toBeTrue()
        ->and($profile->activation_type)->toBe('drink')
        ->and($profile->uses_per_item)->toBe(1)
        ->and($profile->consumed_on_use)->toBeTrue()
        ->and($profile->leaves_container)->toBeTrue();
});

//Verifica le capacità dei contenitori extradimensionali
it('gestisce i profili dei contenitori', function () {
    //Crea una Borsa Conservante di prova
    $bag = ($this->createItem)(
        'test_bag_of_holding',
        'Borsa Conservante di prova',
        $this->wondrousType
    );

    //Crea il profilo del contenitore
    $profile = $bag->containerProfile()->create([
        'capacity_weight_kg' => 226.796,
        'capacity_volume_liters' => 1812.278,
        'ignores_contents_weight' => true,
        'is_extradimensional' => true,
        'retrieval_action' => 'action',
        'dimensions' => 'Spazio interno più ampio di quello esterno.',
        'living_creature_rules' =>
            'Le creature dispongono di aria limitata.',
        'rupture_rules' =>
            'La rottura disperde il contenuto nel Piano Astrale.',
        'nesting_rules' =>
            'L’interazione con altri spazi extradimensionali '
            . 'produce un varco planare.',
        'notes' => null,
    ]);

    //Verifica capacità, comportamento e relazione
    expect($profile->item->is($bag))->toBeTrue()
        ->and($profile->capacity_weight_kg)->toBe(226.796)
        ->and($profile->capacity_volume_liters)->toBe(1812.278)
        ->and($profile->ignores_contents_weight)->toBeTrue()
        ->and($profile->is_extradimensional)->toBeTrue()
        ->and($profile->retrieval_action)->toBe('action');
});

//Verifica i modelli magici applicabili a qualsiasi arma
it('gestisce l applicabilità degli oggetti magici generici', function () {
    //Crea il modello generico Arma +1
    $magicWeapon = ($this->createItem)(
        'test_weapon_plus_one',
        'Arma +1 di prova',
        $this->weaponType
    );

    //Definisce l'applicabilità a qualsiasi arma non magica
    $applicability = $magicWeapon
        ->magicApplicabilities()
        ->create([
            'key' => 'any_nonmagical_weapon',
            'target_scope' => 'any_weapon',
            'target_item_id' => null,
            'target_item_type_id' => null,
            'weapon_category' => null,
            'armor_category' => null,
            'requires_nonmagical' => true,
            'condition' => null,
            'sort_order' => 10,
            'notes' => null,
        ]);

    //Verifica la relazione e l'ambito generico
    expect($applicability->item->is($magicWeapon))->toBeTrue()
        ->and($applicability->target_scope)->toBe('any_weapon')
        ->and($applicability->requires_nonmagical)->toBeTrue()
        ->and($magicWeapon->magicApplicabilities()->count())->toBe(1);
});

//Verifica il rifiuto di configurazioni incoerenti
it('rifiuta profili di utilizzo non validi', function () {
    //Crea un contenitore magico di prova
    $bag = ($this->createItem)(
        'invalid_container',
        'Contenitore non valido',
        $this->wondrousType
    );

    //Rifiuta una capacità di peso negativa
    expect(
        fn () => $bag->containerProfile()->create([
            'capacity_weight_kg' => -1,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Crea un modello magico di arma
    $magicWeapon = ($this->createItem)(
        'invalid_magic_weapon',
        'Arma magica non valida',
        $this->weaponType
    );

    //Rifiuta un ambito generico con un riferimento specifico
    expect(
        fn () => $magicWeapon
            ->magicApplicabilities()
            ->create([
                'key' => 'invalid_any_weapon',
                'target_scope' => 'any_weapon',
                'target_item_type_id' => $this->weaponType->id,
            ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica la pulizia automatica tramite vincoli esterni
it('elimina i profili insieme agli oggetti', function () {
    //Crea una pozione e il relativo profilo
    $potion = ($this->createItem)(
        'temporary_potion',
        'Pozione temporanea',
        $this->potionType
    );

    $profile = $potion->consumableProfile()->create([
        'activation_type' => 'drink',
    ]);

    //Memorizza gli identificativi prima dell'eliminazione
    $profileId = $profile->id;
    $itemId = $potion->id;

    //Elimina l'oggetto proprietario
    $potion->delete();

    //Verifica l'eliminazione del profilo e dell'oggetto
    expect(
        ItemConsumableProfile::query()->find($profileId)
    )->toBeNull()
        ->and(Item::query()->find($itemId))
        ->toBeNull();

    //Verifica che le altre tabelle siano ancora vuote
    expect(ItemContainerProfile::query()->count())->toBe(0)
        ->and(ItemMagicApplicability::query()->count())->toBe(0);
});
