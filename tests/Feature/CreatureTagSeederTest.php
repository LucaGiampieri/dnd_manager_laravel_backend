<?php

use App\Models\CreatureTag;
use Database\Seeders\CreatureTagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea i tag base delle creature senza duplicati', function () {
    $this->seed(CreatureTagSeeder::class);
    $this->seed(CreatureTagSeeder::class);

    $creatureTags = CreatureTag::query()
        ->orderBy('sort_order')
        ->get();

    expect($creatureTags)->toHaveCount(27)
        ->and($creatureTags->pluck('key')->all())->toBe([
            'shapechanger',
            'titan',
            'any_race',
            'demon',
            'devil',
            'yugoloth',
            'aarakocra',
            'bullywug',
            'dwarf',
            'elf',
            'gith',
            'gnoll',
            'gnome',
            'goblinoid',
            'grimlock',
            'human',
            'kenku',
            'kobold',
            'kuo_toa',
            'lizardfolk',
            'merfolk',
            'orc',
            'quaggoth',
            'sahuagin',
            'thri_kreen',
            'troglodyte',
            'yuan_ti',
        ])
        ->and($creatureTags->pluck('name')->all())->toBe([
            'Mutaforma',
            'Titano',
            'Qualsiasi razza',
            'Demone',
            'Diavolo',
            'Yugoloth',
            'Aarakocra',
            'Bullywug',
            'Nano',
            'Elfo',
            'Gith',
            'Gnoll',
            'Gnomo',
            'Goblinoide',
            'Grimlock',
            'Umano',
            'Kenku',
            'Coboldo',
            'Kuo-toa',
            'Lucertoloide',
            'Marinide',
            'Orco',
            'Quaggoth',
            'Sahuagin',
            'Thri-kreen',
            'Troglodita',
            'Yuan-ti',
        ])
        ->and(
            CreatureTag::query()
                ->whereNull('description')
                ->count()
        )->toBe(0);
});
