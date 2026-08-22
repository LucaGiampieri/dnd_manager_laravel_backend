<?php

use App\Models\Item;
use App\Models\ItemCost;
use App\Models\ItemWeaponDamage;
use App\Models\ItemWeaponProfile;
use App\Models\ItemWeaponProperty;
use Database\Seeders\WeaponSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Inserisce tutte le armi prima di ogni verifica
beforeEach(function () {
    $this->seed(WeaponSeeder::class);
});

//Verifica quantità, chiavi e idempotenza del catalogo
it('crea tutte le armi del phb senza duplicati', function () {
    //Ripete il seeder per verificarne l'idempotenza
    $this->seed(WeaponSeeder::class);

    //Definisce le chiavi delle 37 armi nell'ordine previsto
    $expectedKeys = [
        'club',
        'dagger',
        'greatclub',
        'handaxe',
        'javelin',
        'light_hammer',
        'mace',
        'quarterstaff',
        'sickle',
        'spear',
        'light_crossbow',
        'dart',
        'shortbow',
        'sling',
        'battleaxe',
        'flail',
        'glaive',
        'greataxe',
        'greatsword',
        'halberd',
        'lance',
        'longsword',
        'maul',
        'morningstar',
        'pike',
        'rapier',
        'scimitar',
        'shortsword',
        'trident',
        'war_pick',
        'warhammer',
        'whip',
        'blowgun',
        'hand_crossbow',
        'heavy_crossbow',
        'longbow',
        'net',
    ];

    //Recupera le armi nel loro ordine ufficiale
    $weapons = Item::query()
        ->where('version_key', 'phb_2014')
        ->orderBy('sort_order')
        ->get();

    //Verifica quantità, chiavi, versioni e ordinamento
    expect($weapons)->toHaveCount(37)
        ->and($weapons->pluck('key')->all())
        ->toBe($expectedKeys)
        ->and($weapons->pluck('sort_order')->all())
        ->toBe(range(10, 370, 10))
        ->and(
            $weapons->every(
                fn (Item $weapon): bool =>
                    $weapon->canonical_key === $weapon->key
                    && $weapon->is_legacy === false
            )
        )
        ->toBeTrue();

    //Verifica la quantità delle relazioni meccaniche
    expect(ItemCost::query()->count())->toBe(37)
        ->and(ItemWeaponProfile::query()->count())->toBe(37)
        ->and(ItemWeaponDamage::query()->count())->toBe(36)
        ->and(ItemWeaponProperty::query()->count())->toBe(65);
});

//Verifica le categorie e le modalità di attacco
it('assegna categorie e modalità di attacco corrette', function () {
    //Conta le armi appartenenti alle categorie principali
    expect(
        ItemWeaponProfile::query()
            ->where('weapon_category', 'simple')
            ->count()
    )->toBe(14)
        ->and(
            ItemWeaponProfile::query()
                ->where('weapon_category', 'martial')
                ->count()
        )
        ->toBe(23);

    //Conta le armi da mischia e a distanza
    expect(
        ItemWeaponProfile::query()
            ->where('attack_type', 'melee')
            ->count()
    )->toBe(28)
        ->and(
            ItemWeaponProfile::query()
                ->where('attack_type', 'ranged')
                ->count()
        )
        ->toBe(9);
});

//Verifica prezzi, pesi, gittate e formule del danno
it('salva i valori meccanici delle armi', function () {
    //Recupera alcune armi rappresentative
    $longsword = Item::query()
        ->where('key', 'longsword')
        ->with([
            'costs.currency',
            'weaponProfile.damages',
        ])
        ->firstOrFail();

    $greatsword = Item::query()
        ->where('key', 'greatsword')
        ->with('weaponProfile.damages')
        ->firstOrFail();

    $longbow = Item::query()
        ->where('key', 'longbow')
        ->with('weaponProfile')
        ->firstOrFail();

    $blowgun = Item::query()
        ->where('key', 'blowgun')
        ->with('weaponProfile.damages')
        ->firstOrFail();

    $net = Item::query()
        ->where('key', 'net')
        ->with('weaponProfile.damages')
        ->firstOrFail();

    //Verifica prezzo e peso della Spada Lunga
    expect($longsword->costs)->toHaveCount(1)
        ->and($longsword->costs->first()->amount)->toBe(15)
        ->and(
            $longsword
                ->costs
                ->first()
                ->currency
                ->value_in_copper_pieces
        )
        ->toBe(100)
        ->and($longsword->weight_kg)->toBe(1.361);

    //Verifica le formule di danno normali e fisse
    expect(
        $greatsword->weaponProfile->damages->first()->formula
    )->toBe('2d6')
        ->and(
            $blowgun->weaponProfile->damages->first()->formula
        )
        ->toBe('1')
        ->and($net->weaponProfile->damages)
        ->toHaveCount(0);

    //Verifica la gittata dell'Arco Lungo
    expect($longbow->weaponProfile->range)->toBe(45.0)
        ->and($longbow->weaponProfile->long_range)
        ->toBe(180.0);
});

//Verifica le proprietà assegnate alle armi
it('assegna le proprietà corrette alle armi', function () {
    //Recupera armi con combinazioni differenti di proprietà
    $dagger = Item::query()
        ->where('key', 'dagger')
        ->with('weaponProfile.properties')
        ->firstOrFail();

    $heavyCrossbow = Item::query()
        ->where('key', 'heavy_crossbow')
        ->with('weaponProfile.properties')
        ->firstOrFail();

    $quarterstaff = Item::query()
        ->where('key', 'quarterstaff')
        ->with('weaponProfile.properties')
        ->firstOrFail();

    //Verifica le proprietà del Pugnale
    expect(
        $dagger
            ->weaponProfile
            ->properties
            ->pluck('key')
            ->all()
    )->toBe([
        'finesse',
        'light',
        'thrown',
    ]);

    //Verifica le proprietà della Balestra Pesante
    expect(
        $heavyCrossbow
            ->weaponProfile
            ->properties
            ->pluck('key')
            ->all()
    )->toBe([
        'ammunition',
        'heavy',
        'loading',
        'two_handed',
    ]);

    //Verifica il danno alternativo del Bastone Ferrato
    expect(
        $quarterstaff
            ->weaponProfile
            ->properties
            ->firstWhere('key', 'versatile')
            ->pivot
            ->value_text
    )->toBe('1d8');
});

//Verifica le regole delle armi speciali
it('salva le regole speciali di lancia da cavaliere e rete', function () {
    //Recupera i profili delle due armi speciali
    $lance = Item::query()
        ->where('key', 'lance')
        ->with('weaponProfile')
        ->firstOrFail();

    $net = Item::query()
        ->where('key', 'net')
        ->with('weaponProfile')
        ->firstOrFail();

    //Verifica le informazioni principali delle regole speciali
    expect($lance->weaponProfile->notes)
        ->toContain('svantaggio')
        ->and($lance->weaponProfile->notes)
        ->toContain('in sella')
        ->and($net->weaponProfile->notes)
        ->toContain('Intralciata')
        ->and($net->weaponProfile->notes)
        ->toContain('non infligge danni');
});
