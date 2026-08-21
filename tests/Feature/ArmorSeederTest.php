<?php

use App\Models\Item;
use App\Models\ItemArmorProfile;
use App\Models\ItemCost;
use Database\Seeders\ArmorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Esegue due volte il seeder per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(ArmorSeeder::class);
    $this->seed(ArmorSeeder::class);
});

//Verifica il numero di armature e scudi creati
it('crea tutte le armature del phb senza duplicati', function () {
    //Conta gli oggetti dotati di un profilo da armatura
    expect(
        Item::query()
            ->where('version_key', 'phb_2014')
            ->whereHas('armorProfile')
            ->count()
    )
        ->toBe(13)
        ->and(ItemArmorProfile::query()->count())
        ->toBe(13)
        ->and(ItemCost::query()->count())
        ->toBe(13);
});

//Verifica la suddivisione nelle categorie ufficiali
it('assegna le armature alle categorie corrette', function () {
    expect(
        ItemArmorProfile::query()
            ->where('armor_category', 'light')
            ->count()
    )
        ->toBe(3)
        ->and(
            ItemArmorProfile::query()
                ->where('armor_category', 'medium')
                ->count()
        )
        ->toBe(5)
        ->and(
            ItemArmorProfile::query()
                ->where('armor_category', 'heavy')
                ->count()
        )
        ->toBe(4)
        ->and(
            ItemArmorProfile::query()
                ->where('armor_category', 'shield')
                ->count()
        )
        ->toBe(1);
});

//Verifica il calcolo della CA delle diverse categorie
it('salva correttamente classe armatura e destrezza', function () {
    //Recupera alcuni esempi rappresentativi
    $leather = Item::query()
        ->where('key', 'leather_armor')
        ->with('armorProfile')
        ->firstOrFail();

    $halfPlate = Item::query()
        ->where('key', 'half_plate')
        ->with('armorProfile')
        ->firstOrFail();

    $plate = Item::query()
        ->where('key', 'plate_armor')
        ->with('armorProfile')
        ->firstOrFail();

    $shield = Item::query()
        ->where('key', 'shield')
        ->with('armorProfile')
        ->firstOrFail();

    //Le armature leggere applicano tutta la Destrezza
    expect($leather->armorProfile->armor_class_value)
        ->toBe(11)
        ->and($leather->armorProfile->dexterity_modifier)
        ->toBe('full')
        ->and($leather->armorProfile->max_dexterity_bonus)
        ->toBeNull();

    //Le armature medie limitano la Destrezza a +2
    expect($halfPlate->armorProfile->armor_class_value)
        ->toBe(15)
        ->and($halfPlate->armorProfile->dexterity_modifier)
        ->toBe('capped')
        ->and($halfPlate->armorProfile->max_dexterity_bonus)
        ->toBe(2);

    //Le armature pesanti non applicano la Destrezza
    expect($plate->armorProfile->armor_class_value)
        ->toBe(18)
        ->and($plate->armorProfile->dexterity_modifier)
        ->toBe('none');

    //Lo scudo aggiunge due punti alla CA già calcolata
    expect($shield->armorProfile->armor_class_operation)
        ->toBe('add')
        ->and($shield->armorProfile->armor_class_value)
        ->toBe(2);
});

//Verifica Forza minima e svantaggio a Furtività
it('salva requisiti di forza e svantaggio a furtività', function () {
    //Recupera la cotta di maglia
    $chainMail = Item::query()
        ->where('key', 'chain_mail')
        ->with('armorProfile.requirementAbility')
        ->firstOrFail();

    //Recupera l'armatura completa
    $plate = Item::query()
        ->where('key', 'plate_armor')
        ->with('armorProfile.requirementAbility')
        ->firstOrFail();

    //Recupera la corazza
    $breastplate = Item::query()
        ->where('key', 'breastplate')
        ->with('armorProfile')
        ->firstOrFail();

    //La cotta di maglia richiede Forza 13
    expect($chainMail->armorProfile->minimum_ability_score)
        ->toBe(13)
        ->and(
            $chainMail->armorProfile
                ->requirementAbility
                ->short_name
        )
        ->toBe('FOR')
        ->and($chainMail->armorProfile->stealth_disadvantage)
        ->toBeTrue();

    //L'armatura completa richiede Forza 15
    expect($plate->armorProfile->minimum_ability_score)
        ->toBe(15)
        ->and($plate->armorProfile->stealth_disadvantage)
        ->toBeTrue();

    //La corazza non impone svantaggio a Furtività
    expect($breastplate->armorProfile->stealth_disadvantage)
        ->toBeFalse();
});

//Verifica i tempi di indossamento e rimozione
it('salva i tempi in minuti e azioni', function () {
    //Recupera un'armatura per ciascuna categoria
    $light = Item::query()
        ->where('key', 'leather_armor')
        ->with('armorProfile')
        ->firstOrFail();

    $medium = Item::query()
        ->where('key', 'breastplate')
        ->with('armorProfile')
        ->firstOrFail();

    $heavy = Item::query()
        ->where('key', 'plate_armor')
        ->with('armorProfile')
        ->firstOrFail();

    $shield = Item::query()
        ->where('key', 'shield')
        ->with('armorProfile')
        ->firstOrFail();

    //Le armature leggere richiedono un minuto
    expect($light->armorProfile->don_time_minutes)
        ->toBe(1)
        ->and($light->armorProfile->doff_time_minutes)
        ->toBe(1);

    //Le armature medie richiedono cinque minuti e un minuto
    expect($medium->armorProfile->don_time_minutes)
        ->toBe(5)
        ->and($medium->armorProfile->doff_time_minutes)
        ->toBe(1);

    //Le armature pesanti richiedono dieci minuti e cinque minuti
    expect($heavy->armorProfile->don_time_minutes)
        ->toBe(10)
        ->and($heavy->armorProfile->doff_time_minutes)
        ->toBe(5);

    //Lo scudo utilizza azioni anziché minuti
    expect($shield->armorProfile->don_time_minutes)
        ->toBeNull()
        ->and($shield->armorProfile->don_time_actions)
        ->toBe(1)
        ->and($shield->armorProfile->doff_time_minutes)
        ->toBeNull()
        ->and($shield->armorProfile->doff_time_actions)
        ->toBe(1);
});

//Verifica prezzi e pesi di alcuni oggetti
it('salva prezzi e pesi delle armature', function () {
    //Recupera l'armatura completa e lo scudo
    $plate = Item::query()
        ->where('key', 'plate_armor')
        ->with('costs.currency')
        ->firstOrFail();

    $shield = Item::query()
        ->where('key', 'shield')
        ->with('costs.currency')
        ->firstOrFail();

    //Verifica prezzo e peso dell'armatura completa
    expect((int) $plate->costs->first()->amount)
        ->toBe(1500)
        ->and(
            (int) $plate->costs
                ->first()
                ->currency
                ->value_in_copper_pieces
        )
        ->toBe(100)
        ->and((float) $plate->weight_kg)
        ->toBe(29.484);

    //Verifica prezzo e peso dello scudo
    expect((int) $shield->costs->first()->amount)
        ->toBe(10)
        ->and((float) $shield->weight_kg)
        ->toBe(2.722);
});
