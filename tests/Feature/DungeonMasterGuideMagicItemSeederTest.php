<?php

use App\Models\EffectDefinitionHealing;
use App\Models\EffectDefinitionRollModifier;
use App\Models\Item;
use App\Models\ItemMagicProfile;
use Database\Seeders\DungeonMasterGuideMagicItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce due volte il catalogo per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(DungeonMasterGuideMagicItemSeeder::class);
    $this->seed(DungeonMasterGuideMagicItemSeeder::class);
});

//Verifica la creazione degli oggetti senza duplicati
it('crea i primi oggetti magici del dmg senza duplicati', function () {
    //Elenca tutti gli oggetti attesi nel primo blocco
    $expectedKeys = [
        'potion_of_healing',
        'potion_of_greater_healing',
        'potion_of_superior_healing',
        'potion_of_supreme_healing',
        'weapon_plus_1',
        'weapon_plus_2',
        'weapon_plus_3',
        'bag_of_holding',
    ];

    //Recupera gli identificativi del catalogo appena creato
    $itemIds = Item::query()
        ->where('version_key', 'dmg_2014')
        ->whereIn('key', $expectedKeys)
        ->pluck('id');

    //Verifica oggetti e relativi profili magici
    expect($itemIds)->toHaveCount(8)
        ->and(
            ItemMagicProfile::query()
                ->whereIn('item_id', $itemIds)
                ->count()
        )->toBe(8);
});

//Verifica le formule delle pozioni
it('salva le formule delle pozioni di guarigione', function () {
    //Definisce i valori attesi per ogni pozione
    $expectedFormulas = [
        'potion_of_healing' => [
            2,
            4,
            2,
            7.0,
        ],
        'potion_of_greater_healing' => [
            4,
            4,
            4,
            14.0,
        ],
        'potion_of_superior_healing' => [
            8,
            4,
            8,
            28.0,
        ],
        'potion_of_supreme_healing' => [
            10,
            4,
            20,
            45.0,
        ],
    ];

    //Verifica ogni formula separatamente
    foreach ($expectedFormulas as $itemKey => $formula) {
        $potion = Item::query()
            ->where('key', $itemKey)
            ->firstOrFail();

        $effect = $potion->effectDefinitions()
            ->where('key', 'restore_hit_points')
            ->firstOrFail();

        $healing = $effect->healings()
            ->where('key', 'primary_healing')
            ->firstOrFail();

        expect($healing->dice_count)->toBe($formula[0])
            ->and($healing->die_size)->toBe($formula[1])
            ->and($healing->flat_bonus)->toBe($formula[2])
            ->and($healing->average_healing)->toBe($formula[3])
            ->and($healing->is_primary)->toBeTrue()
            ->and(
                $potion->consumableProfile->consumed_on_use
            )->toBeTrue();
    }

    //Verifica il numero totale delle formule
    expect(EffectDefinitionHealing::query()->count())->toBe(4);
});

//Verifica i bonus delle armi magiche
it('salva i bonus delle armi magiche generiche', function () {
    //Definisce il bonus atteso per ogni modello
    $expectedBonuses = [
        'weapon_plus_1' => 1.0,
        'weapon_plus_2' => 2.0,
        'weapon_plus_3' => 3.0,
    ];

    //Verifica applicabilità e modificatori di ogni arma
    foreach ($expectedBonuses as $itemKey => $bonus) {
        $weapon = Item::query()
            ->where('key', $itemKey)
            ->firstOrFail();

        $applicability = $weapon
            ->magicApplicabilities()
            ->firstOrFail();

        $effect = $weapon->effectDefinitions()
            ->where('key', 'magic_weapon_bonus')
            ->firstOrFail();

        $modifiers = $effect->rollModifiers()
            ->get()
            ->pluck('value', 'roll_type')
            ->all();

        expect($applicability->target_scope)
            ->toBe('any_weapon')
            ->and($applicability->requires_nonmagical)
            ->toBeTrue()
            ->and($modifiers)
            ->toBe([
                'attack' => $bonus,
                'damage' => $bonus,
            ]);
    }

    //Ogni modello possiede due modificatori
    expect(
        EffectDefinitionRollModifier::query()->count()
    )->toBe(6);
});

//Verifica la Borsa Conservante
it('salva capacità e regole della borsa conservante', function () {
    //Recupera la Borsa Conservante
    $bag = Item::query()
        ->where('key', 'bag_of_holding')
        ->firstOrFail();

    //Recupera il profilo del contenitore
    $profile = $bag->containerProfile;

    //Verifica peso, volume e comportamento extradimensionale
    expect($bag->name)->toBe('Borsa Conservante')
        ->and($bag->weight_kg)->toBe(6.804)
        ->and($profile->capacity_weight_kg)->toBe(226.796)
        ->and($profile->capacity_volume_liters)->toBe(1812.278)
        ->and($profile->ignores_contents_weight)->toBeTrue()
        ->and($profile->is_extradimensional)->toBeTrue()
        ->and($profile->retrieval_action)->toBe('action');
});

//Verifica i riferimenti al manuale
it('collega gli oggetti alle pagine del dmg', function () {
    //Definisce alcune pagine rappresentative
    $expectedPages = [
        'bag_of_holding' => 153,
        'potion_of_healing' => 187,
        'weapon_plus_1' => 213,
    ];

    //Verifica ogni riferimento bibliografico
    foreach ($expectedPages as $itemKey => $page) {
        $item = Item::query()
            ->where('key', $itemKey)
            ->firstOrFail();

        $reference = $item->sourceReferences()
            ->where('key', 'dmg_2014_primary')
            ->firstOrFail();

        expect($reference->sourceBook->slug)->toBe('dmg-2014')
            ->and($reference->page_start)->toBe($page)
            ->and($reference->page_end)->toBe($page)
            ->and($reference->is_primary)->toBeTrue()
            ->and($reference->official_text)->toBeNull();
    }
});
