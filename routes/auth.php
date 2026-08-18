<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

//Rotta POST /register:
//registra un nuovo utente non autenticato
Route::post(
    '/register',
    [
        RegisteredUserController::class,
        'store',
    ]
)
    ->middleware('guest')
    ->name('register');

//Rotta POST /login:
//autentica un utente tramite email e password
Route::post(
    '/login',
    [
        AuthenticatedSessionController::class,
        'store',
    ]
)
    ->middleware('guest')
    ->name('login');

//Rotta POST /forgot-password:
//invia il collegamento per reimpostare la password
Route::post(
    '/forgot-password',
    [
        PasswordResetLinkController::class,
        'store',
    ]
)
    ->middleware('guest')
    ->name('password.email');

//Rotta POST /reset-password:
//salva la nuova password utilizzando un token valido
Route::post(
    '/reset-password',
    [
        NewPasswordController::class,
        'store',
    ]
)
    ->middleware('guest')
    ->name('password.store');

//Rotta GET /verify-email/{id}/{hash}:
//conferma l’indirizzo email tramite un collegamento firmato
Route::get(
    '/verify-email/{id}/{hash}',
    VerifyEmailController::class
)
    ->middleware([
        //Richiede che l’utente abbia effettuato l’accesso
        'auth',

        //Verifica che l’indirizzo non sia stato modificato
        'signed',

        //Permette al massimo sei tentativi in un minuto
        'throttle:6,1',
    ])
    ->name('verification.verify');

//Rotta POST /email/verification-notification:
//invia nuovamente la notifica di verifica dell’email
Route::post(
    '/email/verification-notification',
    [
        EmailVerificationNotificationController::class,
        'store',
    ]
)
    ->middleware([
        //Richiede un utente autenticato
        'auth',

        //Limita l’invio a sei richieste in un minuto
        'throttle:6,1',
    ])
    ->name('verification.send');

//Rotta POST /logout:
//termina la sessione dell’utente autenticato
Route::post(
    '/logout',
    [
        AuthenticatedSessionController::class,
        'destroy',
    ]
)
    ->middleware('auth')
    ->name('logout');
