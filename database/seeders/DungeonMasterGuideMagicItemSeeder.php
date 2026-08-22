<?php

namespace Database\Seeders;

use App\Models\EffectDefinition;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\Ruleset;
use App\Models\SourceBook;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class DungeonMasterGuideMagicItemSeeder extends Seeder
{
    //Inserisce il primo catalogo di oggetti magici del DMG 2014
    public function run(): void
    {
        //Crea preventivamente tutti i cataloghi necessari
        $this->call([
            RulesetSeeder::class,
            SourceBookSeeder::class,
            ItemTypeSeeder::class,
        ]);

        //Recupera il regolamento della quinta edizione 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera il Manuale del Dungeon Master 2014
        $sourceBook = SourceBook::query()
            ->where('slug', 'dmg-2014')
            ->firstOrFail();

        //Indicizza le tipologie di oggetto tramite la chiave
        $itemTypes = ItemType::query()
            ->whereIn('key', [
                'potion',
                'weapon',
                'wondrous_item',
            ])
            ->get()
            ->keyBy('key');

        //Carica le definizioni separate dalla logica del seeder
        $definitions = require database_path(
            'data/dmg_2014_core_magic_items.php'
        );

        //Inserisce tutte le pozioni di guarigione
        foreach (
            $definitions['healing_potions'] as $definition
        ) {
            $this->seedHealingPotion(
                $ruleset,
                $sourceBook,
                $itemTypes,
                $definition
            );
        }

        //Inserisce i modelli Arma +1, +2 e +3
        foreach (
            $definitions['magic_weapons'] as $definition
        ) {
            $this->seedMagicWeapon(
                $ruleset,
                $sourceBook,
                $itemTypes,
                $definition
            );
        }

        //Inserisce i contenitori extradimensionali
        foreach ($definitions['containers'] as $definition) {
            $this->seedContainer(
                $ruleset,
                $sourceBook,
                $itemTypes,
                $definition
            );
        }
    }

    //Inserisce una pozione di guarigione e il suo effetto
    private function seedHealingPotion(
        Ruleset $ruleset,
        SourceBook $sourceBook,
        Collection $itemTypes,
        array $definition
    ): void {
        //Crea o aggiorna l'oggetto principale
        $item = $this->upsertItem(
            $ruleset,
            $itemTypes,
            'potion',
            $definition,
            0.227,
            true
        );

        //Registra le proprietà generali dell'oggetto magico
        $this->syncMagicProfile(
            $item,
            'La pozione perde definitivamente la propria magia '
            . 'quando viene utilizzata.'
        );

        //Registra la modalità di utilizzo della pozione
        $item->consumableProfile()->updateOrCreate(
            [
                'item_id' => $item->id,
            ],
            [
                'activation_type' => 'drink',
                'activation_action' => 'action',
                'activation_value' => 1,
                'target_scope' => 'self_or_creature',
                'uses_per_item' => 1,
                'consumed_on_use' => true,
                'leaves_container' => true,
                'special_rules' =>
                    'La dose viene consumata completamente '
                    . 'quando la pozione viene bevuta.',
                'notes' => null,
            ]
        );

        //Crea o aggiorna l'effetto di recupero dei punti ferita
        $effect = $item->effectDefinitions()->updateOrCreate(
            [
                'key' => 'restore_hit_points',
            ],
            [
                'name' => 'Recupero dei punti ferita',
                'application_type' => 'manual',
                'target_scope' => 'target',
                'ends_with_source' => false,
                'condition' =>
                    'La pozione deve essere bevuta o somministrata.',
                'description' => $definition['description'],
                'sort_order' => 10,
                'notes' => null,
            ]
        );

        //Registra la formula di guarigione strutturata
        $effect->healings()->updateOrCreate(
            [
                'key' => 'primary_healing',
            ],
            [
                'healing_type' => 'hit_points',
                'dice_count' => $definition['dice_count'],
                'die_size' => $definition['die_size'],
                'flat_bonus' => $definition['flat_bonus'],
                'modifier_source_type' => 'none',
                'modifier_ability_id' => null,
                'modifier_multiplier' => 1,
                'average_healing' =>
                    $definition['average_healing'],
                'temporary_hit_point_rule' => null,
                'is_primary' => true,
                'condition' => null,
                'sort_order' => 10,
                'notes' => null,
            ]
        );

        //Collega l'oggetto al manuale e alla pagina
        $this->syncSourceReference(
            $item,
            $sourceBook,
            $definition['page']
        );
    }

    //Inserisce un modello magico applicabile a qualsiasi arma
    private function seedMagicWeapon(
        Ruleset $ruleset,
        SourceBook $sourceBook,
        Collection $itemTypes,
        array $definition
    ): void {
        //Crea o aggiorna il modello di arma magica
        $item = $this->upsertItem(
            $ruleset,
            $itemTypes,
            'weapon',
            $definition,
            null,
            false
        );

        //Registra il profilo generale dell'oggetto magico
        $this->syncMagicProfile(
            $item,
            'Il modello viene applicato a una precisa arma base.'
        );

        //Rende il modello applicabile a qualsiasi arma non magica
        $item->magicApplicabilities()->updateOrCreate(
            [
                'key' => 'any_nonmagical_weapon',
            ],
            [
                'target_scope' => 'any_weapon',
                'target_item_id' => null,
                'target_item_type_id' => null,
                'weapon_category' => null,
                'armor_category' => null,
                'requires_nonmagical' => true,
                'condition' =>
                    'L’oggetto base deve essere un’arma non magica.',
                'sort_order' => 10,
                'notes' => null,
            ]
        );

        //Crea l'effetto automatico del bonus magico
        $effect = $item->effectDefinitions()->updateOrCreate(
            [
                'key' => 'magic_weapon_bonus',
            ],
            [
                'name' => 'Bonus dell’arma magica',
                'application_type' => 'automatic',
                'target_scope' => 'source',
                'ends_with_source' => true,
                'condition' =>
                    'Si applica ai tiri effettuati con l’arma.',
                'description' => $definition['description'],
                'sort_order' => 10,
                'notes' => null,
            ]
        );

        //Registra il bonus ai tiri per colpire
        $this->syncRollModifier(
            $effect,
            'attack',
            $definition['bonus'],
            10
        );

        //Registra il bonus ai tiri per i danni
        $this->syncRollModifier(
            $effect,
            'damage',
            $definition['bonus'],
            20
        );

        //Collega il modello magico alla fonte ufficiale
        $this->syncSourceReference(
            $item,
            $sourceBook,
            $definition['page']
        );
    }

    //Inserisce un contenitore magico extradimensionale
    private function seedContainer(
        Ruleset $ruleset,
        SourceBook $sourceBook,
        Collection $itemTypes,
        array $definition
    ): void {
        //Crea o aggiorna il contenitore
        $item = $this->upsertItem(
            $ruleset,
            $itemTypes,
            'wondrous_item',
            $definition,
            $definition['weight_kg'],
            false
        );

        //Registra il profilo generale dell'oggetto magico
        $this->syncMagicProfile(
            $item,
            'Il peso esterno non cambia in base al contenuto.'
        );

        //Registra capacità e regole extradimensionali
        $item->containerProfile()->updateOrCreate(
            [
                'item_id' => $item->id,
            ],
            [
                'capacity_weight_kg' =>
                    $definition['capacity_weight_kg'],
                'capacity_volume_liters' =>
                    $definition['capacity_volume_liters'],
                'ignores_contents_weight' => true,
                'is_extradimensional' => true,
                'retrieval_action' => 'action',
                'dimensions' => $definition['dimensions'],
                'living_creature_rules' =>
                    $definition['living_creature_rules'],
                'rupture_rules' =>
                    $definition['rupture_rules'],
                'nesting_rules' =>
                    $definition['nesting_rules'],
                'notes' => null,
            ]
        );

        //Collega il contenitore alla fonte ufficiale
        $this->syncSourceReference(
            $item,
            $sourceBook,
            $definition['page']
        );
    }

    //Crea o aggiorna un oggetto comune al catalogo
    private function upsertItem(
        Ruleset $ruleset,
        Collection $itemTypes,
        string $itemTypeKey,
        array $definition,
        ?float $weightKg,
        bool $isStackable
    ): Item {
        //Recupera la tipologia richiesta
        $itemType = $itemTypes->get($itemTypeKey);

        if ($itemType === null) {
            throw new RuntimeException(
                "Tipologia {$itemTypeKey} non trovata."
            );
        }

        //Crea o aggiorna l'oggetto senza duplicarlo
        return Item::query()->updateOrCreate(
            [
                'ruleset_id' => $ruleset->id,
                'key' => $definition['key'],
            ],
            [
                'canonical_key' => $definition['key'],
                'version_key' => 'dmg_2014',
                'is_legacy' => false,
                'name' => $definition['name'],
                'item_type_id' => $itemType->id,
                'description' => $definition['description'],
                'weight_kg' => $weightKg,
                'is_stackable' => $isStackable,
                'rarity' => $definition['rarity'],
                'is_magical' => true,
                'requires_attunement' => false,
                'requirements' => null,
                'notes' => null,
                'sort_order' => $definition['sort_order'],
            ]
        );
    }

    //Crea o aggiorna il profilo generale dell'oggetto magico
    private function syncMagicProfile(
        Item $item,
        string $specialRules
    ): void {
        $item->magicProfile()->updateOrCreate(
            [
                'item_id' => $item->id,
            ],
            [
                'base_item_id' => null,
                'attunement_requirement' => null,
                'is_cursed' => false,
                'curse_disclosure' => null,
                'destruction_condition' => null,
                'special_rules' => $specialRules,
                'notes' => null,
            ]
        );
    }

    //Crea o aggiorna un modificatore numerico ai tiri
    private function syncRollModifier(
        EffectDefinition $effect,
        string $rollType,
        int $value,
        int $sortOrder
    ): void {
        $effect->rollModifiers()->updateOrCreate(
            [
                'roll_type' => $rollType,
                'ability_id' => null,
                'skill_id' => null,
                'modifier_type' => 'bonus',
            ],
            [
                'value' => $value,
                'dice_count' => null,
                'die_size' => null,
                'condition' =>
                    'Il tiro deve essere effettuato con questa arma.',
                'sort_order' => $sortOrder,
                'notes' => null,
            ]
        );
    }

    //Collega un oggetto alla relativa pagina del DMG 2014
    private function syncSourceReference(
        Item $item,
        SourceBook $sourceBook,
        int $page
    ): void {
        $item->sourceReferences()->updateOrCreate(
            [
                'key' => 'dmg_2014_primary',
            ],
            [
                'source_book_id' => $sourceBook->id,
                'reference_type' => 'definition',
                'page_start' => $page,
                'page_end' => $page,
                'section' =>
                    'Capitolo 7: Tesori — Oggetti Magici',
                'is_primary' => true,
                'sort_order' => 10,
                'official_text' => null,
                'notes' =>
                    'Contenuto strutturato in italiano senza conservare '
                    . 'una copia pubblica del testo ufficiale.',
            ]
        );
    }
}
