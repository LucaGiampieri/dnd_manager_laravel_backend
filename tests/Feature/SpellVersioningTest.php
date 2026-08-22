<?php

use App\Models\Ruleset;
use App\Models\Spell;
use App\Models\SpellSchool;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SpellSchoolSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Crea i cataloghi necessari agli incantesimi
beforeEach(function () {
    //Inserisce regolamento e scuole di magia
    $this->seed([
        RulesetSeeder::class,
        SpellSchoolSeeder::class,
    ]);

    //Recupera il regolamento utilizzato dal test
    $this->ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera una scuola di magia disponibile
    $this->spellSchool = SpellSchool::query()
        ->firstOrFail();

    //Prepara una funzione per creare incantesimi completi
    $this->createSpell = function (
        string $key,
        array $overrides = []
    ): Spell {
        return Spell::query()->create(
            array_merge(
                [
                    'ruleset_id' => $this->ruleset->id,
                    'key' => $key,
                    'name' => 'Incantesimo di prova',
                    'level' => 1,
                    'spell_school_id' =>
                        $this->spellSchool->id,
                    'casting_time_value' => 1,
                    'casting_time_type' => 'action',
                    'casting_trigger' => null,
                    'range_type' => 'distance',
                    'range' => 18,
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
                    'description' =>
                        'Incantesimo utilizzato soltanto nel test.',
                    'higher_levels' => null,
                    'notes' => null,
                ],
                $overrides
            )
        );
    };
});

//Verifica i valori automatici degli incantesimi personalizzati
it('assegna il versionamento agli incantesimi personalizzati', function () {
    //Crea un incantesimo senza specificare la versione
    $spell = ($this->createSpell)(
        'custom_test_spell'
    );

    //Verifica i valori generati dal modello
    expect($spell->canonical_key)
        ->toBe('custom_test_spell')
        ->and($spell->version_key)
        ->toBe('custom')
        ->and($spell->is_legacy)
        ->toBeFalse();
});

//Permette versioni differenti della stessa regola
it('permette versioni differenti dello stesso incantesimo', function () {
    //Crea la versione pubblicata nel PHB 2014
    $spell2014 = ($this->createSpell)(
        'test_spell_phb_2014',
        [
            'canonical_key' => 'test_spell',
            'version_key' => 'phb_2014',
            'is_legacy' => false,
        ]
    );

    //Crea una revisione successiva dello stesso incantesimo
    $spell2024 = ($this->createSpell)(
        'test_spell_phb_2024',
        [
            'canonical_key' => 'test_spell',
            'version_key' => 'phb_2024',
            'is_legacy' => false,
        ]
    );

    //Verifica che le due versioni abbiano identità distinte
    expect($spell2014->id)
        ->not->toBe($spell2024->id)
        ->and($spell2014->canonical_key)
        ->toBe($spell2024->canonical_key)
        ->and($spell2014->version_key)
        ->toBe('phb_2014')
        ->and($spell2024->version_key)
        ->toBe('phb_2024');
});

//Rifiuta due copie della stessa versione
it('rifiuta versioni duplicate dello stesso incantesimo', function () {
    //Crea la prima copia della versione
    ($this->createSpell)(
        'duplicate_spell_first',
        [
            'canonical_key' => 'duplicate_spell',
            'version_key' => 'phb_2014',
        ]
    );

    //Tenta di creare una seconda copia equivalente
    expect(
        fn () => ($this->createSpell)(
            'duplicate_spell_second',
            [
                'canonical_key' => 'duplicate_spell',
                'version_key' => 'phb_2014',
            ]
        )
    )->toThrow(QueryException::class);
});

//Converte correttamente il valore legacy
it('converte lo stato legacy in booleano', function () {
    //Crea una versione esplicitamente obsoleta
    $spell = ($this->createSpell)(
        'legacy_test_spell',
        [
            'canonical_key' => 'legacy_test_spell',
            'version_key' => 'old_test_version',
            'is_legacy' => true,
        ]
    );

    //Verifica il cast booleano
    expect($spell->is_legacy)->toBeTrue();
});
