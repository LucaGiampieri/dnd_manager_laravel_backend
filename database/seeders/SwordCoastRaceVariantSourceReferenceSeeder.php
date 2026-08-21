<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\SourceBook;
use App\Models\Subrace;
use Illuminate\Database\Seeder;

class SwordCoastRaceVariantSourceReferenceSeeder extends Seeder
{
    //Collega le varianti alle pagine dello SCAG
    public function run(): void
    {
        $this->call([
            RulesetSeeder::class,
            SourceBookSeeder::class,
            SwordCoastRaceVariantSeeder::class,
        ]);

        $sourceBook = SourceBook::query()
            ->where('slug', 'scag-2015')
            ->firstOrFail();

        $halfElf = Race::query()
            ->where('key', 'half_elf')
            ->firstOrFail();

        $tiefling = Race::query()
            ->where('key', 'tiefling')
            ->firstOrFail();

        $feralTiefling = Subrace::query()
            ->where('key', 'feral_tiefling_scag_2015')
            ->firstOrFail();

        //Collega le varianti del Mezzelfo
        $halfElf->sourceReferences()->updateOrCreate(
            [
                'key' => 'scag_2015_it_half_elf_variants',
            ],
            [
                'source_book_id' => $sourceBook->id,
                'reference_type' => 'definition',
                'page_start' => 116,
                'page_end' => 116,
                'section' =>
                    'Capitolo 3: Varianti dei Mezzelfi',
                'is_primary' => false,
                'sort_order' => 20,
                'official_text' => null,
                'notes' =>
                    'Alternative opzionali a Versatilità '
                    . 'nelle Abilità.',
            ]
        );

        //Collega le varianti dei tratti del Tiefling
        $tiefling->sourceReferences()->updateOrCreate(
            [
                'key' => 'scag_2015_it_tiefling_variants',
            ],
            [
                'source_book_id' => $sourceBook->id,
                'reference_type' => 'definition',
                'page_start' => 118,
                'page_end' => 118,
                'section' =>
                    'Capitolo 3: Varianti dei Tiefling',
                'is_primary' => false,
                'sort_order' => 20,
                'official_text' => null,
                'notes' =>
                    'Varianti opzionali dei tratti razziali '
                    . 'del Tiefling.',
            ]
        );

        //Collega la variante del Tiefling Ferino
        $feralTiefling->sourceReferences()->updateOrCreate(
            [
                'key' => 'scag_2015_it_feral_tiefling',
            ],
            [
                'source_book_id' => $sourceBook->id,
                'reference_type' => 'definition',
                'page_start' => 118,
                'page_end' => 118,
                'section' =>
                    'Capitolo 3: Tiefling Ferino',
                'is_primary' => true,
                'sort_order' => 10,
                'official_text' => null,
                'notes' =>
                    'Variante degli incrementi di caratteristica '
                    . 'del Tiefling.',
            ]
        );
    }
}
