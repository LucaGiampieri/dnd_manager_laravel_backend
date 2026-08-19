<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

it('invia una nuova notifica a un utente non verificato', function () {
    //Impedisce l'invio reale delle notifiche
    Notification::fake();

    //Crea un utente che non ha ancora verificato l'email
    $user = User::factory()
        ->unverified()
        ->create();

    //Richiede una nuova email di verifica
    $response = $this
        ->actingAs($user)
        ->postJson('/email/verification-notification');

    //Verifica la risposta JSON restituita al frontend
    $response
        ->assertOk()
        ->assertJson([
            'status' => 'verification-link-sent',
        ]);

    //Verifica che la notifica sia stata preparata per l'utente
    Notification::assertSentTo(
        $user,
        VerifyEmail::class
    );
});

it('non invia una nuova notifica a un utente già verificato', function () {
    //Impedisce l'invio reale delle notifiche
    Notification::fake();

    //Crea un utente con l'indirizzo email già verificato
    $user = User::factory()->create();

    //Prova a richiedere nuovamente l'email di verifica
    $response = $this
        ->actingAs($user)
        ->postJson('/email/verification-notification');

    //Verifica che il backend comunichi lo stato corretto
    $response
        ->assertOk()
        ->assertJson([
            'status' => 'email-already-verified',
        ]);

    //Verifica che non sia stata preparata alcuna notifica
    Notification::assertNothingSent();
});
