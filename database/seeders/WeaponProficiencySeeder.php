<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Ruleset;
use App\Models\WeaponProficiency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class WeaponProficiencySeeder extends Seeder
{
    //Crea le competenze nelle armi del Manuale del Giocatore 2014
    public function run(): void
    {
        //Crea prima tutte le armi e i cataloghi da cui dipendono
        $this->call(WeaponSeeder::class);

        //Recupera il regolamento D&D 5e del 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera tutte le armi della versione PHB 2014
        $weapons = Item::query()
            ->where('ruleset_id', $ruleset->id)
            ->where('version_key', 'phb_2014')
            ->whereHas('weaponProfile')
            ->with('weaponProfile')
            ->orderBy('sort_order')
            ->get();

        //Interrompe il seeding se il catalogo delle armi è incompleto
        if ($weapons->count() !== 37) {
            throw new RuntimeException(
                'Il catalogo delle armi PHB 2014 deve contenere 37 armi.'
            );
        }

        //Crea le competenze che rappresentano gruppi di armi
        $this->seedCategories(
            $ruleset->id,
            $weapons
        );

        //Crea una competenza specifica per ogni singola arma
        $this->seedSpecificWeaponProficiencies(
            $ruleset->id,
            $weapons
        );
    }

    //Crea le categorie generali di competenza nelle armi
    private function seedCategories(
        int $rulesetId,
        Collection $weapons
    ): void {
        //Definisce le categorie utilizzabili da razze e classi
        $categoryDefinitions = [
            [
                'key' => 'simple_weapons_phb_2014',
                'name' => 'Armi semplici',
                'weapon_category' => 'simple',
                'attack_type' => null,
                'description' =>
                    'Concede competenza in tutte le armi semplici '
                    . 'del Manuale del Giocatore 2014.',
                'sort_order' => 10,
            ],
            [
                'key' => 'simple_melee_weapons_phb_2014',
                'name' => 'Armi semplici da mischia',
                'weapon_category' => 'simple',
                'attack_type' => 'melee',
                'description' =>
                    'Concede competenza in tutte le armi semplici '
                    . 'da mischia del Manuale del Giocatore 2014.',
                'sort_order' => 20,
            ],
            [
                'key' => 'simple_ranged_weapons_phb_2014',
                'name' => 'Armi semplici a distanza',
                'weapon_category' => 'simple',
                'attack_type' => 'ranged',
                'description' =>
                    'Concede competenza in tutte le armi semplici '
                    . 'a distanza del Manuale del Giocatore 2014.',
                'sort_order' => 30,
            ],
            [
                'key' => 'martial_weapons_phb_2014',
                'name' => 'Armi marziali',
                'weapon_category' => 'martial',
                'attack_type' => null,
                'description' =>
                    'Concede competenza in tutte le armi marziali '
                    . 'del Manuale del Giocatore 2014.',
                'sort_order' => 40,
            ],
            [
                'key' => 'martial_melee_weapons_phb_2014',
                'name' => 'Armi marziali da mischia',
                'weapon_category' => 'martial',
                'attack_type' => 'melee',
                'description' =>
                    'Concede competenza in tutte le armi marziali '
                    . 'da mischia del Manuale del Giocatore 2014.',
                'sort_order' => 50,
            ],
            [
                'key' => 'martial_ranged_weapons_phb_2014',
                'name' => 'Armi marziali a distanza',
                'weapon_category' => 'martial',
                'attack_type' => 'ranged',
                'description' =>
                    'Concede competenza in tutte le armi marziali '
                    . 'a distanza del Manuale del Giocatore 2014.',
                'sort_order' => 60,
            ],
        ];

        //Crea e riempie ogni categoria
        foreach ($categoryDefinitions as $definition) {
            //Seleziona le armi appartenenti alla categoria
            $categoryWeapons = $weapons
                ->filter(function (Item $weapon) use ($definition): bool {
                    $profile = $weapon->weaponProfile;

                    //Esclude eventuali oggetti senza profilo
                    if ($profile === null) {
                        return false;
                    }

                    //Controlla la categoria semplice o marziale
                    if (
                        $profile->weapon_category
                        !== $definition['weapon_category']
                    ) {
                        return false;
                    }

                    //Se non è richiesta una modalità di attacco,
                    //la categoria comprende sia mischia sia distanza
                    if ($definition['attack_type'] === null) {
                        return true;
                    }

                    //Controlla la modalità di attacco richiesta
                    return $profile->attack_type
                        === $definition['attack_type'];
                })
                ->values();

            //Impedisce la creazione di categorie vuote
            if ($categoryWeapons->isEmpty()) {
                throw new RuntimeException(
                    "La categoria {$definition['key']} non contiene armi."
                );
            }

            //Crea o aggiorna la competenza di categoria
            $proficiency = WeaponProficiency::query()
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

            //Sincronizza tutte le armi appartenenti alla categoria
            $proficiency->items()->sync(
                $categoryWeapons
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
                    ->all()
            );
        }
    }

    //Crea una competenza specifica per ciascuna arma
    private function seedSpecificWeaponProficiencies(
        int $rulesetId,
        Collection $weapons
    ): void {
        //Crea una voce distinta per ogni arma
        foreach ($weapons as $weapon) {
            //Costruisce una chiave stabile e legata alla versione
            $proficiencyKey =
                "weapon_{$weapon->canonical_key}_phb_2014";

            //Crea o aggiorna la competenza specifica
            $proficiency = WeaponProficiency::query()
                ->updateOrCreate(
                    [
                        'ruleset_id' => $rulesetId,
                        'key' => $proficiencyKey,
                    ],
                    [
                        'name' => "Competenza: {$weapon->name}",
                        'type' => 'specific',
                        'item_id' => $weapon->id,
                        'description' =>
                            "Concede competenza nell’uso dell’arma "
                            . "{$weapon->name}.",
                        'sort_order' =>
                            1000 + (int) $weapon->sort_order,
                    ]
                );

            //Una competenza specifica usa item_id
            //e non deve possedere armi nella tabella di gruppo
            $proficiency->items()->detach();
        }
    }
}
