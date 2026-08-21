<?php

use App\Models\Ruleset;
use App\Models\WeaponProperty;
use Database\Seeders\WeaponPropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database prima del test
uses(RefreshDatabase::class);

//Verifica il catalogo delle proprietà delle armi
it('crea tutte le proprietà delle armi senza duplicati', function () {
    //Esegue due volte il seeder per verificarne l'idempotenza
    $this->seed(WeaponPropertySeeder::class);
    $this->seed(WeaponPropertySeeder::class);

    //Recupera il regolamento utilizzato dal catalogo
    $ruleset = Ruleset::query()
        ->where('key', 'dnd5e_2014')
        ->firstOrFail();

    //Definisce le chiavi tecniche nell'ordine previsto
    $expectedKeys = [
        'ammunition',
        'finesse',
        'heavy',
        'light',
        'loading',
        'range',
        'reach',
        'special',
        'thrown',
        'two_handed',
        'versatile',
    ];

    //Recupera tutte le proprietà nel loro ordine ufficiale
    $properties = WeaponProperty::query()
        ->where('ruleset_id', $ruleset->id)
        ->orderBy('sort_order')
        ->get();

    //Verifica quantità, chiavi e ordinamento
    expect($properties)->toHaveCount(11)
        ->and($properties->pluck('key')->all())
        ->toBe($expectedKeys)
        ->and($properties->pluck('sort_order')->all())
        ->toBe(range(10, 110, 10));

    //Verifica che tutte le proprietà appartengano al regolamento
    expect(
        $properties->every(
            fn (WeaponProperty $property): bool =>
                $property->ruleset->is($ruleset)
        )
    )->toBeTrue();

    //Verifica che nomi e descrizioni siano sempre presenti
    expect(
        $properties->pluck('name')->unique()->count()
    )->toBe(11)
        ->and(
            $properties->every(
                fn (WeaponProperty $property): bool =>
                    filled($property->description)
            )
        )
        ->toBeTrue();

    //Verifica alcune meccaniche particolarmente importanti
    expect(
        $properties->firstWhere('key', 'reach')->description
    )->toContain('1,5 metri')
        ->and(
            $properties->firstWhere('key', 'versatile')->description
        )
        ->toContain('due mani')
        ->and(
            $properties->firstWhere('key', 'loading')->description
        )
        ->toContain('una sola munizione');
});
