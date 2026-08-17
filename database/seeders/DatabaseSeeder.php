<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RulesetSeeder::class,
        ]);

        $user = User::firstOrNew([
            'email' => 'test@example.com',
        ]);

        $user->name = 'Test User';
        $user->email_verified_at = now();
        $user->password = 'password';

        $user->save();
    }
}
