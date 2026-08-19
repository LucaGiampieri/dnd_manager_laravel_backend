<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    //Registra i servizi utilizzati dall'applicazione
    public function register(): void
    {
        //Al momento non sono necessari servizi aggiuntivi
    }

    //Configura i servizi dopo l'avvio dell'applicazione
    public function boot(): void
    {
        //Personalizza il collegamento inviato per il recupero password
        ResetPassword::createUrlUsing(
            function (object $notifiable, string $token): string {
                //Recupera l'indirizzo del frontend senza barra finale
                $frontendUrl = rtrim(
                    (string) config('app.frontend_url'),
                    '/'
                );

                //Codifica correttamente l'email come parametro dell'URL
                $query = http_build_query(
                    [
                        'email' =>
                            $notifiable->getEmailForPasswordReset(),
                    ],
                    '',
                    '&',
                    PHP_QUERY_RFC3986
                );

                //Restituisce l'indirizzo completo della pagina frontend
                return $frontendUrl
                    .'/password-reset/'
                    .rawurlencode($token)
                    .'?'
                    .$query;
            }
        );
    }
}
