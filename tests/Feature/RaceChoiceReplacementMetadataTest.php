<?php

use App\Models\Feature;
use App\Models\Race;
use App\Models\Ruleset;
use App\Models\Subrace;
use Database\Seeders\RaceFeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Prepara razze e capacità utilizzate dai test
beforeEach(function () {
    $this->seed(RaceFeatureSeeder::class);

    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Crea una capacità sostitutiva utilizzata nei test
    $this->replacementFeature = $this->ruleset
        ->features()
        ->updateOrCreate(
            [
                'key' => 'test_replacement_feature',
            ],
            [
                'name' => 'Capacità sostitutiva di prova',
                'type' => 'race',
                'level' => 1,
                'description' =>
                    'Capacità creata soltanto per il test.',
                'max_uses' => null,
                'recharge' => null,
                'notes' => null,
            ]
        );
});

//Verifica i metadati di sostituzione delle scelte razziali
it('registra la capacità sostituita da una scelta razziale', function () {
    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    $skillVersatility = Feature::query()
        ->where(
            'key',
            'half_elf_skill_versatility_phb_2014'
        )
        ->firstOrFail();

    $choice = $halfElf->choices()->create([
        'key' => 'test_half_elf_replacement',
        'name' => 'Sostituzione di prova',
        'choice_type' => 'feature',
        'replaces_feature_id' => $skillVersatility->id,
        'choose' => 1,
        'level' => 1,
        'required' => false,
        'requires_dm_permission' => true,
        'sort_order' => 10,
        'description' =>
            'Scelta sostitutiva creata per il test.',
        'notes' => null,
    ]);

    $option = $choice->options()->create([
        'key' => 'wood_elf_test_option',
        'option_type' => 'feature',
        'option_id' => $this->replacementFeature->id,
        'option_text' => null,
        'ancestry_key' => 'wood_elf',
        'eligibility_condition' =>
            'Il personaggio deve avere ascendenza elfica '
            . 'dei boschi.',
        'value' => null,
        'quantity' => 1,
        'sort_order' => 10,
        'notes' => null,
    ]);

    expect($choice->replacedFeature->is($skillVersatility))
        ->toBeTrue()
        ->and($choice->requires_dm_permission)->toBeTrue()
        ->and($choice->required)->toBeFalse()
        ->and($option->ancestry_key)->toBe('wood_elf')
        ->and($option->eligibility_condition)
        ->toContain('elfica dei boschi');
});

//Verifica che i metadati funzionino anche sulle sottorazze
it('registra sostituzioni anche nelle scelte di sottorazza', function () {
    $highElf = Subrace::query()
        ->where('key', 'high_elf')
        ->firstOrFail();

    $skillVersatility = Feature::query()
        ->where(
            'key',
            'half_elf_skill_versatility_phb_2014'
        )
        ->firstOrFail();

    $choice = $highElf->choices()->create([
        'key' => 'test_subrace_replacement',
        'name' => 'Sostituzione di sottorazza',
        'choice_type' => 'feature',
        'replaces_feature_id' => $skillVersatility->id,
        'choose' => 1,
        'level' => 1,
        'required' => false,
        'requires_dm_permission' => true,
        'sort_order' => 10,
        'description' =>
            'Scelta di sottorazza creata per il test.',
        'notes' => null,
    ]);

    $option = $choice->options()->create([
        'key' => 'high_elf_test_option',
        'option_type' => 'feature',
        'option_id' => $this->replacementFeature->id,
        'option_text' => null,
        'ancestry_key' => 'high_elf',
        'eligibility_condition' =>
            'Il personaggio deve appartenere alla '
            . 'sottorazza richiesta.',
        'value' => null,
        'quantity' => 1,
        'sort_order' => 10,
        'notes' => null,
    ]);

    expect($choice->replacedFeature->is($skillVersatility))
        ->toBeTrue()
        ->and($choice->requires_dm_permission)->toBeTrue()
        ->and($option->ancestry_key)->toBe('high_elf');
});

//Verifica che la scelta sopravviva alla cancellazione della capacità
it('mantiene la scelta quando la capacità sostituita viene eliminata', function () {
    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    $skillVersatility = Feature::query()
        ->where(
            'key',
            'half_elf_skill_versatility_phb_2014'
        )
        ->firstOrFail();

    $choice = $halfElf->choices()->create([
        'key' => 'test_nullable_replacement',
        'name' => 'Sostituzione eliminabile',
        'choice_type' => 'feature',
        'replaces_feature_id' => $skillVersatility->id,
        'choose' => 1,
        'level' => 1,
        'required' => false,
        'requires_dm_permission' => true,
        'sort_order' => 10,
        'description' => null,
        'notes' => null,
    ]);

    //Elimina la capacità originariamente sostituita
    $skillVersatility->delete();

    //La scelta rimane ma il collegamento diventa nullo
    expect($choice->fresh())->not->toBeNull()
        ->and($choice->fresh()->replaces_feature_id)
        ->toBeNull();
});
