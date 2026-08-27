<?php

use App\Models\CreatureStatBlock;
use App\Models\SourceReference;
use App\Models\Spell;
use Database\Seeders\TashasCauldronSpellSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

//Inserisce due volte il catalogo per verificarne l'idempotenza
beforeEach(function () {
    $this->seed(TashasCauldronSpellSeeder::class);
    $this->seed(TashasCauldronSpellSeeder::class);
});

it('salva i due incantesimi di secondo livello di Tasha', function () {
    $levelTwoSpells = Spell::query()
        ->where('version_key', 'tcoe_2020')
        ->where('level', 2);

    $mindWhip = Spell::query()
        ->where('key', 'tashas_mind_whip')
        ->firstOrFail();

    expect($levelTwoSpells->count())
        ->toBe(2)
        ->and($levelTwoSpells->distinct('canonical_key')->count())
        ->toBe(2)
        ->and(
            Spell::query()
                ->where('version_key', 'tcoe_2020')
                ->where('level', '<=', 2)
                ->count()
        )->toBe(8)
        ->and($mindWhip->name)
        ->toBe('Scudiscio Mentale di Tasha')
        ->and($mindWhip->spellSchool->key)
        ->toBe('enchantment')
        ->and($mindWhip->savingThrowAbility->short_name)
        ->toBe('INT')
        ->and($mindWhip->save_success_damage)
        ->toBe('half')
        ->and($mindWhip->targetProfile->target_count)
        ->toBe(1);
});

it('salva danno durata e crescita di Scudiscio Mentale', function () {
    $mindWhip = Spell::query()
        ->where('key', 'tashas_mind_whip')
        ->firstOrFail();

    $damageEffect = $mindWhip
        ->effectDefinitions()
        ->where('key', 'psychic_assault')
        ->firstOrFail();

    $damage = $damageEffect
        ->damages()
        ->where('key', 'psychic_damage')
        ->firstOrFail();

    $targetScaling = $damageEffect
        ->scalings()
        ->where('key', 'extra_target_per_slot')
        ->firstOrFail();

    $limitedTurn = $mindWhip
        ->effectDefinitions()
        ->where('key', 'limited_turn')
        ->firstOrFail();

    expect($damage->formula)
        ->toBe('3d6')
        ->and($damage->damageType->name)
        ->toBe('Psichico')
        ->and($targetScaling->target_field)
        ->toBe('target_count')
        ->and($targetScaling->source_type)
        ->toBe('spell_slot_level')
        ->and($targetScaling->source_offset)
        ->toBe(-2.0)
        ->and($limitedTurn->durations()->count())
        ->toBe(1)
        ->and(
            $limitedTurn->durations()->firstOrFail()->turn_reference
        )->toBe('target');
});

it('salva le tre forme strutturate di Evocare Bestia', function () {
    $summonBeast = Spell::query()
        ->where('key', 'summon_beast')
        ->firstOrFail();

    $summon = $summonBeast->summons()->firstOrFail();
    $template = $summon->templates()->firstOrFail();
    $airForm = $template
        ->forms()
        ->where('name', 'Aria')
        ->firstOrFail();
    $air = $airForm->creatureStatBlock;

    $rend = $air->actions()
        ->where('key', 'rend')
        ->firstOrFail();
    $damage = $rend->damages()->firstOrFail();

    expect($summonBeast->concentration)
        ->toBeTrue()
        ->and($summonBeast->targetProfile->target_type)
        ->toBe('point')
        ->and($summon->quantity)
        ->toBe(1)
        ->and($template->creatureType->key)
        ->toBe('beast')
        ->and($template->size->name)
        ->toBe('Piccola')
        ->and($template->forms()->count())
        ->toBe(3)
        ->and($air->armor_class)
        ->toBe(11)
        ->and($air->average_hit_points)
        ->toBe(20)
        ->and($air->abilityScores()->count())
        ->toBe(6)
        ->and($air->movements()->count())
        ->toBe(2)
        ->and(
            $air->movements()
                ->whereHas(
                    'movementType',
                    fn ($query) => $query->where('name', 'Volare')
                )
                ->firstOrFail()
                ->speed
        )->toBe(18.288)
        ->and($rend->attacks()->firstOrFail()->attack_type)
        ->toBe('melee')
        ->and($damage->formula)
        ->toBe('1d8 + 4')
        ->and($damage->damageType->name)
        ->toBe('Perforante')
        ->and($airForm->scalings()->count())
        ->toBe(5)
        ->and(
            $airForm->scalings()
                ->where('key', 'multiattack_from_slot')
                ->firstOrFail()
                ->rounding
        )->toBe('floor');
});

it('collega materiali riferimenti e pulisce gli stat block', function () {
    $summonBeast = Spell::query()
        ->where('key', 'summon_beast')
        ->firstOrFail();

    $material = $summonBeast
        ->materialComponents()
        ->firstOrFail();
    $reference = $summonBeast
        ->sourceReferences()
        ->firstOrFail();
    $statBlockIds = CreatureStatBlock::query()
        ->where('name', 'like', 'Spirito Bestiale%')
        ->pluck('id');

    expect((float) $material->cost_amount)
        ->toBe(200.0)
        ->and($material->currency->code)
        ->toBe('mo')
        ->and($material->cost_is_minimum)
        ->toBeTrue()
        ->and($material->consumed)
        ->toBeFalse()
        ->and($reference->page_start)
        ->toBe(112)
        ->and($reference->sourceBook->slug)
        ->toBe('tcoe-2020')
        ->and(
            SourceReference::query()
                ->where('sourceable_type', Spell::class)
                ->where('sourceable_id', $summonBeast->id)
                ->count()
        )->toBe(1)
        ->and($statBlockIds->count())
        ->toBe(3);

    $summonBeast->delete();

    expect(
        CreatureStatBlock::query()
            ->whereIn('id', $statBlockIds)
            ->count()
    )->toBe(0);
});
