<?php

use App\Models\Concerns\HasContentRelations;
use App\Models\Ability;
use App\Models\Condition;
use App\Models\ConditionLevel;
use App\Models\Concerns\HasSourceReferences;
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
use App\Models\Alignment;
use App\Models\CreatureStatBlock;

it('i contenuti del regolamento possono avere riferimenti alle fonti', function () {
    $models = [
        Ability::class,
        Condition::class,
        ConditionLevel::class,
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
        Subclass::class,
        Alignment::class,
        CreatureStatBlock::class,
    ];

    foreach ($models as $model) {
        expect(
            in_array(
                HasSourceReferences::class,
                class_uses_recursive($model),
                true
            )
        )->toBeTrue(
            "{$model} deve usare il trait HasSourceReferences."
        );

        expect(
            in_array(
                HasContentRelations::class,
                class_uses_recursive($model),
                true
            )
        )->toBeTrue(
            "{$model} deve usare il trait HasContentRelations."
        );
    }
});
