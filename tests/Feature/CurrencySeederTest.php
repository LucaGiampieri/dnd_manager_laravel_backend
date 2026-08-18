<?php

use App\Models\Currency;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('crea le cinque valute senza duplicati', function () {
    $this->seed(CurrencySeeder::class);
    $this->seed(CurrencySeeder::class);

    $currencies = Currency::query()
        ->orderBy('sort_order')
        ->get();

    expect($currencies)->toHaveCount(5)
        ->and($currencies->pluck('name')->all())->toBe([
            'Rame',
            'Argento',
            'Electrum',
            'Oro',
            'Platino',
        ])
        ->and($currencies->pluck('code')->all())->toBe([
            'mr',
            'ma',
            'me',
            'mo',
            'mp',
        ])
        ->and(
            $currencies
                ->pluck('value_in_copper_pieces')
                ->all()
        )->toBe([
            1,
            10,
            50,
            100,
            1000,
        ])
        ->and(
            $currencies
                ->pluck('coin_weight_kg')
                ->all()
        )->toBe([
            '0.0100',
            '0.0100',
            '0.0100',
            '0.0100',
            '0.0100',
        ])
        ->and(
            $currencies
                ->where('is_common', true)
                ->pluck('code')
                ->values()
                ->all()
        )->toBe([
            'mr',
            'ma',
            'mo',
        ]);
});
