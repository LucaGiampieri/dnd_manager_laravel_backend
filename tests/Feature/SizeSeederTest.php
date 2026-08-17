<?php

use App\Models\Size;
use Database\Seeders\SizeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea le sei taglie senza duplicati e nel giusto ordine', function () {
    $this->seed(SizeSeeder::class);
    $this->seed(SizeSeeder::class);

    $sizes = Size::query()
        ->orderBy('sort_order')
        ->get();

    expect($sizes)->toHaveCount(6)
        ->and($sizes->pluck('name')->all())->toBe([
            'Minuscola',
            'Piccola',
            'Media',
            'Grande',
            'Enorme',
            'Mastodontica',
        ])
        ->and($sizes->pluck('sort_order')->all())->toBe([
            1, 2, 3, 4, 5, 6,
        ])
        ->and($sizes->pluck('space_side_meters')->all())->toBe([
            '0.750',
            '1.500',
            '1.500',
            '3.000',
            '4.500',
            '6.000',
        ]);
});
