<?php

use App\Models\Ability;
use App\Models\Alignment;
use App\Models\Condition;
use App\Models\ConditionLevel;
use App\Models\Concerns\HasContentRelations;
use App\Models\Concerns\HasSourceReferences;
use App\Models\CreatureStatBlock;
use App\Models\CreatureTag;
use App\Models\CreatureType;
use App\Models\Currency;
use App\Models\DamageType;
use App\Models\EffectDefinition;
use App\Models\EffectDefinitionMovementCostModifier;
use App\Models\Item;
use App\Models\Language;
use App\Models\LanguageScript;
use App\Models\MovementType;
use App\Models\Sense;
use App\Models\Size;
use App\Models\Skill;
use App\Models\Spell;
use App\Models\SpellSchool;
use App\Models\Subclass;
use App\Models\CharacterClass;

//Verifica che tutti i contenuti ufficiali usino i trait condivisi
it('i contenuti del regolamento possono avere riferimenti alle fonti', function () {
    //Elenca tutti i modelli che rappresentano contenuti ufficiali
    $models = [
        Ability::class,
        Alignment::class,
        Condition::class,
        ConditionLevel::class,
        CreatureStatBlock::class,
        CreatureTag::class,
        CreatureType::class,
        Currency::class,
        DamageType::class,
        EffectDefinition::class,
        EffectDefinitionMovementCostModifier::class,
        Item::class,
        Language::class,
        LanguageScript::class,
        MovementType::class,
        Sense::class,
        Size::class,
        Skill::class,
        Spell::class,
        SpellSchool::class,
        CharacterClass::class,
        Subclass::class,
    ];

    //Controlla separatamente ogni modello ufficiale
    foreach ($models as $model) {
        //Recupera anche i trait utilizzati attraverso altri trait
        $usedTraits = class_uses_recursive($model);

        //Verifica la presenza dei riferimenti ai manuali
        expect(
            in_array(
                HasSourceReferences::class,
                $usedTraits,
                true
            )
        )->toBeTrue(
            "{$model} deve usare il trait HasSourceReferences."
        );

        //Verifica la presenza delle relazioni tra contenuti
        expect(
            in_array(
                HasContentRelations::class,
                $usedTraits,
                true
            )
        )->toBeTrue(
            "{$model} deve usare il trait HasContentRelations."
        );
    }
});
