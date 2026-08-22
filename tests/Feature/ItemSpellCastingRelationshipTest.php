<?php

use App\Models\Item;
use App\Models\ItemSpellCasting;
use App\Models\ItemType;
use App\Models\Ruleset;
use App\Models\Spell;
use App\Models\SpellSchool;
use Database\Seeders\ItemTypeSeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SpellSchoolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Crea i cataloghi e i dati utilizzati da ogni test
beforeEach(function () {
    //Inserisce regolamento, scuole di magia e tipologie di oggetto
    $this->seed([
        RulesetSeeder::class,
        SpellSchoolSeeder::class,
        ItemTypeSeeder::class,
    ]);

    //Recupera il regolamento utilizzato dal test
    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera una scuola disponibile
    $this->spellSchool = SpellSchool::query()
        ->firstOrFail();

    //Recupera la tipologia delle bacchette
    $this->itemType = ItemType::query()
        ->where('key', 'wand')
        ->firstOrFail();

    //Crea un incantesimo rappresentativo
    $this->spell = Spell::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_magic_missile',
        'name' => 'Dardo Incantato di prova',
        'level' => 1,
        'spell_school_id' => $this->spellSchool->id,
        'casting_time_value' => 1,
        'casting_time_type' => 'action',
        'casting_trigger' => null,
        'range_type' => 'distance',
        'range' => 36,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => false,
        'material_description' => null,
        'material_consumed' => false,
        'material_cost' => null,
        'duration_type' => 'instantaneous',
        'duration_value' => null,
        'concentration' => false,
        'ritual' => false,
        'attack_type' => null,
        'saving_throw_ability_id' => null,
        'save_success_damage' => null,
        'description' => 'Incantesimo utilizzato soltanto nel test.',
        'higher_levels' => null,
        'notes' => null,
    ]);

    //Crea una bacchetta magica rappresentativa
    $this->item = Item::query()->create([
        'ruleset_id' => $this->ruleset->id,
        'key' => 'test_magic_wand',
        'canonical_key' => 'test_magic_wand',
        'version_key' => 'test',
        'is_legacy' => false,
        'name' => 'Bacchetta magica di prova',
        'item_type_id' => $this->itemType->id,
        'description' => 'Oggetto utilizzato soltanto nel test.',
        'weight_kg' => 0.5,
        'is_stackable' => false,
        'rarity' => 'uncommon',
        'is_magical' => true,
        'requires_attunement' => false,
        'requirements' => null,
        'notes' => null,
        'sort_order' => 1,
    ]);
});

//Verifica il collegamento tra oggetti e incantesimi
it('collega gli incantesimi agli oggetti', function () {
    //Crea il lancio concesso dalla bacchetta
    $casting = $this->item->spellCastings()->create([
        'spell_id' => $this->spell->id,
        'key' => 'cast_magic_missile',
        'activation_type' => 'action',
        'activation_value' => 1,
        'resource_cost' => 0,
        'cast_at_level' => 1,
        'save_dc' => null,
        'spell_attack_bonus' => null,
        'requires_components' => false,
        'requires_concentration' => null,
        'condition' => null,
        'description' => 'Lancia Dardo Incantato.',
        'sort_order' => 10,
        'notes' => null,
    ]);

    //Verifica le relazioni dirette del lancio
    expect($casting->item->is($this->item))->toBeTrue()
        ->and($casting->spell->is($this->spell))->toBeTrue();

    //Verifica le relazioni molti-a-molti
    expect($this->item->spells()->count())->toBe(1)
        ->and($this->spell->items()->count())->toBe(1);

    //Verifica le proprietà principali dell'attivazione
    expect($casting->activation_type)->toBe('action')
        ->and($casting->activation_value)->toBe(1)
        ->and($casting->resource_cost)->toBe(0)
        ->and($casting->cast_at_level)->toBe(1)
        ->and($casting->requires_components)->toBeFalse();
});

//Verifica i valori predefiniti di un lancio
it('applica i valori predefiniti ai lanci degli oggetti', function () {
    //Crea un lancio che utilizza le regole normali dell'incantesimo
    $casting = $this->item->spellCastings()->create([
        'spell_id' => $this->spell->id,
        'key' => 'default_magic_missile',
    ]);

    //Verifica i valori generati automaticamente
    expect($casting->activation_type)
        ->toBe('spell_casting_time')
        ->and($casting->activation_value)
        ->toBe(1)
        ->and($casting->resource_cost)
        ->toBe(0)
        ->and($casting->requires_components)
        ->toBeFalse()
        ->and($casting->sort_order)
        ->toBe(0);
});

//Rifiuta un costo privo della relativa risorsa
it('rifiuta costi senza una risorsa dell oggetto', function () {
    //Tenta di creare un lancio con un costo non collegato
    expect(
        fn () => $this->item->spellCastings()->create([
            'spell_id' => $this->spell->id,
            'key' => 'invalid_resource_cost',
            'resource_cost' => 1,
        ])
    )->toThrow(\InvalidArgumentException::class);
});

//Rifiuta un livello inferiore a quello base
it('rifiuta livelli di lancio inferiori all incantesimo', function () {
    //Tenta di lanciare un incantesimo di livello 1 come livello 0
    expect(
        fn () => $this->item->spellCastings()->create([
            'spell_id' => $this->spell->id,
            'key' => 'invalid_cast_level',
            'cast_at_level' => 0,
        ])
    )->toThrow(\InvalidArgumentException::class);
});

//Elimina automaticamente i collegamenti orfani
it('elimina i lanci quando viene cancellato l oggetto', function () {
    //Crea un lancio da eliminare insieme alla bacchetta
    $casting = $this->item->spellCastings()->create([
        'spell_id' => $this->spell->id,
        'key' => 'temporary_magic_missile',
    ]);

    //Memorizza l'identificativo prima dell'eliminazione
    $castingId = $casting->id;

    //Elimina l'oggetto proprietario
    $this->item->delete();

    //Verifica la cancellazione tramite vincolo esterno
    expect(
        ItemSpellCasting::query()->find($castingId)
    )->toBeNull();
});
