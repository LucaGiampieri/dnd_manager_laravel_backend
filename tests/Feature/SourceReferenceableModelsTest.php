<?php

use App\Models\Ability;
use App\Models\Alignment;
use App\Models\ChallengeRating;
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
use App\Models\Race;
use App\Models\Subrace;
use App\Models\RacePhysicalTrait;
use App\Models\SubracePhysicalTrait;
use App\Models\OptionalRule;
use App\Models\Feature;
use App\Models\ArmorProficiency;
use App\Models\ToolProficiency;
use App\Models\WeaponProficiency;
use App\Models\WeaponProperty;

//Verifica che tutti i contenuti ufficiali usino i trait condivisi
it('i contenuti del regolamento possono avere riferimenti alle fonti', function () {
    //Elenca tutti i modelli che rappresentano contenuti ufficiali
    $models = [
        Ability::class,
        Alignment::class,
        ChallengeRating::class,
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
        Race::class,
        Subrace::class,
        RacePhysicalTrait::class,
        SubracePhysicalTrait::class,
        OptionalRule::class,
        Feature::class,
        ArmorProficiency::class,
        ToolProficiency::class,
        WeaponProficiency::class,
        WeaponProperty::class,
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
