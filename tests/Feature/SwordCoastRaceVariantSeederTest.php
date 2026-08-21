<?php

use App\Models\Feature;
use App\Models\Race;
use App\Models\Subrace;
use Database\Seeders\SwordCoastRaceVariantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima di ogni test
uses(RefreshDatabase::class);

//Verifica la creazione idempotente del Tiefling Ferino
it('crea il Tiefling Ferino e i suoi bonus senza duplicati', function () {
    $this->seed(SwordCoastRaceVariantSeeder::class);
    $this->seed(SwordCoastRaceVariantSeeder::class);

    $feralTiefling = Subrace::query()
        ->where('key', 'feral_tiefling_scag_2015')
        ->with([
            'race',
            'abilityBonuses.ability',
        ])
        ->firstOrFail();

    expect(
        Subrace::query()
            ->where('key', 'feral_tiefling_scag_2015')
            ->count()
    )->toBe(1)
        ->and($feralTiefling->race->key)->toBe('tiefling')
        ->and($feralTiefling->is_variant)->toBeTrue()
        ->and($feralTiefling->replaces_race_ability_bonuses)
        ->toBeTrue()
        ->and($feralTiefling->requires_dm_permission)
        ->toBeTrue()
        ->and($feralTiefling->abilityBonuses)
        ->toHaveCount(2);

    $bonuses = $feralTiefling->abilityBonuses
        ->mapWithKeys(
            fn ($bonus) => [
                $bonus->ability->short_name => $bonus->bonus,
            ]
        )
        ->all();

    expect($bonuses)->toBe([
        'DES' => 2,
        'INT' => 1,
    ]);
});

//Verifica le alternative del Mezzelfo
it('crea le varianti di ascendenza del Mezzelfo', function () {
    $this->seed(SwordCoastRaceVariantSeeder::class);

    $halfElf = Race::query()
        ->where('key', 'half_elf')
        ->firstOrFail();

    $choice = $halfElf->choices()
        ->where(
            'key',
            'half_elf_ancestry_trait_scag_2015'
        )
        ->with('replacedFeature')
        ->firstOrFail();

    $options = $choice->options()
        ->orderBy('sort_order')
        ->get();

    expect($choice->replacedFeature->key)
        ->toBe('half_elf_skill_versatility_phb_2014')
        ->and($choice->required)->toBeFalse()
        ->and($choice->requires_dm_permission)->toBeTrue()
        ->and($options)->toHaveCount(7)
        ->and($options->pluck('ancestry_key')->all())
        ->toBe([
            'wood_elf',
            'wood_elf',
            'wood_elf',
            'high_elf',
            'high_elf',
            'drow',
            'aquatic_elf',
        ]);
});

//Verifica le alternative del Tiefling
it('crea le varianti del Retaggio Infernale', function () {
    $this->seed(SwordCoastRaceVariantSeeder::class);

    $tiefling = Race::query()
        ->where('key', 'tiefling')
        ->firstOrFail();

    $choice = $tiefling->choices()
        ->where(
            'key',
            'tiefling_infernal_legacy_variant_scag_2015'
        )
        ->with('replacedFeature')
        ->firstOrFail();

    $options = $choice->options()
        ->orderBy('sort_order')
        ->get();

    expect($choice->replacedFeature->key)
        ->toBe('tiefling_infernal_legacy_phb_2014')
        ->and($choice->required)->toBeFalse()
        ->and($choice->requires_dm_permission)->toBeTrue()
        ->and($options->pluck('key')->all())
        ->toBe([
            'devils_tongue',
            'hellfire',
            'winged',
        ]);
});

//Verifica le capacità alternative create nel catalogo
it('crea tutte le capacità alternative dello SCAG', function () {
    $this->seed(SwordCoastRaceVariantSeeder::class);

    $featureKeys = [
        'half_elf_wood_weapon_training_scag_2015',
        'half_elf_fleet_of_foot_scag_2015',
        'half_elf_mask_of_the_wild_scag_2015',
        'half_elf_high_weapon_training_scag_2015',
        'half_elf_high_cantrip_scag_2015',
        'half_elf_drow_magic_scag_2015',
        'half_elf_swim_speed_scag_2015',
        'tiefling_devils_tongue_scag_2015',
        'tiefling_hellfire_scag_2015',
        'tiefling_winged_scag_2015',
    ];

    expect(
        Feature::query()
            ->whereIn('key', $featureKeys)
            ->count()
    )->toBe(10);
});

//Verifica le proprietà meccaniche principali
it('descrive volo nuoto e magia delle varianti', function () {
    $this->seed(SwordCoastRaceVariantSeeder::class);

    $winged = Feature::query()
        ->where('key', 'tiefling_winged_scag_2015')
        ->firstOrFail();

    $swimming = Feature::query()
        ->where('key', 'half_elf_swim_speed_scag_2015')
        ->firstOrFail();

    $drowMagic = Feature::query()
        ->where('key', 'half_elf_drow_magic_scag_2015')
        ->firstOrFail();

    expect($winged->description)
        ->toContain('9 metri')
        ->toContain('armature pesanti')
        ->and($swimming->description)
        ->toContain('9 metri')
        ->and($drowMagic->description)
        ->toContain('3° livello')
        ->toContain('5° livello');
});
