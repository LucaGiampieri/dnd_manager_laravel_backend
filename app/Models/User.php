<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    //Permette di creare utenti tramite la factory nei test e nei seeder
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    //Permette al modello di ricevere notifiche
    use Notifiable;

    //Campi che possono essere valorizzati tramite create o update
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    //Campi esclusi dalla serializzazione e dalle risposte JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //Converte automaticamente i valori nei tipi PHP corretti
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
