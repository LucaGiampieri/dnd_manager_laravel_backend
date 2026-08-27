<?php

use App\Models\EffectDefinitionDamage;
use App\Models\SourceReference;
use App\Models\Spell;
use App\Models\SpellSummonTemplateForm;
use Database\Seeders\TashasCauldronSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Inserisce due volte il catalogo per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(TashasCauldronSpellSeeder::class);
    $this->seed(TashasCauldronSpellSeeder::class);
});

it('salva i cinque incantesimi di terzo livello di Tasha', function () {
    $levelThreeSpells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 3);

    expect((clone $levelThreeSpells)->count())
        ->toBe(5)
        ->and(
            (clone $levelThreeSpells)
                ->distinct('canonical_key')
                ->count()
        )
        ->toBe(5)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->where('level', '<=', 3)
                ->count()
        )->toBe(13)
        ->and(
            (clone $levelThreeSpells)
                ->whereHas('summons')
                ->count()
        )->toBe(3)
        ->and(
            SourceReference::query()
                ->where('sourceable_type', Spell::class)
                ->whereIn(
                    'sourceable_id',
                    (clone $levelThreeSpells)->pluck('id')
                )
                ->count()
        )->toBe(5);
});

it('salva le forme di ombra folletto e non morto', function () {
    $shadow = Spell::query()
        ->where('key', 'summon_shadowspawn')
        ->firstOrFail();
    $fey = Spell::query()
        ->where('key', 'summon_fey')
        ->firstOrFail();
    $undead = Spell::query()
        ->where('key', 'summon_undead')
        ->firstOrFail();

    $shadowTemplate = $shadow
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();
    $feyTemplate = $fey
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();
    $undeadTemplate = $undead
        ->summons()
        ->firstOrFail()
        ->templates()
        ->firstOrFail();

    expect($shadowTemplate->forms()->count())
        ->toBe(3)
        ->and($feyTemplate->forms()->count())
        ->toBe(3)
        ->and($undeadTemplate->forms()->count())
        ->toBe(3)
        ->and(
            SpellSummonTemplateForm::query()
                ->whereIn('spell_summon_template_id', [
                    $shadowTemplate->id,
                    $feyTemplate->id,
                    $undeadTemplate->id,
                ])
                ->count()
        )->toBe(9);

    $shadowDamage = $shadowTemplate
        ->forms()
        ->where('name', 'Furia')
        ->firstOrFail()
        ->creatureStatBlock
        ->actions()
        ->where('key', 'chilling_rend')
        ->firstOrFail()
        ->damages()
        ->firstOrFail();

    $feyDamages = $feyTemplate
        ->forms()
        ->where('name', 'Rabbioso')
        ->firstOrFail()
        ->creatureStatBlock
        ->actions()
        ->where('key', 'shortsword')
        ->firstOrFail()
        ->damages();

    $skeletonForm = $undeadTemplate
        ->forms()
        ->where('name', 'Scheletrico')
        ->firstOrFail();
    $skeletonDamage = $skeletonForm
        ->creatureStatBlock
        ->actions()
        ->where('key', 'grave_bolt')
        ->firstOrFail()
        ->damages()
        ->firstOrFail();

    expect($shadowDamage->formula)
        ->toBe('1d12 + 3')
        ->and($shadowDamage->damageType->name)
        ->toBe('Freddo')
        ->and($feyDamages->count())
        ->toBe(2)
        ->and(
            $feyDamages
                ->get()
                ->map(fn ($damage) => $damage->damageType->name)
                ->all()
        )
        ->toBe(['Perforante', 'Forza'])
        ->and($skeletonDamage->formula)
        ->toBe('2d4 + 3')
        ->and($skeletonDamage->damageType->name)
        ->toBe('Necrotico')
        ->and($skeletonForm->scalings()->count())
        ->toBe(5);
});

it('salva Fortezza della Mente e Sudario Spirituale', function () {
    $fortress = Spell::query()
        ->where('key', 'intellect_fortress')
        ->firstOrFail();
    $fortressEffect = $fortress
        ->effectDefinitions()
        ->firstOrFail();

    expect($fortress->spellSchool->key)
        ->toBe('abjuration')
        ->and($fortress->concentration)
        ->toBeTrue()
        ->and($fortress->targetProfile->can_target_self)
        ->toBeTrue()
        ->and($fortressEffect->rollModifiers()->count())
        ->toBe(3)
        ->and(
            $fortressEffect
                ->rollModifiers()
                ->pluck('modifier_type')
                ->unique()
                ->values()
                ->all()
        )->toBe(['advantage'])
        ->and($fortressEffect->scalings()->count())
        ->toBe(1);

    $shroud = Spell::query()
        ->where('key', 'spirit_shroud')
        ->firstOrFail();
    $attackEffect = $shroud
        ->effectDefinitions()
        ->where('key', 'shrouded_attacks')
        ->firstOrFail();
    $damageIds = $attackEffect->damages()->pluck('id');

    expect($shroud->casting_time_type)
        ->toBe('bonus_action')
        ->and($shroud->targetProfile->target_type)
        ->toBe('self')
        ->and($attackEffect->damages()->count())
        ->toBe(3)
        ->and(
            $attackEffect
                ->damages()
                ->get()
                ->map(fn ($damage) => $damage->damageType->name)
                ->all()
        )->toBe(['Freddo', 'Necrotico', 'Radioso'])
        ->and(
            EffectDefinitionDamage::query()
                ->whereIn('id', $damageIds)
                ->whereHas('scalings')
                ->count()
        )->toBe(3)
        ->and($shroud->effectDefinitions()->count())
        ->toBe(3);
});
