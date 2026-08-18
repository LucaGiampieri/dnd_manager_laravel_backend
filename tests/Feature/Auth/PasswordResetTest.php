<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('un utente può richiedere il link per reimpostare la password', function () {
    //Intercetta le notifiche senza inviare email reali
    Notification::fake();

    //Crea l’utente che richiederà il recupero della password
    $user = User::factory()->create();

    //Invia la richiesta utilizzando l’indirizzo email dell’utente
    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    //Verifica che Laravel abbia preparato
    //la notifica contenente il link di recupero
    Notification::assertSentTo(
        $user,
        ResetPassword::class
    );
});

test('un utente può reimpostare la password con un token valido', function () {
    //Intercetta la notifica per poter leggere
    //il token senza inviare un’email reale
    Notification::fake();

    //Crea l’utente che reimposterà la password
    $user = User::factory()->create();

    //Richiede l’invio del link per il recupero
    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    //Verifica che la notifica sia stata generata
    //e utilizza il token contenuto al suo interno
    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            //Invia il token insieme alla nuova password
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            //Verifica che la richiesta non abbia prodotto
            //errori di validazione e sia terminata correttamente
            $response
                ->assertSessionHasNoErrors()
                ->assertStatus(200);

            //Ricarica l’utente dal database e verifica
            //che la nuova password sia stata realmente salvata
            expect(
                Hash::check(
                    'new-password',
                    $user->fresh()->password
                )
            )->toBeTrue();

            //Conferma alla verifica della notifica
            //che tutti i controlli sono stati completati
            return true;
        }
    );
});

test('il link di recupero password punta al frontend', function () {
    //Intercetta le notifiche senza inviare email reali
    Notification::fake();

    //Crea l’utente che richiederà il recupero
    $user = User::factory()->create();

    //Richiede la notifica per reimpostare la password
    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    //Intercetta la notifica e genera il messaggio email
    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            //Recupera l’indirizzo del pulsante
            //presente nell’email di recupero
            $url = $notification
                ->toMail($user)
                ->actionUrl;

            //Verifica che il collegamento punti al frontend React
            //e contenga l’indirizzo email dell’utente
            expect($url)
                ->toStartWith(
                    config('app.frontend_url')
                    . '/password-reset/'
                )
                ->toContain(
                    'email=' . $user->email
                );

            //Conferma che la notifica ha superato i controlli
            return true;
        }
    );
});
