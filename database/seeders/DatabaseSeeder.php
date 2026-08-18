<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    //Evita di eseguire gli eventi dei modelli durante il seeding
    use WithoutModelEvents;

    //Avvia tutti i seeder principali nell'ordine corretto
    public function run(): void
    {
        //Inserisce i dati ufficiali e i cataloghi di base
        $this->call([
            //Crea il regolamento e i manuali
            RulesetSeeder::class,
            SourceBookSeeder::class,

            //Crea i nove allineamenti del regolamento
            AlignmentSeeder::class,

            //Crea caratteristiche e abilità
            AbilitySeeder::class,
            SkillSeeder::class,

            //Crea taglie e regole di movimento
            SizeSeeder::class,
            MovementTypeSeeder::class,
            MovementCostRuleSeeder::class,

            //Crea i cataloghi collegati alla magia
            SpellSchoolSeeder::class,

            //Crea valute, sensi e linguaggi
            CurrencySeeder::class,
            SenseSeeder::class,
            LanguageSeeder::class,

            //Crea tipi e tag delle creature
            CreatureTypeSeeder::class,
            CreatureTagSeeder::class,

            //Crea tipi di danno e condizioni
            DamageTypeSeeder::class,
            ConditionSeeder::class,
        ]);

        //Recupera l'utente di test oppure ne prepara uno nuovo
        $user = User::firstOrNew([
            'email' => 'test@example.com',
        ]);

        //Imposta i dati dell'utente di test
        $user->name = 'Test User';
        $user->email_verified_at = now();
        $user->password = 'password';

        //Inserisce o aggiorna l'utente nel database
        $user->save();
    }
}
