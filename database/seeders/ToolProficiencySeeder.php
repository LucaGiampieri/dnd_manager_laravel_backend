<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Ruleset;
use App\Models\ToolProficiency;
use Illuminate\Database\Seeder;
use RuntimeException;

class ToolProficiencySeeder extends Seeder
{
    //Crea le competenze negli strumenti del PHB 2014
    public function run(): void
    {
        //Crea prima tutti gli strumenti acquistabili
        $this->call(ToolItemSeeder::class);

        //Recupera il regolamento D&D 5e 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Carica le definizioni degli strumenti
        $toolDefinitions = require database_path(
            'data/phb_2014_tools.php'
        );

        //Recupera le chiavi degli strumenti ufficiali
        $toolKeys = collect($toolDefinitions)
            ->pluck('key')
            ->all();

        //Recupera tutti gli strumenti dal database
        $toolItems = Item::query()
            ->where('ruleset_id', $ruleset->id)
            ->where('version_key', 'phb_2014')
            ->whereIn('key', $toolKeys)
            ->with('itemType')
            ->orderBy('sort_order')
            ->get();

        //Interrompe il seeding se manca uno strumento
        if ($toolItems->count() !== 37) {
            throw new RuntimeException(
                'Il catalogo PHB 2014 deve contenere '
                . '37 strumenti acquistabili.'
            );
        }

        //Crea una competenza specifica per ogni strumento
        foreach ($toolItems as $index => $toolItem) {
            //Costruisce una chiave stabile e legata alla versione
            $proficiencyKey =
                "tool_{$toolItem->canonical_key}_phb_2014";

            //Crea oppure aggiorna la competenza
            $proficiency = ToolProficiency::query()
                ->updateOrCreate(
                    [
                        'ruleset_id' => $ruleset->id,
                        'key' => $proficiencyKey,
                    ],
                    [
                        'name' => "Competenza: {$toolItem->name}",
                        'type' => 'specific',
                        'item_id' => $toolItem->id,
                        'description' =>
                            'Concede competenza nell’utilizzo di '
                            . "{$toolItem->name}.",
                        'sort_order' => 100 + (($index + 1) * 10),
                    ]
                );

            //Una competenza specifica usa item_id e non il gruppo
            $proficiency->items()->detach();
        }

        //Crea le competenze generali nei veicoli
        $this->seedVehicleProficiencies($ruleset->id);
    }

    //Crea le competenze nei veicoli terrestri e acquatici
    private function seedVehicleProficiencies(
        int $rulesetId
    ): void {
        //Definisce le due categorie ufficiali
        $vehicleDefinitions = [
            [
                'key' => 'land_vehicles_phb_2014',
                'name' => 'Veicoli terrestri',
                'description' =>
                    'Concede competenza nel controllo dei veicoli '
                    . 'terrestri in circostanze difficili.',
                'sort_order' => 1000,
            ],
            [
                'key' => 'water_vehicles_phb_2014',
                'name' => 'Veicoli acquatici',
                'description' =>
                    'Concede competenza nel controllo dei veicoli '
                    . 'acquatici in circostanze difficili.',
                'sort_order' => 1010,
            ],
        ];

        //Crea entrambe le competenze di categoria
        foreach ($vehicleDefinitions as $definition) {
            $proficiency = ToolProficiency::query()
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

            //Le categorie saranno riempite quando creeremo i veicoli
            $proficiency->items()->detach();
        }
    }
}
