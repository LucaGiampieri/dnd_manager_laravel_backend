<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('un utente può richiedere il link per reimpostare la password', function () {
    //Impedisce l'invio reale delle notifiche
    Notification::fake();

    //Crea un utente su cui eseguire il test
    $user = User::factory()->create();

    //Richiede l'invio del collegamento di recupero
    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    //Verifica che Laravel abbia preparato la notifica
    Notification::assertSentTo(
        $user,
        ResetPassword::class
    );
});

test('un utente può reimpostare la password con un token valido', function () {
    //Impedisce l'invio reale delle notifiche
    Notification::fake();

    //Crea l'utente che deve recuperare la password
    $user = User::factory()->create();

    //Richiede il collegamento di recupero
    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    //Recupera il token dalla notifica preparata da Laravel
    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            //Invia la nuova password insieme al token
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            //Verifica che il cambio della password sia riuscito
            $response
                ->assertSessionHasNoErrors()
                ->assertStatus(200);

            return true;
        }
    );
});

test('il link di recupero password punta al frontend', function () {
    //Impedisce l'invio reale delle notifiche
    Notification::fake();

    //Crea l'utente che riceverà il collegamento
    $user = User::factory()->create();

    //Richiede l'invio del collegamento
    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    //Verifica il collegamento contenuto nella notifica
    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            //Genera l'email e recupera l'URL del pulsante
            $url = $notification->toMail($user)->actionUrl;

            //Verifica che il collegamento punti al frontend
            expect($url)->toStartWith(
                config('app.frontend_url')
                .'/password-reset/'
            );

            //Estrae la parte dell'URL contenente i parametri
            $queryString = parse_url(
                $url,
                PHP_URL_QUERY
            );

            //Riconverte i parametri dell'URL in un array
            parse_str(
                (string) $queryString,
                $query
            );

            //Verifica che l'email sia ancora quella dell'utente
            expect($query['email'] ?? null)
                ->toBe($user->email);

            return true;
        }
    );
});

test('codifica correttamente l’email nel link di recupero password', function () {
    //Impedisce l'invio reale delle notifiche
    Notification::fake();

    //Crea un utente con un carattere speciale nell'email
    $user = User::factory()->create([
        'email' => 'luca+prova@example.com',
    ]);

    //Richiede l'invio del collegamento di recupero
    $this->post('/forgot-password', [
        'email' => $user->email,
    ]);

    //Verifica la codifica del collegamento generato
    Notification::assertSentTo(
        $user,
        ResetPassword::class,
        function (ResetPassword $notification) use ($user) {
            //Genera l'email e recupera l'URL del pulsante
            $url = $notification->toMail($user)->actionUrl;

            //Verifica direttamente la codifica dei caratteri speciali
            expect($url)->toContain(
                'email=luca%2Bprova%40example.com'
            );

            //Estrae la parte dell'URL contenente i parametri
            $queryString = parse_url(
                $url,
                PHP_URL_QUERY
            );

            //Riconverte i parametri dell'URL in un array
            parse_str(
                (string) $queryString,
                $query
            );

            //Verifica che la decodifica restituisca l'email originale
            expect($query['email'] ?? null)
                ->toBe($user->email);

            return true;
        }
    );
});
