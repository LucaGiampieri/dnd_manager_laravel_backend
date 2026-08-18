<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class SourceBookSeeder extends Seeder
{
    //Inserisce i tre manuali fondamentali del regolamento
    public function run(): void
    {
        //Recupera il regolamento a cui appartengono i manuali
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Definisce i manuali fondamentali in lingua italiana
        $sourceBooks = [
            //Manuale del Giocatore
            [
                'title' => 'Manuale del Giocatore',
                'original_title' => 'Player’s Handbook',
                'slug' => 'phb-2014',
                'abbreviation' => 'PHB',
                'type' => 'core_rulebook',
                'edition' => '5e',
                'language' => 'it',
                'is_official' => true,
                'is_playtest' => false,
                'is_active' => true,
            ],

            //Guida del Dungeon Master
            [
                'title' => 'Guida del Dungeon Master',
                'original_title' => 'Dungeon Master’s Guide',
                'slug' => 'dmg-2014',
                'abbreviation' => 'DMG',
                'type' => 'core_rulebook',
                'edition' => '5e',
                'language' => 'it',
                'is_official' => true,
                'is_playtest' => false,
                'is_active' => true,
            ],

            //Manuale dei Mostri
            [
                'title' => 'Manuale dei Mostri',
                'original_title' => 'Monster Manual',
                'slug' => 'mm-2014',
                'abbreviation' => 'MM',
                'type' => 'core_rulebook',
                'edition' => '5e',
                'language' => 'it',
                'is_official' => true,
                'is_playtest' => false,
                'is_active' => true,
            ],
        ];

        //Inserisce o aggiorna ogni manuale usando lo slug stabile
        foreach ($sourceBooks as $sourceBook) {
            $ruleset->sourceBooks()->updateOrCreate(
                //Identifica univocamente il manuale
                [
                    'slug' => $sourceBook['slug'],
                ],

                //Inserisce o aggiorna tutti i dati del manuale
                $sourceBook
            );
        }
    }
}
