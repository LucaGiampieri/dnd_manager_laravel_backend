<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    //Registra una rotta utilizzata esclusivamente dai test
    //e la protegge con sessione, autenticazione e verifica email
    Route::middleware([
        'web',
        'auth',
        'verified',
    ])->get('/test/verified-area', function () {
        //Restituisce la risposta prevista
        //quando tutti i middleware vengono superati
        return response()->json([
            'message' => 'Accesso consentito.',
        ]);
    });
});

test('blocca un utente con email non verificata', function () {
    //Crea un utente il cui indirizzo email non è verificato
    $user = User::factory()
        ->unverified()
        ->create();

    //Simula l’utente autenticato mentre prova
    //ad accedere all’area protetta
    $response = $this
        ->actingAs($user)
        ->getJson('/test/verified-area');

    //Verifica che il middleware risponda con HTTP 409
    //e con il messaggio italiano previsto
    $response
        ->assertStatus(409)
        ->assertJson([
            'message' => 'Il tuo indirizzo email non è stato verificato.',
        ]);
});

test('permette l’accesso a un utente con email verificata', function () {
    //Crea un utente che possiede già un’email verificata
    $user = User::factory()->create();

    //Simula l’utente autenticato mentre accede
    //alla stessa area protetta
    $response = $this
        ->actingAs($user)
        ->getJson('/test/verified-area');

    //Verifica che la richiesta abbia successo
    //e che restituisca il contenuto della rotta
    $response
        ->assertOk()
        ->assertJson([
            'message' => 'Accesso consentito.',
        ]);
});
