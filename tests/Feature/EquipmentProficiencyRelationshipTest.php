<?php

use App\Models\ArmorProficiency;
use App\Models\ArmorProficiencyItem;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Ruleset;
use App\Models\ToolProficiency;
use App\Models\ToolProficiencyItem;
use App\Models\WeaponProficiency;
use App\Models\WeaponProficiencyItem;
use Database\Seeders\RulesetSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara il regolamento e gli oggetti richiesti dai test
beforeEach(function () {
    //Inserisce il regolamento D&D 5e del 2014
    $this->seed(RulesetSeeder::class);

    //Recupera il regolamento utilizzato dalle competenze
    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Crea la tipologia utilizzata dalle armi
    $weaponType = ItemType::query()->create([
        'key' => 'weapon',
        'name' => 'Arma',
        'sort_order' => 10,
    ]);

    //Crea la tipologia utilizzata dalle armature
    $armorType = ItemType::query()->create([
        'key' => 'armor',
        'name' => 'Armatura',
        'sort_order' => 20,
    ]);

    //Crea la tipologia utilizzata dagli strumenti
    $toolType = ItemType::query()->create([
        'key' => 'tool',
        'name' => 'Strumento',
        'sort_order' => 30,
    ]);

    //Crea un'arma utilizzata dalle verifiche
    $this->weapon = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_weapon',
        'name' => 'Arma di prova',
        'item_type_id' => $weaponType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 10,
    ]);

    //Crea un'armatura utilizzata dalle verifiche
    $this->armor = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_armor',
        'name' => 'Armatura di prova',
        'item_type_id' => $armorType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 20,
    ]);

    //Crea uno strumento utilizzato dalle verifiche
    $this->tool = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_tool',
        'name' => 'Strumento di prova',
        'item_type_id' => $toolType->id,
        'is_stackable' => false,
        'is_magical' => false,
        'requires_attunement' => false,
        'sort_order' => 30,
    ]);
});

//Verifica competenze specifiche e categorie delle armi
it('gestisce le competenze nelle armi', function () {
    //Crea una competenza collegata direttamente a una singola arma
    $specific = WeaponProficiency::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_specific_weapon',
        'name' => 'Arma specifica di prova',
        'type' => 'specific',
        'item_id' => $this->weapon->id,
        'sort_order' => 10,
    ]);

    //Crea una categoria che può comprendere diverse armi
    $category = WeaponProficiency::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_weapon_category',
        'name' => 'Categoria di armi di prova',
        'type' => 'category',
        'item_id' => null,
        'sort_order' => 20,
    ]);

    //Inserisce l'arma nella categoria attraverso la tabella pivot
    $category->items()->attach($this->weapon->id);

    //Ricarica le relazioni utilizzate dalle verifiche
    $category->load('items');

    $this->weapon->load([
        'directWeaponProficiencies',
        'weaponProficiencies',
    ]);

    //Verifica la relazione della competenza specifica
    expect(
        $specific->specificItem->is($this->weapon)
    )->toBeTrue();

    //Verifica le armi comprese nella categoria
    expect($category->items)->toHaveCount(1)
        ->and($category->items->first()->is($this->weapon))
        ->toBeTrue();

    //Verifica le relazioni inverse disponibili sull'oggetto
    expect($this->weapon->directWeaponProficiencies)
        ->toHaveCount(1)
        ->and($this->weapon->weaponProficiencies)
        ->toHaveCount(1);
});

//Verifica le competenze nelle armature e negli strumenti
it('gestisce le competenze nelle armature e negli strumenti', function () {
    //Crea una categoria di armature
    $armorProficiency = ArmorProficiency::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_armor_category',
        'name' => 'Categoria di armature di prova',
        'type' => 'category',
        'item_id' => null,
        'sort_order' => 10,
    ]);

    //Crea una competenza collegata direttamente a uno strumento
    $toolProficiency = ToolProficiency::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_specific_tool',
        'name' => 'Strumento specifico di prova',
        'type' => 'specific',
        'item_id' => $this->tool->id,
        'sort_order' => 10,
    ]);

    //Inserisce l'armatura nella sua categoria
    $armorProficiency->items()->attach(
        $this->armor->id
    );

    //Verifica il contenuto della categoria di armature
    expect(
        $armorProficiency->items()->count()
    )->toBe(1)
        ->and(
            $armorProficiency
                ->items()
                ->first()
                ->is($this->armor)
        )
        ->toBeTrue();

    //Verifica lo strumento della competenza specifica
    expect(
        $toolProficiency->specificItem->is($this->tool)
    )->toBeTrue();
});

//Verifica i controlli applicati prima del salvataggio
it('rifiuta configurazioni incoerenti delle competenze', function () {
    //Una competenza specifica non può essere priva dell'arma
    expect(
        fn () => WeaponProficiency::query()->create([
            'ruleset_id' => $this->ruleset->id,
            'key' => 'invalid_specific_weapon',
            'name' => 'Competenza specifica non valida',
            'type' => 'specific',
            'item_id' => null,
            'sort_order' => 10,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Una categoria non può indicare direttamente un'armatura
    expect(
        fn () => ArmorProficiency::query()->create([
            'ruleset_id' => $this->ruleset->id,
            'key' => 'invalid_armor_category',
            'name' => 'Categoria di armature non valida',
            'type' => 'category',
            'item_id' => $this->armor->id,
            'sort_order' => 10,
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Una competenza personalizzata utilizza le assegnazioni
    //e non può indicare direttamente uno strumento
    expect(
        fn () => ToolProficiency::query()->create([
            'ruleset_id' => $this->ruleset->id,
            'key' => 'invalid_custom_tool',
            'name' => 'Competenza personalizzata non valida',
            'type' => 'custom',
            'item_id' => $this->tool->id,
            'sort_order' => 10,
        ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica il vincolo univoco della tabella pivot
it('rifiuta oggetti duplicati nella stessa competenza', function () {
    //Crea una categoria di competenza
    $proficiency = WeaponProficiency::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'unique_weapon_category',
        'name' => 'Categoria univoca di prova',
        'type' => 'category',
        'item_id' => null,
        'sort_order' => 10,
    ]);

    //Inserisce una prima volta l'arma
    $proficiency->items()->attach($this->weapon->id);

    //Verifica che la stessa arma non possa essere reinserita
    expect(
        fn () => $proficiency
            ->items()
            ->attach($this->weapon->id)
    )->toThrow(QueryException::class);
});

//Verifica le eliminazioni a cascata delle assegnazioni
it('elimina le assegnazioni quando viene eliminato un oggetto', function () {
    //Crea una categoria temporanea di armi
    $weaponProficiency = WeaponProficiency::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'temporary_weapon_category',
        'name' => 'Categoria temporanea di armi',
        'type' => 'category',
        'item_id' => null,
        'sort_order' => 10,
    ]);

    //Crea una categoria temporanea di armature
    $armorProficiency = ArmorProficiency::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'temporary_armor_category',
        'name' => 'Categoria temporanea di armature',
        'type' => 'category',
        'item_id' => null,
        'sort_order' => 10,
    ]);

    //Crea una categoria temporanea di strumenti
    $toolProficiency = ToolProficiency::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'temporary_tool_category',
        'name' => 'Categoria temporanea di strumenti',
        'type' => 'category',
        'item_id' => null,
        'sort_order' => 10,
    ]);

    //Collega gli oggetti alle rispettive categorie
    $weaponProficiency->items()->attach(
        $this->weapon->id
    );

    $armorProficiency->items()->attach(
        $this->armor->id
    );

    $toolProficiency->items()->attach(
        $this->tool->id
    );

    //Elimina gli oggetti proprietari delle assegnazioni
    $this->weapon->delete();
    $this->armor->delete();
    $this->tool->delete();

    //Verifica che tutte le righe pivot siano state eliminate
    expect(
        WeaponProficiencyItem::query()->count()
    )->toBe(0)
        ->and(
            ArmorProficiencyItem::query()->count()
        )
        ->toBe(0)
        ->and(
            ToolProficiencyItem::query()->count()
        )
        ->toBe(0);

    //Le competenze appartengono al catalogo e devono rimanere
    expect(
        WeaponProficiency::query()->count()
    )->toBe(1)
        ->and(
            ArmorProficiency::query()->count()
        )
        ->toBe(1)
        ->and(
            ToolProficiency::query()->count()
        )
        ->toBe(1);
});
