<?php

use App\Models\EffectDefinitionRollModifier;
use App\Models\EffectDefinitionScaling;
use App\Models\Spell;
use Database\Seeders\PlayerHandbookSpellSeeder;
use Database\Seeders\TashasCauldronSpellSeeder;
use Database\Seeders\XanatharsGuideSpellSeeder;
use Illuminate\Support\Facades\DB;

//Un solo ciclo completo per verificare insieme tutti i manuali e le relazioni.
//RefreshDatabase è già applicato ai test Feature in tests/Pest.php.
it('salva tutti gli effetti senza duplicati e pulisce le progressioni obsolete', function () {
    $seeders = [
        PlayerHandbookSpellSeeder::class,
        XanatharsGuideSpellSeeder::class,
        TashasCauldronSpellSeeder::class,
    ];
    foreach ($seeders as $seeder) {
        $this->seed($seeder);
    }

    $tables = [
        'spells', 'spell_target_profiles', 'spell_material_components',
        'source_references', 'effect_definitions', 'effect_definition_damages',
        'effect_definition_healings', 'effect_definition_roll_modifiers',
        'effect_definition_scalings', 'effect_definition_durations',
        'effect_definition_forced_movements', 'spell_summons',
        'spell_summon_templates', 'spell_summon_template_forms',
        'spell_summon_template_scalings', 'creature_stat_blocks',
    ];
    $snapshot = fn (): array => collect($tables)->mapWithKeys(
        fn (string $table): array => [$table => DB::table($table)->count()]
    )->all();
    $before = $snapshot();
    $ids = Spell::query()->orderBy('key')->pluck('id', 'key')->all();

    expect(Spell::where('version_key', 'phb_2014')->count())->toBe(361)
        ->and(Spell::where('version_key', 'xgte_2017')->count())->toBe(95)
        ->and(Spell::where('version_key', 'tcoe_2020')->count())->toBe(21)
        ->and(Spell::whereDoesntHave('targetProfile')->count())->toBe(0)
        ->and(Spell::whereDoesntHave('sourceReferences')->count())->toBe(0)
        ->and(Spell::where('material_component', true)->whereDoesntHave('materialComponents')->count())->toBe(0);

    $fireball = Spell::where('key', 'fireball')->firstOrFail();
    $fireDamage = $fireball->effectDefinitions()->firstOrFail()->damages()->firstOrFail();
    expect($fireDamage->formula)->toBe('8d6')
        ->and($fireDamage->damageType->name)->toBe('Fuoco')
        ->and($fireDamage->scalings()->firstOrFail()->source_offset)->toBe(-3.0);

    $sleep = Spell::where('key', 'sleep')->firstOrFail();
    $sleepEffect = $sleep->effectDefinitions()->firstOrFail();
    $pool = $sleepEffect->rollModifiers()->firstOrFail();
    expect($pool->dice_count)->toBe(5)
        ->and($pool->die_size)->toBe(8)
        ->and($pool->scalings()->count())->toBe(1)
        ->and($pool->scalings()->firstOrFail()->multiplier)->toBe(2.0);

    //Simula una vecchia progressione e un modificatore rimosso dal file dati.
    $obsoleteScaling = $pool->scalings()->create([
        'key' => 'obsolete_rule', 'target_field' => 'dice_count',
        'source_type' => 'fixed', 'fixed_value' => 99,
    ]);
    $obsoleteModifier = $sleepEffect->rollModifiers()->create([
        'roll_type' => 'other', 'modifier_type' => 'bonus',
        'value' => 99, 'sort_order' => 999,
    ]);
    $orphanCandidate = $obsoleteModifier->scalings()->create([
        'key' => 'removed_modifier_rule', 'target_field' => 'value',
        'source_type' => 'fixed', 'fixed_value' => 99,
    ]);

    foreach ($seeders as $seeder) {
        $this->seed($seeder);
    }

    expect($snapshot())->toBe($before)
        ->and(Spell::query()->orderBy('key')->pluck('id', 'key')->all())->toBe($ids)
        ->and(EffectDefinitionScaling::find($obsoleteScaling->id))->toBeNull()
        ->and(EffectDefinitionScaling::find($orphanCandidate->id))->toBeNull()
        ->and(EffectDefinitionRollModifier::find($obsoleteModifier->id))->toBeNull();

    //Le progressioni del modificatore devono sparire anche eliminando lo spell.
    $poolScalingIds = $pool->scalings()->pluck('id');
    expect($poolScalingIds)->not->toBeEmpty();
    $sleep->delete();
    expect(EffectDefinitionRollModifier::find($pool->id))->toBeNull()
        ->and(EffectDefinitionScaling::whereIn('id', $poolScalingIds)->count())->toBe(0);
});
