<?php

use App\Models\Currency;
use Database\Seeders\CurrencySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per evitare che valute già presenti alterino il risultato
uses(RefreshDatabase::class);

it('crea le cinque valute senza duplicati', function () {
    //Esegue due volte il seeder per verificarne l’idempotenza
    $this->seed(CurrencySeeder::class);
    $this->seed(CurrencySeeder::class);

    //Recupera tutte le valute nell’ordine crescente stabilito
    $currencies = Currency::query()
        ->orderBy('sort_order')
        ->get();

    //Verifica che siano state create esattamente cinque valute
    expect($currencies)->toHaveCount(5);

    //Verifica i nomi italiani e il loro ordine
    expect($currencies->pluck('name')->all())->toBe([
        'Rame',
        'Argento',
        'Electrum',
        'Oro',
        'Platino',
    ]);

    //Verifica i codici abbreviati delle cinque valute
    expect($currencies->pluck('code')->all())->toBe([
        'mr',
        'ma',
        'me',
        'mo',
        'mp',
    ]);

    //Verifica il valore di ogni valuta espresso in monete di rame
    expect(
        $currencies
            ->pluck('value_in_copper_pieces')
            ->all()
    )->toBe([
        1,
        10,
        50,
        100,
        1000,
    ]);

    //Verifica che ogni singola moneta pesi 0,01 chilogrammi
    expect(
        $currencies
            ->pluck('coin_weight_kg')
            ->all()
    )->toBe([
        '0.0100',
        '0.0100',
        '0.0100',
        '0.0100',
        '0.0100',
    ]);

    //Verifica che Rame, Argento e Oro siano
    //le denominazioni contrassegnate come comuni
    expect(
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
