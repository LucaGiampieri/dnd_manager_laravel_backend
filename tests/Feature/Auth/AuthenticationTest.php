<?php

use App\Models\User;

test('un utente può autenticarsi con credenziali corrette', function () {
    //Crea un utente tramite la factory
    //la cui password predefinita è "password"
    $user = User::factory()->create();

    //Invia le credenziali corrette alla rotta di login
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    //Verifica che Laravel riconosca l’utente come autenticato
    $this->assertAuthenticated();

    //Verifica che il login API risponda con HTTP 204
    //senza restituire contenuto nel corpo della risposta
    $response->assertNoContent();
});

test('un utente non può autenticarsi con una password errata', function () {
    //Crea un utente tramite la factory
    $user = User::factory()->create();

    //Prova a effettuare il login utilizzando una password errata
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    //Verifica che nessun utente risulti autenticato
    $this->assertGuest();
});

test('un utente può effettuare il logout', function () {
    //Crea un utente tramite la factory
    $user = User::factory()->create();

    //Simula un utente autenticato e invia la richiesta di logout
    $response = $this
        ->actingAs($user)
        ->post('/logout');

    //Verifica che dopo il logout l’utente sia diventato un ospite
    $this->assertGuest();

    //Verifica che il logout API risponda con HTTP 204
    $response->assertNoContent();
});
