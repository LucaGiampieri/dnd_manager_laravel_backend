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
            SourceBookSeeder::class,
            AbilitySeeder::class,
            SkillSeeder::class,
            SizeSeeder::class,
            MovementTypeSeeder::class,
            MovementCostRuleSeeder::class,
            SpellSchoolSeeder::class,
            CurrencySeeder::class,
            SenseSeeder::class,
            LanguageSeeder::class,
            CreatureTypeSeeder::class,
            CreatureTagSeeder::class,
            DamageTypeSeeder::class,
            ConditionSeeder::class,
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
