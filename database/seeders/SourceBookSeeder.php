<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class SourceBookSeeder extends Seeder
{
    public function run(): void
    {
        $ruleset = Ruleset::where('key', 'dnd5e_2014')
            ->firstOrFail();

        $sourceBooks = [
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

        foreach ($sourceBooks as $sourceBook) {
            $ruleset->sourceBooks()->updateOrCreate(
                [
                    'slug' => $sourceBook['slug'],
                ],
                $sourceBook
            );
        }
    }
}
