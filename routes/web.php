<?php

use Illuminate\Support\Facades\Route;

//Rotta GET /:
//restituisce il nome del framework e la versione installata
Route::get('/', function () {
    return [
        'Laravel' => app()->version(),
    ];
});

//Carica le rotte web dedicate
//a registrazione, autenticazione e verifica email
require __DIR__ . '/auth.php';
