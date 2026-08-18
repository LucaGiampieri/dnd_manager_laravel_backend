<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Rotta GET /api/user:
//restituisce i dati dell’utente autenticato
Route::middleware([
    'auth:sanctum',
])->get('/user', function (Request $request) {
    //Recupera l’utente associato
    //alla richiesta autenticata tramite Sanctum
    return $request->user();
});
