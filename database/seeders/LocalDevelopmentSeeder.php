<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class LocalDevelopmentSeeder extends Seeder
{
    //Inserisce dati utili esclusivamente durante lo sviluppo locale
    public function run(): void
    {
        //Interrompe il seeder se l'applicazione non è in ambiente locale
        if (! app()->environment('local')) {
            return;
        }

        //Crea oppure aggiorna l'utente utilizzato per le prove manuali
        User::updateOrCreate(
            [
                'email' => 'test@example.com',
            ],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => 'password',
            ]
        );
    }
}
