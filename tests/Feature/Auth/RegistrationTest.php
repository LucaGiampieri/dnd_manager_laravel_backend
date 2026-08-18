<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('un nuovo utente può registrarsi', function () {
    //Invia alla rotta di registrazione
    //i dati validi di un nuovo utente
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    //Verifica che il nuovo utente risulti autenticato
    $this->assertAuthenticated();

    //Verifica che la registrazione API risponda con HTTP 204
    $response->assertNoContent();

    //Verifica che il nuovo utente sia stato salvato nel database
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
});

test('un nuovo utente riceve la notifica di verifica email', function () {
    //Intercetta le notifiche per evitare
    //l’invio di una vera email durante il test
    Notification::fake();

    //Registra un nuovo utente e verifica
    //che la richiesta termini correttamente
    $this->post('/register', [
        'name' => 'Nuovo Utente',
        'email' => 'nuovo@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNoContent();

    //Recupera dal database l’utente appena registrato
    $user = User::query()
        ->where('email', 'nuovo@example.com')
        ->firstOrFail();

    //Verifica che l’indirizzo email
    //non sia ancora considerato confermato
    expect($user->hasVerifiedEmail())->toBeFalse();

    //Verifica che Laravel abbia preparato
    //la notifica contenente il link di verifica
    Notification::assertSentTo(
        $user,
        VerifyEmail::class
    );
});
