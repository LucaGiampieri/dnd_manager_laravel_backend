<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('un nuovo utente può registrarsi', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertNoContent();
});

test('un nuovo utente riceve la notifica di verifica email', function () {
    Notification::fake();

    $this->post('/register', [
        'name' => 'Nuovo Utente',
        'email' => 'nuovo@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNoContent();

    $user = User::where('email', 'nuovo@example.com')->firstOrFail();

    expect($user->hasVerifiedEmail())->toBeFalse();

    Notification::assertSentTo($user, VerifyEmail::class);
});
