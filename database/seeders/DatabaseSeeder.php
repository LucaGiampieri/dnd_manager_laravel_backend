<?php

namespace Database\Seeders;

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

            //Crea i gradi di sfida con bonus di competenza e PE
            ChallengeRatingSeeder::class,

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

        //Inserisce i dati dimostrativi soltanto in ambiente locale
        if (app()->environment('local')) {
            $this->call(LocalDevelopmentSeeder::class);
        }
    }
}
