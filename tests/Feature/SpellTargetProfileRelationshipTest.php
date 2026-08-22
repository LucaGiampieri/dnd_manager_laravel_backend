<?php

use App\Models\Ruleset;
use App\Models\Spell;
use App\Models\SpellSchool;
use App\Models\SpellTargetProfile;
use Database\Seeders\RulesetSeeder;
use Database\Seeders\SpellSchoolSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Crea i cataloghi e l'incantesimo utilizzato dai test
beforeEach(function () {
    //Inserisce regolamento e scuole di magia
    $this->seed([
        RulesetSeeder::class,
        SpellSchoolSeeder::class,
    ]);

    //Recupera il regolamento ufficiale
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Recupera una scuola disponibile
    $school = SpellSchool::query()
        ->firstOrFail();

    //Crea un incantesimo completo per i test
    $this->spell = Spell::query()->create([
        'ruleset_id' => $ruleset->id,
        'key' => 'test_area_spell',
        'canonical_key' => 'test_area_spell',
        'version_key' => 'phb_2014',
        'is_legacy' => false,
        'name' => 'Incantesimo ad Area di Test',
        'level' => 3,
        'spell_school_id' => $school->id,
        'casting_time_value' => 1,
        'casting_time_type' => 'action',
        'range_type' => 'distance',
        'range' => 45.72,
        'verbal_component' => true,
        'somatic_component' => true,
        'material_component' => false,
        'duration_type' => 'instantaneous',
        'concentration' => false,
        'ritual' => false,
        'description' => 'Incantesimo creato esclusivamente per il test.',
    ]);
});

//Verifica un profilo completo per un incantesimo ad area
it('gestisce il bersaglio e l area di un incantesimo', function () {
    //Crea un'area sferica con raggio di 6,096 metri
    $profile = $this->spell
        ->targetProfile()
        ->create([
            'target_type' => 'area',
            'area_shape' => 'sphere',
            'area_size_meters' => 6.096,
            'can_target_self' => false,
            'can_target_objects' => true,
            'requires_sight' => false,
            'notes' => 'Il valore rappresenta il raggio della sfera.',
        ]);

    //Ricarica i dati e verifica relazione e conversioni
    $profile->refresh();
    $this->spell->refresh();

    expect($this->spell->targetProfile->is($profile))
        ->toBeTrue()
        ->and($profile->spell->is($this->spell))
        ->toBeTrue()
        ->and($profile->target_type)
        ->toBe('area')
        ->and($profile->area_shape)
        ->toBe('sphere')
        ->and($profile->area_size_meters)
        ->toBe(6.096)
        ->and($profile->can_target_objects)
        ->toBeTrue();
});

//Verifica un incantesimo rivolto a una singola creatura
it('gestisce un bersaglio singolo senza area', function () {
    //Crea un profilo per una singola creatura
    $profile = $this->spell
        ->targetProfile()
        ->create([
            'target_type' => 'creature',
            'target_count' => 1,
            'can_target_self' => true,
            'requires_sight' => false,
        ]);

    //Ricarica i valori predefiniti generati dal database
    $profile->refresh();

    expect($profile->target_count)
        ->toBe(1)
        ->and($profile->area_shape)
        ->toBeNull()
        ->and($profile->area_size_meters)
        ->toBeNull()
        ->and($profile->can_target_self)
        ->toBeTrue()
        ->and($profile->can_target_objects)
        ->toBeFalse();
});

//Verifica che un'area debba avere forma e dimensione
it('rifiuta aree incomplete', function () {
    //Tenta di creare un'area priva della dimensione
    expect(
        fn () => $this->spell
            ->targetProfile()
            ->create([
                'target_type' => 'area',
                'area_shape' => 'cone',
            ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica che un bersaglio normale non usi campi dell'area
it('rifiuta dimensioni applicate a bersagli normali', function () {
    //Tenta di assegnare una sfera a una singola creatura
    expect(
        fn () => $this->spell
            ->targetProfile()
            ->create([
                'target_type' => 'creature',
                'target_count' => 1,
                'area_shape' => 'sphere',
                'area_size_meters' => 3.048,
            ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica che il numero dei bersagli sia valido
it('rifiuta un numero di bersagli non positivo', function () {
    //Tenta di creare un profilo con zero bersagli
    expect(
        fn () => $this->spell
            ->targetProfile()
            ->create([
                'target_type' => 'creatures',
                'target_count' => 0,
            ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica che ogni incantesimo abbia un solo profilo
it('rifiuta profili duplicati dello stesso incantesimo', function () {
    //Crea il primo profilo valido
    $this->spell
        ->targetProfile()
        ->create([
            'target_type' => 'creature',
            'target_count' => 1,
        ]);

    //Tenta di crearne un secondo per lo stesso incantesimo
    expect(
        fn () => SpellTargetProfile::query()->create([
            'spell_id' => $this->spell->id,
            'target_type' => 'object',
            'target_count' => 1,
        ])
    )->toThrow(QueryException::class);
});

//Verifica la pulizia automatica del profilo
it('elimina il profilo insieme all incantesimo', function () {
    //Crea il profilo collegato all'incantesimo
    $profile = $this->spell
        ->targetProfile()
        ->create([
            'target_type' => 'point',
        ]);

    $profileId = $profile->id;

    //Elimina l'incantesimo proprietario
    $this->spell->delete();

    //Verifica l'eliminazione tramite vincolo esterno
    expect(
        SpellTargetProfile::query()
            ->whereKey($profileId)
            ->exists()
    )->toBeFalse();
});

//Verifica il supporto delle aree quadrate utilizzate da alcuni incantesimi
it('gestisce un area quadrata', function () {
    //Crea un quadrato con lato di 6,096 metri
    $profile = $this->spell
        ->targetProfile()
        ->create([
            'target_type' => 'area',
            'area_shape' => 'square',
            'area_size_meters' => 6.096,
            'requires_sight' => false,
            'notes' => 'La dimensione rappresenta il lato del quadrato.',
        ]);

    //Verifica forma e dimensione convertita in float
    expect($profile->area_shape)
        ->toBe('square')
        ->and($profile->area_size_meters)
        ->toBe(6.096);
});

//Verifica che le forme sconosciute vengano rifiutate
it('rifiuta forme di area sconosciute', function () {
    expect(
        fn () => $this->spell
            ->targetProfile()
            ->create([
                'target_type' => 'area',
                'area_shape' => 'piramide_impossibile',
                'area_size_meters' => 6.096,
            ])
    )->toThrow(\InvalidArgumentException::class);
});
