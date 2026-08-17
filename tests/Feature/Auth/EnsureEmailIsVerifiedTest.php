<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'verified'])
        ->get('/test/verified-area', function () {
            return response()->json([
                'message' => 'Accesso consentito.',
            ]);
        });
});

test('blocca un utente con email non verificata', function () {
    $user = User::factory()->unverified()->create();

    $response = $this
        ->actingAs($user)
        ->getJson('/test/verified-area');

    $response
        ->assertStatus(409)
        ->assertJson([
            'message' => 'Il tuo indirizzo email non è stato verificato.',
        ]);
});

test('permette l’accesso a un utente con email verificata', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->getJson('/test/verified-area');

    $response
        ->assertOk()
        ->assertJson([
            'message' => 'Accesso consentito.',
        ]);
});
