<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Currency;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Ruleset;
use Illuminate\Database\Seeder;
use RuntimeException;

class ArmorSeeder extends Seeder
{
    //Inserisce tutte le armature e lo scudo del PHB 2014
    public function run(): void
    {
        //Crea prima tutti i cataloghi necessari
        $this->call([
            RulesetSeeder::class,
            AbilitySeeder::class,
            CurrencySeeder::class,
            ItemTypeSeeder::class,
        ]);

        //Recupera il regolamento D&D 5e 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera le tipologie armatura e scudo
        $itemTypes = ItemType::query()
            ->whereIn('key', [
                'armor',
                'shield',
            ])
            ->get()
            ->keyBy('key');

        //Recupera la moneta d'oro attraverso il valore in rame
        $goldCurrency = Currency::query()
            ->where('value_in_copper_pieces', 100)
            ->firstOrFail();

        //Recupera la caratteristica Forza
        $strength = Ability::query()
            ->where('short_name', 'FOR')
            ->firstOrFail();

        //Carica le definizioni delle armature
        $armorDefinitions = require database_path(
            'data/phb_2014_armor.php'
        );

        //Inserisce ogni armatura senza creare duplicati
        foreach ($armorDefinitions as $definition) {
            //Recupera la tipologia dell'oggetto
            $itemType = $itemTypes->get(
                $definition['item_type_key']
            );

            //Interrompe il seeding se manca la tipologia richiesta
            if ($itemType === null) {
                throw new RuntimeException(
                    "Tipologia {$definition['item_type_key']} non trovata."
                );
            }

            //Crea oppure aggiorna l'oggetto
            $item = Item::query()->updateOrCreate(
                [
                    'ruleset_id' => $ruleset->id,
                    'key' => $definition['key'],
                ],
                [
                    'canonical_key' => $definition['key'],
                    'version_key' => 'phb_2014',
                    'is_legacy' => false,
                    'name' => $definition['name'],
                    'item_type_id' => $itemType->id,
                    'description' => $definition['description'],
                    'weight_kg' => $definition['weight_kg'],
                    'is_stackable' => false,
                    'rarity' => 'common',
                    'is_magical' => false,
                    'requires_attunement' => false,
                    'requirements' => null,
                    'notes' => $definition['notes'],
                    'sort_order' => $definition['sort_order'],
                ]
            );

            //Registra il prezzo in monete d'oro
            $item->costs()->updateOrCreate(
                [
                    'currency_id' => $goldCurrency->id,
                ],
                [
                    'amount' => $definition['cost_gp'],
                    'notes' => null,
                ]
            );

            //Rimuove eventuali prezzi obsoleti in altre valute
            $item->costs()
                ->where('currency_id', '!=', $goldCurrency->id)
                ->delete();

            //Determina se l'armatura richiede un valore di Forza
            $requiresStrength =
                $definition['minimum_strength'] !== null;

            //Crea oppure aggiorna il profilo meccanico
            $item->armorProfile()->updateOrCreate(
                [
                    'item_id' => $item->id,
                ],
                [
                    'armor_category' =>
                        $definition['armor_category'],
                    'armor_class_operation' =>
                        $definition['armor_class_operation'],
                    'armor_class_value' =>
                        $definition['armor_class_value'],
                    'dexterity_modifier' =>
                        $definition['dexterity_modifier'],
                    'max_dexterity_bonus' =>
                        $definition['max_dexterity_bonus'],
                    'requirement_ability_id' =>
                        $requiresStrength
                            ? $strength->id
                            : null,
                    'minimum_ability_score' =>
                        $definition['minimum_strength'],
                    'stealth_disadvantage' =>
                        $definition['stealth_disadvantage'],
                    'don_time_minutes' =>
                        $definition['don_time_minutes'],
                    'don_time_actions' =>
                        $definition['don_time_actions'],
                    'doff_time_minutes' =>
                        $definition['doff_time_minutes'],
                    'doff_time_actions' =>
                        $definition['doff_time_actions'],
                    'notes' => null,
                ]
            );
        }
    }
}
