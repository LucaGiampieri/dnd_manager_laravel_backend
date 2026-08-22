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

            //Crea il catalogo delle regole opzionali collegate ai manuali
            OptionalRuleSeeder::class,

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

            //Crea gli incantesimi del Manuale del Giocatore 2014
            PlayerHandbookSpellSeeder::class,

            //Crea valute e cataloghi dell'equipaggiamento
            CurrencySeeder::class,
            ItemTypeSeeder::class,
            WeaponPropertySeeder::class,

            //Crea tutte le armi comuni del PHB 2014
            WeaponSeeder::class,

            //Crea le categorie e le competenze nelle singole armi
            WeaponProficiencySeeder::class,

            //Crea armature e scudo del Manuale del Giocatore 2014
            ArmorSeeder::class,

            //Crea le competenze nelle categorie di armature e scudi
            ArmorProficiencySeeder::class,

            //Crea gli strumenti acquistabili del Manuale del Giocatore
            ToolItemSeeder::class,

            //Crea competenze negli strumenti e nei veicoli
            ToolProficiencySeeder::class,

            //Crea il primo catalogo di oggetti magici del DMG 2014
            DungeonMasterGuideMagicItemSeeder::class,

            //Crea sensi e linguaggi
            SenseSeeder::class,
            LanguageSeeder::class,

            //Crea tipi e tag delle creature
            CreatureTypeSeeder::class,
            CreatureTagSeeder::class,

            //Crea razze e sottorazze del Manuale del Giocatore
            RaceSeeder::class,

            //Crea i bonus fissi delle razze del Manuale del Giocatore
            RaceAbilityBonusSeeder::class,

            //Crea le scelte flessibili del Mezzelfo e dell'Umano Variante
            RaceChoiceSeeder::class,

            //Crea le capacità delle razze del Manuale del Giocatore
            RaceFeatureSeeder::class,

            //Crea le capacità delle razze pubblicate nell'EEPC
            ElementalEvilRaceFeatureSeeder::class,

            //Collega le razze PHB alle pagine del manuale
            RaceSourceReferenceSeeder::class,

            //Crea le versioni delle razze pubblicate nell'EEPC
            ElementalEvilRaceSeeder::class,

            //Crea i bonus delle razze EEPC compatibili con Tasha
            ElementalEvilRaceAbilityBonusSeeder::class,

            //Collega le razze EEPC alle pagine del manuale
            ElementalEvilRaceSourceReferenceSeeder::class,

            //Crea le sottorazze pubblicate nello SCAG
            SwordCoastRaceSeeder::class,

            //Crea i bonus delle sottorazze SCAG
            SwordCoastRaceAbilityBonusSeeder::class,

            //Crea le capacità delle sottorazze SCAG
            SwordCoastRaceFeatureSeeder::class,

            //Collega le sottorazze SCAG alle pagine del manuale
            SwordCoastRaceSourceReferenceSeeder::class,

            //Crea le varianti razziali opzionali dello SCAG
            SwordCoastRaceVariantSeeder::class,

            //Collega le varianti SCAG alle pagine del manuale
            SwordCoastRaceVariantSourceReferenceSeeder::class,

            //Crea sensi e lingue di razze e sottorazze
            RaceSenseSeeder::class,
            RaceLanguageSeeder::class,

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
