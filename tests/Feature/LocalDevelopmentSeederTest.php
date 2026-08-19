<?php

use App\Models\User;
use Database\Seeders\LocalDevelopmentSeeder;

it('non crea l’utente dimostrativo fuori dall’ambiente locale', function () {
    //I test Laravel vengono eseguiti nell'ambiente testing
    expect(app()->environment())->toBe('testing');

    //Prova a eseguire direttamente il seeder dei dati locali
    $this->seed(LocalDevelopmentSeeder::class);

    //Verifica che l'account dimostrativo non sia stato creato
    expect(
        User::query()
            ->where('email', 'test@example.com')
            ->exists()
    )->toBeFalse();
});
