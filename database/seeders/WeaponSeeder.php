<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\DamageType;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Ruleset;
use App\Models\WeaponProperty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class WeaponSeeder extends Seeder
{
    //Inserisce tutte le armi base del PHB 2014
    public function run(): void
    {
        //Crea tutti i cataloghi richiesti dalle armi
        $this->call([
            RulesetSeeder::class,
            CurrencySeeder::class,
            DamageTypeSeeder::class,
            ItemTypeSeeder::class,
            WeaponPropertySeeder::class,
        ]);

        //Recupera il regolamento delle armi
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera la tipologia generale delle armi
        $weaponType = ItemType::query()
            ->where('key', 'weapon')
            ->firstOrFail();

        //Indicizza le valute attraverso il loro valore in monete di rame
        $currencies = Currency::query()
            ->get()
            ->keyBy(
                fn (Currency $currency): int =>
                    (int) $currency->value_in_copper_pieces
            );

        //Indicizza i tipi di danno attraverso il nome italiano
        $damageTypes = DamageType::query()
            ->whereIn('name', [
                'Contundente',
                'Perforante',
                'Tagliente',
            ])
            ->get()
            ->keyBy('name');

        //Indicizza le proprietà attraverso la chiave tecnica
        $properties = WeaponProperty::query()
            ->where('ruleset_id', $ruleset->id)
            ->get()
            ->keyBy('key');

        //Carica le definizioni delle armi dal file dati
        $weaponDefinitions = require database_path(
            'data/phb_2014_weapons.php'
        );

        //Inserisce o aggiorna ogni arma senza duplicarla
        foreach (
            $weaponDefinitions as $index => $definition
        ) {
            //Crea o aggiorna l'oggetto principale
            $weapon = Item::query()->updateOrCreate(
                [
                    'ruleset_id' => $ruleset->id,
                    'key' => $definition['key'],
                ],
                [
                    'canonical_key' => $definition['key'],
                    'version_key' => 'phb_2014',
                    'is_legacy' => false,
                    'name' => $definition['name'],
                    'item_type_id' => $weaponType->id,
                    'description' => $this->descriptionFor(
                        $definition['category'],
                        $definition['attack_type']
                    ),
                    'weight_kg' => $definition['weight_kg'],
                    'is_stackable' => false,
                    'rarity' => null,
                    'is_magical' => false,
                    'requires_attunement' => false,
                    'requirements' => null,
                    'notes' => null,
                    'sort_order' => ($index + 1) * 10,
                ]
            );

            //Sincronizza il prezzo dell'arma
            $this->syncCost(
                $weapon,
                $currencies,
                $definition
            );

            //Crea o aggiorna il profilo meccanico dell'arma
            $profile = $weapon->weaponProfile()->updateOrCreate(
                [
                    'item_id' => $weapon->id,
                ],
                [
                    'weapon_category' => $definition['category'],
                    'attack_type' => $definition['attack_type'],
                    'range' => $definition['range'],
                    'long_range' => $definition['long_range'],
                    'uses_ammunition' =>
                        $definition['uses_ammunition'],
                    'capacity' => null,
                    'notes' => $definition['notes'],
                ]
            );

            //Sincronizza il danno principale dell'arma
            $this->syncDamage(
                $profile,
                $damageTypes,
                $definition['damage']
            );

            //Sincronizza tutte le proprietà dell'arma
            $this->syncProperties(
                $profile,
                $properties,
                $definition['properties']
            );
        }
    }

    //Restituisce una descrizione sintetica della categoria dell'arma
    private function descriptionFor(
        string $category,
        string $attackType
    ): string {
        //Traduce la categoria meccanica
        $categoryName = $category === 'simple'
            ? 'Arma semplice'
            : 'Arma marziale';

        //Traduce la modalità principale di attacco
        $attackDescription = $attackType === 'melee'
            ? 'da mischia'
            : 'a distanza';

        return "{$categoryName} {$attackDescription} "
            . 'del regolamento 2014.';
    }

    //Crea o aggiorna il prezzo ufficiale dell'arma
    private function syncCost(
        Item $weapon,
        Collection $currencies,
        array $definition
    ): void {
        //Recupera la valuta attraverso il suo valore in rame
        $currency = $currencies->get(
            $definition['currency_value']
        );

        //Interrompe il seeding se manca la valuta richiesta
        if ($currency === null) {
            throw new RuntimeException(
                'Valuta con valore '
                . "{$definition['currency_value']} "
                . 'monete di rame non trovata.'
            );
        }

        //Rimuove eventuali prezzi obsoleti espressi in altre valute
        $weapon->costs()
            ->where('currency_id', '!=', $currency->id)
            ->delete();

        //Crea o aggiorna il prezzo ufficiale
        $weapon->costs()->updateOrCreate(
            [
                'currency_id' => $currency->id,
            ],
            [
                'amount' => $definition['cost_amount'],
                'notes' => null,
            ]
        );
    }

    //Crea o aggiorna il danno principale dell'arma
    private function syncDamage(
        mixed $profile,
        Collection $damageTypes,
        ?array $damageDefinition
    ): void {
        //Le armi prive di danno non devono possedere componenti
        if ($damageDefinition === null) {
            $profile->damages()->delete();

            return;
        }

        //Recupera il tipo di danno italiano
        $damageType = $damageTypes->get(
            $damageDefinition['type']
        );

        //Interrompe il seeding se manca il tipo di danno
        if ($damageType === null) {
            throw new RuntimeException(
                "Tipo di danno {$damageDefinition['type']} "
                . 'non trovato.'
            );
        }

        //Crea o aggiorna il danno principale
        $profile->damages()->updateOrCreate(
            [
                'sort_order' => 10,
            ],
            [
                'damage_type_id' => $damageType->id,
                'dice_count' => $damageDefinition['dice_count'],
                'die_size' => $damageDefinition['die_size'],
                'bonus' => $damageDefinition['bonus'],
                'primary' => true,
                'notes' => null,
            ]
        );

        //Rimuove eventuali componenti di danno obsoleti
        $profile->damages()
            ->where('sort_order', '!=', 10)
            ->delete();
    }

    //Sincronizza le proprietà meccaniche dell'arma
    private function syncProperties(
        mixed $profile,
        Collection $properties,
        array $propertyDefinitions
    ): void {
        //Prepara i dati destinati alla tabella pivot
        $syncData = [];

        foreach (
            $propertyDefinitions as $key => $configuration
        ) {
            //Recupera la proprietà attraverso la chiave tecnica
            $property = $properties->get($key);

            //Interrompe il seeding se manca una proprietà
            if ($property === null) {
                throw new RuntimeException(
                    "Proprietà dell’arma {$key} non trovata."
                );
            }

            //Registra valori e note specifici dell'arma
            $syncData[$property->id] = [
                'value' => $configuration['value'] ?? null,
                'value_text' =>
                    $configuration['value_text'] ?? null,
                'notes' => $configuration['notes'] ?? null,
            ];
        }

        //Aggiunge, aggiorna o rimuove le proprietà della singola arma
        $profile->properties()->sync($syncData);
    }
}
