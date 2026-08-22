<?php

use App\Models\Currency;
use App\Models\Ruleset;
use App\Models\Spell;
use App\Models\SpellMaterialComponent;
use App\Models\SpellSchool;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SpellSchoolSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea completamente il database prima di ogni test
uses(RefreshDatabase::class);

//Crea i cataloghi e un incantesimo utilizzabili da tutti i test
beforeEach(function () {
    //Inserisce regolamento, valute e scuole di magia
    $this->seed(RulesetSeeder::class);
    $this->seed(CurrencySeeder::class);
    $this->seed(SpellSchoolSeeder::class);

    //Recupera le dipendenze necessarie all'incantesimo
    $ruleset = Ruleset::query()->firstOrFail();
    $school = SpellSchool::query()->firstOrFail();

    //Crea un incantesimo isolato per verificare la relazione
    $this->spell = Spell::query()->create([
        'ruleset_id' => $ruleset->id,
        'key' => 'legend_lore_test',
        'canonical_key' => 'legend_lore_test',
        'version_key' => 'test',
        'is_legacy' => false,
        'name' => 'Conoscenza delle Leggende di Test',
        'level' => 5,
        'spell_school_id' => $school->id,
        'casting_time_value' => 10,
        'casting_time_type' => 'minute',
        'range_type' => 'self',
        'duration_type' => 'instantaneous',
        'description' => 'Incantesimo creato per verificare i componenti.',
    ]);

    //Recupera una valuta per verificare i componenti costosi
    $this->currency = Currency::query()->firstOrFail();
});

//Verifica componenti costosi con regole di consumo differenti
it('gestisce componenti materiali dettagliati', function () {
    //Crea il componente consumato dal lancio
    $incense = $this->spell->materialComponents()->create([
        'key' => 'incense',
        'name' => 'Incenso',
        'description' => 'Incenso del valore di almeno 250 mo.',
        'cost_amount' => 250,
        'currency_id' => $this->currency->id,
        'cost_is_minimum' => true,
        'consumed' => true,
        'focus_replaceable' => false,
        'sort_order' => 1,
    ]);

    //Crea il componente costoso ma riutilizzabile
    $ivory = $this->spell->materialComponents()->create([
        'key' => 'ivory_strips',
        'name' => 'Strisce d’avorio',
        'description' => 'Quattro strisce d’avorio da 50 mo ciascuna.',
        'quantity' => 4,
        'unit' => 'striscia',
        'cost_amount' => 200,
        'currency_id' => $this->currency->id,
        'cost_is_minimum' => false,
        'consumed' => false,
        'focus_replaceable' => false,
        'sort_order' => 2,
    ]);

    //Verifica relazione, ordinamento e proprietà individuali
    expect($this->spell->materialComponents)
        ->toHaveCount(2)
        ->and($incense->spell->is($this->spell))
        ->toBeTrue()
        ->and($incense->currency->is($this->currency))
        ->toBeTrue()
        ->and($incense->consumed)
        ->toBeTrue()
        ->and($ivory->consumed)
        ->toBeFalse()
        ->and($ivory->quantity)
        ->toBe('4.000')
        ->and($ivory->cost_amount)
        ->toBe('200.00');
});

//Verifica la coerenza delle quantità e dei costi
it('rifiuta quantità e costi non validi', function () {
    //Rifiuta una quantità non positiva
    expect(fn () => $this->spell->materialComponents()->create([
        'key' => 'invalid_quantity',
        'name' => 'Quantità non valida',
        'quantity' => 0,
    ]))->toThrow(InvalidArgumentException::class);

    //Rifiuta un costo privo della relativa valuta
    expect(fn () => $this->spell->materialComponents()->create([
        'key' => 'missing_currency',
        'name' => 'Valuta mancante',
        'cost_amount' => 10,
        'focus_replaceable' => false,
    ]))->toThrow(InvalidArgumentException::class);

    //Rifiuta una valuta priva del relativo costo
    expect(fn () => $this->spell->materialComponents()->create([
        'key' => 'missing_cost',
        'name' => 'Costo mancante',
        'currency_id' => $this->currency->id,
    ]))->toThrow(InvalidArgumentException::class);
});

//Verifica la regola della sostituzione tramite focus
it('rifiuta focus per componenti costosi o consumati', function () {
    //Rifiuta un componente costoso marcato come sostituibile
    expect(fn () => $this->spell->materialComponents()->create([
        'key' => 'costly_focus',
        'name' => 'Componente costoso',
        'cost_amount' => 50,
        'currency_id' => $this->currency->id,
        'focus_replaceable' => true,
    ]))->toThrow(InvalidArgumentException::class);

    //Rifiuta un componente consumato marcato come sostituibile
    expect(fn () => $this->spell->materialComponents()->create([
        'key' => 'consumed_focus',
        'name' => 'Componente consumato',
        'consumed' => true,
        'focus_replaceable' => true,
    ]))->toThrow(InvalidArgumentException::class);
});

//Verifica l'unicità delle chiavi nello stesso incantesimo
it('rifiuta componenti duplicati', function () {
    //Crea il primo componente con la chiave da proteggere
    $this->spell->materialComponents()->create([
        'key' => 'duplicate_component',
        'name' => 'Primo componente',
    ]);

    //Il database rifiuta una seconda riga con la stessa chiave
    expect(fn () => $this->spell->materialComponents()->create([
        'key' => 'duplicate_component',
        'name' => 'Secondo componente',
    ]))->toThrow(QueryException::class);
});

//Verifica l'eliminazione automatica insieme all'incantesimo
it('elimina i componenti insieme all incantesimo', function () {
    //Crea un componente collegato all'incantesimo
    $this->spell->materialComponents()->create([
        'key' => 'temporary_component',
        'name' => 'Componente temporaneo',
    ]);

    //Elimina l'incantesimo proprietario
    $this->spell->delete();

    //La chiave esterna elimina anche il componente
    expect(SpellMaterialComponent::query()->count())
        ->toBe(0);
});
