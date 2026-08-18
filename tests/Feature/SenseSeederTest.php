<?php

use App\Models\Sense;
use Database\Seeders\SenseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea i quattro sensi speciali senza duplicati', function () {
    $this->seed(SenseSeeder::class);
    $this->seed(SenseSeeder::class);

    $senses = Sense::query()
        ->orderBy('sort_order')
        ->get();

    expect($senses)->toHaveCount(4)
        ->and($senses->pluck('key')->all())->toBe([
            'blindsight',
            'darkvision',
            'tremorsense',
            'truesight',
        ])
        ->and($senses->pluck('name')->all())->toBe([
            'Vista Cieca',
            'Scurovisione',
            'Percezione Tellurica',
            'Vista Pura',
        ])
        ->and(Sense::whereNull('description')->count())->toBe(0);
});
