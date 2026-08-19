<?php

use App\Models\CreatureStatBlock;
use App\Models\CreatureStatBlockArmorClass;

//Verifica CA principale e alternative dello stat block
it('gestisce la Classe Armatura principale e quelle alternative', function () {
    //Crea lo stat block utilizzato dal test
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Guerriero elfo di prova',
    ]);

    //Crea la prima CA senza indicarla esplicitamente come principale
    $defaultArmorClass = $statBlock->armorClasses()->create([
        'armor_class' => 18,
        'armor_class_type' => 'armor',
        'description' => 'Cotta di maglia e scudo.',
        'sort_order' => 1,
    ]);

    //Crea una CA alternativa utilizzabile senza lo scudo
    $alternativeArmorClass = $statBlock->armorClasses()->create([
        'armor_class' => 16,
        'armor_class_type' => 'armor',
        'description' => 'Cotta di maglia.',
        'condition' => 'Si applica quando non utilizza lo scudo.',
        'sort_order' => 2,
    ]);

    //Ricarica le Classi Armatura con il loro ordinamento
    $statBlock->load('armorClasses');

    //Verifica che la prima CA sia diventata quella principale
    expect($defaultArmorClass->is_default)->toBeTrue()
        ->and($alternativeArmorClass->is_default)->toBeFalse()
        ->and($statBlock->armorClasses)->toHaveCount(2)
        ->and($statBlock->armorClasses->first()->id)
        ->toBe($defaultArmorClass->id);

    //Verifica l'attributo calcolato dello stat block
    expect($statBlock->fresh()->armor_class)->toBe(18);

    //Verifica la relazione inversa verso lo stat block
    expect(
        $alternativeArmorClass
            ->creatureStatBlock
            ->is($statBlock)
    )->toBeTrue();
});

//Verifica la sostituzione automatica della CA principale
it('mantiene una sola Classe Armatura principale', function () {
    //Crea lo stat block utilizzato dal test
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Mutaforma di prova',
    ]);

    //Crea la CA inizialmente principale
    $humanoidArmorClass = $statBlock->armorClasses()->create([
        'armor_class' => 12,
        'armor_class_type' => 'armor',
        'description' => 'Armatura della forma umanoide.',
        'condition' => 'Forma umanoide.',
        'sort_order' => 1,
    ]);

    //Crea una nuova CA e la imposta come principale
    $hybridArmorClass = $statBlock->armorClasses()->create([
        'armor_class' => 14,
        'armor_class_type' => 'natural_armor',
        'is_default' => true,
        'description' => 'Armatura naturale della forma ibrida.',
        'condition' => 'Forma ibrida.',
        'sort_order' => 2,
    ]);

    //Verifica che soltanto la nuova CA sia principale
    expect($humanoidArmorClass->fresh()->is_default)
        ->toBeFalse()
        ->and($hybridArmorClass->fresh()->is_default)
        ->toBeTrue()
        ->and(
            $statBlock
                ->armorClasses()
                ->where('is_default', true)
                ->count()
        )->toBe(1)
        ->and($statBlock->fresh()->armor_class)
        ->toBe(14);

    //Elimina la CA attualmente principale
    $hybridArmorClass->delete();

    //Verifica che la CA rimanente venga promossa automaticamente
    expect($humanoidArmorClass->fresh()->is_default)
        ->toBeTrue()
        ->and($statBlock->fresh()->armor_class)
        ->toBe(12);
});

//Verifica tipologie e valori accettati
it('accetta tutte le tipologie valide e rifiuta dati errati', function () {
    //Crea lo stat block utilizzato dal test
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura con CA multiple',
    ]);

    //Crea una CA per ogni tipologia supportata
    foreach (
        CreatureStatBlockArmorClass::TYPES as $index => $type
    ) {
        $statBlock->armorClasses()->create([
            'armor_class' => 10 + $index,
            'armor_class_type' => $type,
            'sort_order' => $index + 1,
        ]);
    }

    //Verifica che tutte le tipologie siano state salvate
    expect(
        $statBlock
            ->armorClasses()
            ->pluck('armor_class_type')
            ->all()
    )->toBe(CreatureStatBlockArmorClass::TYPES);

    //Verifica che una CA uguale a zero venga rifiutata
    expect(
        fn () => $statBlock->armorClasses()->create([
            'armor_class' => 0,
            'armor_class_type' => 'fixed',
        ])
    )->toThrow(\InvalidArgumentException::class);

    //Verifica che una tipologia sconosciuta venga rifiutata
    expect(
        fn () => $statBlock->armorClasses()->create([
            'armor_class' => 15,
            'armor_class_type' => 'unknown_type',
        ])
    )->toThrow(\InvalidArgumentException::class);
});

//Verifica la cancellazione automatica delle CA
it('elimina le Classi Armatura insieme allo stat block', function () {
    //Crea uno stat block con due Classi Armatura
    $statBlock = CreatureStatBlock::query()->create([
        'name' => 'Creatura da eliminare',
    ]);

    $statBlock->armorClasses()->create([
        'armor_class' => 15,
        'armor_class_type' => 'natural_armor',
    ]);

    $statBlock->armorClasses()->create([
        'armor_class' => 12,
        'armor_class_type' => 'fixed',
        'is_default' => false,
    ]);

    //Memorizza l'identificativo dello stat block
    $statBlockId = $statBlock->id;

    //Verifica che entrambe le CA siano presenti
    expect(
        CreatureStatBlockArmorClass::query()
            ->where('creature_stat_block_id', $statBlockId)
            ->count()
    )->toBe(2);

    //Elimina lo stat block proprietario
    $statBlock->delete();

    //Verifica la cancellazione automatica delle CA
    expect(
        CreatureStatBlockArmorClass::query()
            ->where('creature_stat_block_id', $statBlockId)
            ->count()
    )->toBe(0);
});
