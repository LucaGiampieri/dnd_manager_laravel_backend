<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('un indirizzo email può essere verificato', function () {
    //Crea un utente il cui indirizzo email non è ancora verificato
    $user = User::factory()
        ->unverified()
        ->create();

    //Intercetta gli eventi Laravel senza eseguirne
    //gli eventuali listener reali
    Event::fake();

    //Genera un collegamento temporaneo firmato
    //valido per sessanta minuti
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    //Simula l’utente autenticato mentre apre
    //il collegamento di verifica ricevuto
    $response = $this
        ->actingAs($user)
        ->get($verificationUrl);

    //Verifica che Laravel abbia emesso l’evento
    //che segnala la conferma dell’indirizzo email
    Event::assertDispatched(Verified::class);

    //Ricarica l’utente dal database e verifica
    //che l’indirizzo email risulti confermato
    expect(
        $user->fresh()->hasVerifiedEmail()
    )->toBeTrue();

    //Verifica il reindirizzamento al frontend
    //con il parametro che segnala la verifica completata
    $response->assertRedirect(
        config('app.frontend_url') . '/dashboard?verified=1'
    );
});

test('un indirizzo email non viene verificato con un hash errato', function () {
    //Crea un utente con indirizzo email non verificato
    $user = User::factory()
        ->unverified()
        ->create();

    //Genera un collegamento firmato utilizzando
    //un hash che non corrisponde all’email dell’utente
    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1('wrong-email'),
        ]
    );

    //Simula il tentativo di verifica dell’utente autenticato
    $this
        ->actingAs($user)
        ->get($verificationUrl);

    //Ricarica l’utente e verifica che l’indirizzo
    //sia rimasto non confermato
    expect(
        $user->fresh()->hasVerifiedEmail()
    )->toBeFalse();
});
