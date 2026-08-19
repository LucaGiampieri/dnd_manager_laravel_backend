<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    //Invia una nuova notifica per la verifica dell'indirizzo email
    public function store(Request $request): JsonResponse
    {
        //Recupera l'utente autenticato dalla richiesta
        $user = $request->user();

        //Evita di inviare una nuova email se è già stata verificata
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'email-already-verified',
            ]);
        }

        //Invia all'utente una nuova notifica di verifica
        $user->sendEmailVerificationNotification();

        //Comunica al frontend che la notifica è stata inviata
        return response()->json([
            'status' => 'verification-link-sent',
        ]);
    }
}
