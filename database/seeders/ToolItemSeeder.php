<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Ruleset;
use Illuminate\Database\Seeder;
use RuntimeException;

class ToolItemSeeder extends Seeder
{
    //Inserisce tutti gli strumenti acquistabili del PHB 2014
    public function run(): void
    {
        //Crea i cataloghi da cui dipendono gli strumenti
        $this->call([
            RulesetSeeder::class,
            CurrencySeeder::class,
            ItemTypeSeeder::class,
        ]);

        //Recupera il regolamento D&D 5e 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera le tipologie utilizzate dagli strumenti
        $itemTypes = ItemType::query()
            ->whereIn('key', [
                'artisan_tool',
                'gaming_set',
                'musical_instrument',
                'kit',
                'other_tool',
            ])
            ->get()
            ->keyBy('key');

        //Recupera argento e oro tramite il valore in rame
        $currencies = Currency::query()
            ->whereIn('value_in_copper_pieces', [
                10,
                100,
            ])
            ->get()
            ->keyBy(
                fn (Currency $currency): int =>
                    (int) $currency->value_in_copper_pieces
            );

        //Carica le definizioni ufficiali
        $toolDefinitions = require database_path(
            'data/phb_2014_tools.php'
        );

        //Controlla che il catalogo sia completo
        if (count($toolDefinitions) !== 37) {
            throw new RuntimeException(
                'Il catalogo PHB 2014 deve contenere 37 strumenti.'
            );
        }

        //Inserisce ogni strumento
        foreach ($toolDefinitions as $index => $definition) {
            //Recupera la tipologia richiesta
            $itemType = $itemTypes->get(
                $definition['item_type_key']
            );

            //Interrompe il seeding se manca la tipologia
            if ($itemType === null) {
                throw new RuntimeException(
                    "Tipologia {$definition['item_type_key']} non trovata."
                );
            }

            //Recupera la valuta del prezzo
            $currency = $currencies->get(
                $definition['currency_value']
            );

            //Interrompe il seeding se manca la valuta
            if ($currency === null) {
                throw new RuntimeException(
                    'Valuta con valore '
                    . "{$definition['currency_value']} non trovata."
                );
            }

            //Crea oppure aggiorna lo strumento
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
                    'description' => $this->descriptionFor(
                        $definition['item_type_key'],
                        $definition['name']
                    ),
                    'weight_kg' => $definition['weight_kg'],
                    'is_stackable' => false,
                    'rarity' => 'common',
                    'is_magical' => false,
                    'requires_attunement' => false,
                    'requirements' => null,
                    'notes' => null,
                    'sort_order' => 2000 + (($index + 1) * 10),
                ]
            );

            //Crea oppure aggiorna il prezzo
            $item->costs()->updateOrCreate(
                [
                    'currency_id' => $currency->id,
                ],
                [
                    'amount' => $definition['cost_amount'],
                    'notes' => null,
                ]
            );

            //Rimuove eventuali prezzi obsoleti
            $item->costs()
                ->where('currency_id', '!=', $currency->id)
                ->delete();
        }
    }

    //Restituisce una descrizione italiana in base alla tipologia
    private function descriptionFor(
        string $itemTypeKey,
        string $name
    ): string {
        return match ($itemTypeKey) {
            'artisan_tool' =>
                "{$name}: strumenti e materiali necessari "
                . 'per esercitare la relativa professione artigianale.',

            'gaming_set' =>
                "{$name}: insieme di componenti utilizzato "
                . 'per praticare il relativo gioco.',

            'musical_instrument' =>
                "{$name}: strumento musicale utilizzabile "
                . 'per esibizioni e prove appropriate.',

            'kit' =>
                "{$name}: insieme di materiali specializzati "
                . 'per svolgere la relativa attività.',

            'other_tool' =>
                "{$name}: insieme di strumenti specializzati "
                . 'che richiede una competenza dedicata.',

            default =>
                "{$name}: strumento del Manuale del Giocatore 2014.",
        };
    }
}
