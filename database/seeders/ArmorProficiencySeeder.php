<?php

namespace Database\Seeders;

use App\Models\ArmorProficiency;
use App\Models\Item;
use App\Models\Ruleset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class ArmorProficiencySeeder extends Seeder
{
    //Inserisce le competenze nelle armature del PHB 2014
    public function run(): void
    {
        //Crea prima armature, scudi e cataloghi necessari
        $this->call(ArmorSeeder::class);

        //Recupera il regolamento D&D 5e 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera armature e scudo del PHB 2014
        $armorItems = Item::query()
            ->where('ruleset_id', $ruleset->id)
            ->where('version_key', 'phb_2014')
            ->whereHas('armorProfile')
            ->with('armorProfile')
            ->orderBy('sort_order')
            ->get();

        //Interrompe il seeding se il catalogo è incompleto
        if ($armorItems->count() !== 13) {
            throw new RuntimeException(
                'Il catalogo PHB 2014 deve contenere '
                . 'dodici armature e uno scudo.'
            );
        }

        //Crea tutte le categorie ufficiali
        $this->seedCategories(
            $ruleset->id,
            $armorItems
        );
    }

    //Crea e riempie le categorie di competenza
    private function seedCategories(
        int $rulesetId,
        Collection $armorItems
    ): void {
        //Definisce le quattro competenze ufficiali
        $categoryDefinitions = [
            [
                'key' => 'light_armor_phb_2014',
                'name' => 'Armature leggere',
                'armor_category' => 'light',
                'description' =>
                    'Concede competenza in tutte le armature leggere '
                    . 'del Manuale del Giocatore 2014.',
                'sort_order' => 10,
            ],
            [
                'key' => 'medium_armor_phb_2014',
                'name' => 'Armature medie',
                'armor_category' => 'medium',
                'description' =>
                    'Concede competenza in tutte le armature medie '
                    . 'del Manuale del Giocatore 2014.',
                'sort_order' => 20,
            ],
            [
                'key' => 'heavy_armor_phb_2014',
                'name' => 'Armature pesanti',
                'armor_category' => 'heavy',
                'description' =>
                    'Concede competenza in tutte le armature pesanti '
                    . 'del Manuale del Giocatore 2014.',
                'sort_order' => 30,
            ],
            [
                'key' => 'shields_phb_2014',
                'name' => 'Scudi',
                'armor_category' => 'shield',
                'description' =>
                    'Concede competenza nell’utilizzo degli scudi '
                    . 'del Manuale del Giocatore 2014.',
                'sort_order' => 40,
            ],
        ];

        //Crea ogni competenza e sincronizza i relativi oggetti
        foreach ($categoryDefinitions as $definition) {
            //Seleziona gli oggetti appartenenti alla categoria
            $categoryItems = $armorItems
                ->filter(
                    fn (Item $item): bool =>
                        $item->armorProfile?->armor_category
                        === $definition['armor_category']
                )
                ->values();

            //Impedisce la creazione di categorie vuote
            if ($categoryItems->isEmpty()) {
                throw new RuntimeException(
                    "La categoria {$definition['key']} "
                    . 'non contiene oggetti.'
                );
            }

            //Crea oppure aggiorna la competenza
            $proficiency = ArmorProficiency::query()
                ->updateOrCreate(
                    [
                        'ruleset_id' => $rulesetId,
                        'key' => $definition['key'],
                    ],
                    [
                        'name' => $definition['name'],
                        'type' => 'category',
                        'item_id' => null,
                        'description' => $definition['description'],
                        'sort_order' => $definition['sort_order'],
                    ]
                );

            //Sincronizza gli oggetti della categoria
            $proficiency->items()->sync(
                $categoryItems
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
                    ->all()
            );
        }
    }
}
