<?php

use App\Models\Item;
use App\Models\ItemMagicProfile;
use App\Models\ItemResource;
use App\Models\ItemType;
use App\Models\Ruleset;
use Database\Seeders\ItemTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Crea i cataloghi richiesti dagli oggetti
beforeEach(function () {
    $this->seed([
        RulesetSeeder::class,
        ItemTypeSeeder::class,
    ]);

    //Recupera il regolamento utilizzato dal test
    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera la tipologia oggetto meraviglioso
    $this->itemType = ItemType::query()
        ->where('key', 'wondrous_item')
        ->firstOrFail();
});

//Verifica il profilo magico e l'oggetto comune di base
it('gestisce il profilo di un oggetto magico', function () {
    //Crea l'oggetto comune utilizzato come base
    $baseItem = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_base_item',
        'name' => 'Oggetto Base di Prova',
        'item_type_id' => $this->itemType->id,
        'rarity' => 'common',
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 1,
    ]);

    //Crea la versione magica
    $magicItem = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_magic_item',
        'name' => 'Oggetto Magico di Prova',
        'item_type_id' => $this->itemType->id,
        'rarity' => 'rare',
        'is_magical' => true,
        'requires_attunement' => true,
        'sort_order' => 2,
    ]);

    //Crea il profilo magico
    $profile = $magicItem->magicProfile()->create([
        'base_item_id' => $baseItem->id,
        'attunement_requirement' =>
            'Richiede sintonia da parte di un incantatore.',
        'is_cursed' => true,
        'curse_disclosure' => null,
        'destruction_condition' =>
            'Può essere distrutto soltanto in una circostanza speciale.',
    ]);

    //Verifica proprietario, oggetto base e maledizione
    expect($profile->item->is($magicItem))
        ->toBeTrue()
        ->and($profile->baseItem->is($baseItem))
        ->toBeTrue()
        ->and($profile->is_cursed)
        ->toBeTrue()
        ->and($profile->curse_disclosure)
        ->toBe('hidden');
});

//Verifica cariche e formula di recupero
it('gestisce cariche e recupero con dadi', function () {
    //Crea un oggetto magico con cariche
    $magicItem = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'charged_magic_item',
        'name' => 'Oggetto a Cariche',
        'item_type_id' => $this->itemType->id,
        'rarity' => 'very_rare',
        'is_magical' => true,
        'requires_attunement' => true,
        'sort_order' => 1,
    ]);

    //Crea la risorsa delle cariche
    $charges = $magicItem->resources()->create([
        'key' => 'charges',
        'name' => 'Cariche',
        'resource_type' => 'charges',
        'maximum' => 7,
        'expended_per_use' => 1,
        'recharge_type' => 'dawn',
        'recharge_all' => false,
        'recharge_fixed' => null,
        'recharge_dice_count' => 1,
        'recharge_die_size' => 6,
        'recharge_bonus' => 1,
        'empty_behavior' => 'roll_destroy',
        'empty_behavior_condition' =>
            'Quando viene spesa l’ultima carica, tira 1d20. '
            . 'Con un risultato di 1 l’oggetto viene distrutto.',
        'sort_order' => 1,
    ]);

    //Verifica la formula e le proprietà principali
    expect($charges->maximum)
        ->toBe(7)
        ->and($charges->recharge_formula)
        ->toBe('1d6+1')
        ->and($charges->empty_behavior)
        ->toBe('roll_destroy')
        ->and($charges->item->is($magicItem))
        ->toBeTrue();
});

//Verifica il recupero completo della risorsa
it('gestisce il recupero completo durante un riposo', function () {
    //Crea l'oggetto utilizzato dal test
    $magicItem = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'rest_recharge_item',
        'name' => 'Oggetto con Recupero',
        'item_type_id' => $this->itemType->id,
        'rarity' => 'uncommon',
        'is_magical' => true,
        'requires_attunement' => false,
        'sort_order' => 1,
    ]);

    //Crea una risorsa recuperata completamente
    $resource = $magicItem->resources()->create([
        'key' => 'uses',
        'name' => 'Utilizzi',
        'resource_type' => 'uses',
        'maximum' => 1,
        'expended_per_use' => 1,
        'recharge_type' => 'long_rest',
        'recharge_all' => true,
        'empty_behavior' => 'inactive',
        'sort_order' => 1,
    ]);

    //Verifica il recupero completo
    expect($resource->recharge_all)
        ->toBeTrue()
        ->and($resource->recharge_formula)
        ->toBe('all');
});

//Verifica che un oggetto possa generare effetti
it('collega gli effetti agli oggetti', function () {
    //Crea un oggetto magico
    $magicItem = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'effect_magic_item',
        'name' => 'Oggetto con Effetto',
        'item_type_id' => $this->itemType->id,
        'rarity' => 'rare',
        'is_magical' => true,
        'requires_attunement' => false,
        'sort_order' => 1,
    ]);

    //Crea un effetto attraverso la relazione polimorfica
    $effect = $magicItem->effectDefinitions()->create([
        'key' => 'automatic_effect',
        'name' => 'Effetto Automatico',
        'application_type' => 'automatic',
        'target_scope' => 'source',
        'ends_with_source' => true,
        'description' =>
            'Effetto creato soltanto per verificare la relazione.',
        'sort_order' => 1,
    ]);

    //Verifica entrambe le direzioni della relazione
    expect($effect->source->is($magicItem))
        ->toBeTrue()
        ->and($magicItem->effectDefinitions()->count())
        ->toBe(1);
});

//Verifica il rifiuto delle configurazioni incoerenti
it('rifiuta profili e risorse magiche non validi', function () {
    //Crea un oggetto non magico
    $mundaneItem = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'mundane_test_item',
        'name' => 'Oggetto Comune',
        'item_type_id' => $this->itemType->id,
        'rarity' => 'common',
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 1,
    ]);

    //Un oggetto comune non può ricevere un profilo magico
    expect(
        fn () => $mundaneItem->magicProfile()->create([
            'is_cursed' => false,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Una risorsa senza recupero non può avere una quantità recuperata
    expect(
        fn () => $mundaneItem->resources()->create([
            'key' => 'invalid_resource',
            'name' => 'Risorsa non valida',
            'resource_type' => 'uses',
            'maximum' => 3,
            'expended_per_use' => 1,
            'recharge_type' => 'none',
            'recharge_fixed' => 1,
            'empty_behavior' => 'inactive',
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Non è possibile recuperare tutto e una quantità fissa insieme
    expect(
        fn () => $mundaneItem->resources()->create([
            'key' => 'conflicting_recharge',
            'name' => 'Recupero incompatibile',
            'resource_type' => 'uses',
            'maximum' => 3,
            'expended_per_use' => 1,
            'recharge_type' => 'long_rest',
            'recharge_all' => true,
            'recharge_fixed' => 1,
            'empty_behavior' => 'inactive',
        ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica la cancellazione a cascata
it('elimina profilo risorse ed effetti insieme all oggetto', function () {
    //Crea l'oggetto magico da eliminare
    $magicItem = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'deletable_magic_item',
        'name' => 'Oggetto Eliminabile',
        'item_type_id' => $this->itemType->id,
        'rarity' => 'rare',
        'is_magical' => true,
        'requires_attunement' => false,
        'sort_order' => 1,
    ]);

    //Crea profilo, risorsa ed effetto
    $profile = $magicItem->magicProfile()->create([
        'is_cursed' => false,
    ]);

    $resource = $magicItem->resources()->create([
        'key' => 'uses',
        'name' => 'Utilizzi',
        'resource_type' => 'uses',
        'maximum' => 1,
        'expended_per_use' => 1,
        'recharge_type' => 'none',
        'empty_behavior' => 'consume',
    ]);

    $effect = $magicItem->effectDefinitions()->create([
        'key' => 'temporary_effect',
        'name' => 'Effetto Temporaneo',
        'application_type' => 'automatic',
        'target_scope' => 'source',
        'ends_with_source' => true,
    ]);

    //Elimina l'oggetto
    $magicItem->delete();

    //Verifica la cancellazione dei record collegati
    expect(ItemMagicProfile::query()->find($profile->id))
        ->toBeNull()
        ->and(ItemResource::query()->find($resource->id))
        ->toBeNull()
        ->and($effect->fresh())
        ->toBeNull();
});
