<?php

use App\Models\Size;
use Database\Seeders\SizeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per evitare interferenze con taglie già presenti
uses(RefreshDatabase::class);

it('crea le sei taglie senza duplicati e nel giusto ordine', function () {
    //Esegue due volte il seeder per verificare
    //che non vengano create taglie duplicate
    $this->seed(SizeSeeder::class);
    $this->seed(SizeSeeder::class);

    //Recupera tutte le taglie in base al loro ordine
    $sizes = Size::query()
        ->orderBy('sort_order')
        ->get();

    //Verifica che siano state create esattamente sei taglie
    expect($sizes)->toHaveCount(6);

    //Verifica i nomi italiani dal più piccolo al più grande
    expect($sizes->pluck('name')->all())->toBe([
        'Minuscola',
        'Piccola',
        'Media',
        'Grande',
        'Enorme',
        'Mastodontica',
    ]);

    //Verifica i valori utilizzati per ordinare le taglie
    expect($sizes->pluck('sort_order')->all())->toBe([
        1,
        2,
        3,
        4,
        5,
        6,
    ]);

    //Verifica il lato in metri dello spazio quadrato
    //normalmente controllato da ogni taglia
    expect($sizes->pluck('space_side_meters')->all())->toBe([
        '0.750',
        '1.500',
        '1.500',
        '3.000',
        '4.500',
        '6.000',
    ]);
});
