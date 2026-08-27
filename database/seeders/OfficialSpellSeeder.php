<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\CreatureStatBlock;
use App\Models\CreatureStatBlockAction;
use App\Models\CreatureType;
use App\Models\Currency;
use App\Models\DamageType;
use App\Models\EffectDefinition;
use App\Models\EffectDefinitionDamage;
use App\Models\EffectDefinitionHealing;
use App\Models\EffectDefinitionRollModifier;
use App\Models\MovementType;
use App\Models\Ruleset;
use App\Models\Size;
use App\Models\SourceBook;
use App\Models\Spell;
use App\Models\SpellSchool;
use App\Models\SpellSummon;
use App\Models\SpellSummonTemplate;
use App\Models\SpellSummonTemplateForm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

abstract class OfficialSpellSeeder extends Seeder
{
    /**
     * Inserisce un catalogo di incantesimi ufficiali mantenendo
     * separati manuale, versione e riferimenti bibliografici.
     *
     * @param array{
     *     source_book_slug: string,
     *     version_key: string,
     *     reference_key: string,
     *     section: string,
     *     source_notes: string,
     *     data_files: array<int, string>
     * } $configuration
     */
    protected function seedOfficialSpellCatalog(
        array $configuration
    ): void {
        //Crea tutti i cataloghi richiesti dagli incantesimi
        $this->call([
            RulesetSeeder::class,
            SourceBookSeeder::class,
            AbilitySeeder::class,
            CurrencySeeder::class,
            SpellSchoolSeeder::class,
            DamageTypeSeeder::class,
            CreatureTypeSeeder::class,
            SizeSeeder::class,
            MovementTypeSeeder::class,
        ]);

        //Recupera il regolamento D&D 5e del 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera il manuale che definisce questa versione
        $sourceBook = SourceBook::query()
            ->where('slug', $configuration['source_book_slug'])
            ->firstOrFail();

        //Indicizza i cataloghi tramite le loro chiavi tecniche
        $schools = SpellSchool::query()->pluck('id', 'key');
        $abilities = Ability::query()->pluck('id', 'short_name');
        $currencies = Currency::query()->pluck('id', 'code');
        $damageTypes = DamageType::query()->pluck('id', 'name');
        $creatureTypes = CreatureType::query()->pluck('id', 'key');
        $sizes = Size::query()->pluck('id', 'name');
        $movementTypes = MovementType::query()->pluck('id', 'name');

        foreach ($configuration['data_files'] as $dataFile) {
            $spellData = require database_path($dataFile);

            foreach ($spellData as $data) {
                $this->seedSpell(
                    $data,
                    $configuration,
                    $ruleset,
                    $sourceBook,
                    $schools,
                    $abilities,
                    $currencies,
                    $damageTypes,
                    $creatureTypes,
                    $sizes,
                    $movementTypes
                );
            }
        }
    }

    /**
     * Inserisce un singolo incantesimo e tutte le sue relazioni.
     */
    private function seedSpell(
        array $data,
        array $configuration,
        Ruleset $ruleset,
        SourceBook $sourceBook,
        Collection $schools,
        Collection $abilities,
        Collection $currencies,
        Collection $damageTypes,
        Collection $creatureTypes,
        Collection $sizes,
        Collection $movementTypes
    ): void {
        $schoolId = $schools->get($data['school_key']);

        if ($schoolId === null) {
            throw new RuntimeException(
                "Scuola {$data['school_key']} non trovata."
            );
        }

        $savingThrowAbilityId = null;

        //Recupera l'eventuale caratteristica del tiro salvezza
        if ($data['saving_throw'] !== null) {
            $savingThrowAbilityId = $abilities->get(
                $data['saving_throw']
            );

            if ($savingThrowAbilityId === null) {
                throw new RuntimeException(
                    "Caratteristica {$data['saving_throw']} "
                    . 'non trovata.'
                );
            }
        }

        $canonicalKey = $data['canonical_key'] ?? $data['key'];

        //Una chiave tecnica già usata da un'altra versione non può
        //essere sovrascritta accidentalmente da questo catalogo
        $existingSpell = Spell::query()
            ->where('ruleset_id', $ruleset->id)
            ->where('key', $data['key'])
            ->first();

        if (
            $existingSpell !== null
            && $existingSpell->version_key
                !== $configuration['version_key']
        ) {
            throw new RuntimeException(
                "La chiave {$data['key']} appartiene già alla versione "
                . "{$existingSpell->version_key}."
            );
        }

        //Crea o aggiorna soltanto la versione del manuale corrente
        $spell = Spell::query()->updateOrCreate(
            [
                'ruleset_id' => $ruleset->id,
                'key' => $data['key'],
            ],
            [
                'canonical_key' => $canonicalKey,
                'version_key' => $configuration['version_key'],
                'is_legacy' => $data['is_legacy'] ?? false,
                'name' => $data['name'],
                'level' => $data['level'],
                'spell_school_id' => $schoolId,
                'casting_time_value' =>
                    $data['casting_time_value'],
                'casting_time_type' =>
                    $data['casting_time_type'],
                'casting_trigger' => $data['casting_trigger'],
                'range_type' => $data['range_type'],
                'range' => $data['range'],
                'verbal_component' => $data['verbal_component'],
                'somatic_component' => $data['somatic_component'],
                'material_component' =>
                    $data['material_component'],
                'material_description' =>
                    $data['material_description'],
                'material_consumed' => $data['material_consumed'],
                'material_cost' => $data['material_cost'],
                'duration_type' => $data['duration_type'],
                'duration_value' => $data['duration_value'],
                'concentration' => $data['concentration'],
                'ritual' => $data['ritual'],
                'attack_type' => $data['attack_type'],
                'saving_throw_ability_id' =>
                    $savingThrowAbilityId,
                'save_success_damage' =>
                    $data['save_success_damage'],
                'description' => $data['description'],
                'higher_levels' => $data['higher_levels'],
                'notes' => $data['notes'],
            ]
        );

        //Crea o aggiorna il profilo del bersaglio
        $spell->targetProfile()->updateOrCreate(
            [],
            $data['target']
        );

        $this->syncMaterialComponents(
            $spell,
            $data,
            $currencies
        );

        $this->syncEffects(
            $spell,
            $data,
            $abilities,
            $damageTypes
        );

        $this->syncSummons(
            $spell,
            $data,
            $abilities,
            $damageTypes,
            $creatureTypes,
            $sizes,
            $movementTypes
        );

        //Collega l'incantesimo alla pagina del manuale
        $spell->sourceReferences()->updateOrCreate(
            [
                'source_book_id' => $sourceBook->id,
                'reference_type' => 'definition',
            ],
            [
                'key' => $configuration['reference_key'],
                'page_start' => $data['page'],
                'page_end' => $data['page'],
                'section' => $configuration['section'],
                'is_primary' => true,
                'sort_order' => 10,
                'official_text' => null,
                'notes' => $configuration['source_notes'],
            ]
        );
    }

    //Sincronizza tutti i componenti materiali di un incantesimo
    private function syncMaterialComponents(
        Spell $spell,
        array $data,
        Collection $currencies
    ): void {
        $materials = $data['materials']
            ?? $this->buildSummaryMaterial($data);

        if (! $data['material_component'] && $materials !== []) {
            throw new RuntimeException(
                "L'incantesimo {$data['key']} possiede dettagli "
                . 'materiali ma non usa la componente M.'
            );
        }

        $materialKeys = collect($materials)->pluck('key')->all();

        if (count($materialKeys) !== count(array_unique($materialKeys))) {
            throw new RuntimeException(
                "L'incantesimo {$data['key']} contiene componenti "
                . 'materiali duplicati.'
            );
        }

        //Elimina i dettagli rimossi mantenendo l'idempotenza
        if ($materialKeys === []) {
            $spell->materialComponents()->delete();
        } else {
            $spell->materialComponents()
                ->whereNotIn('key', $materialKeys)
                ->delete();
        }

        foreach ($materials as $material) {
            $currencyCode = $material['currency_code'] ?? null;
            $currencyId = null;

            if ($currencyCode !== null) {
                $currencyId = $currencies->get($currencyCode);

                if ($currencyId === null) {
                    throw new RuntimeException(
                        "Valuta {$currencyCode} non trovata per "
                        . "l'incantesimo {$data['key']}."
                    );
                }
            }

            $spell->materialComponents()->updateOrCreate(
                ['key' => $material['key']],
                [
                    'name' => $material['name'],
                    'description' =>
                        $material['description'] ?? null,
                    'quantity' => $material['quantity'] ?? null,
                    'unit' => $material['unit'] ?? null,
                    'cost_amount' =>
                        $material['cost_amount'] ?? null,
                    'currency_id' => $currencyId,
                    'cost_is_minimum' =>
                        $material['cost_is_minimum'] ?? false,
                    'consumed' => $material['consumed'] ?? false,
                    'focus_replaceable' =>
                        $material['focus_replaceable'] ?? true,
                    'sort_order' => $material['sort_order'] ?? 0,
                    'notes' => $material['notes'] ?? null,
                ]
            );
        }
    }

    //Converte i campi riassuntivi in un requisito dettagliato
    private function buildSummaryMaterial(array $data): array
    {
        if (! $data['material_component']) {
            return [];
        }

        $hasCost = $data['material_cost'] !== null;
        $isConsumed = $data['material_consumed'];
        $description = $data['material_description'];

        return [[
            'key' => 'material_requirement',
            'name' => 'Requisito materiale',
            'description' => $description,
            'quantity' => null,
            'unit' => null,
            'cost_amount' => $data['material_cost'],
            'currency_code' => $hasCost ? 'mo' : null,
            'cost_is_minimum' => $hasCost
                && $description !== null
                && str_contains(
                    mb_strtolower($description),
                    'almeno'
                ),
            'consumed' => $isConsumed,
            'focus_replaceable' => ! $hasCost && ! $isConsumed,
            'sort_order' => 1,
            'notes' => 'Requisito completo ricavato dal catalogo '
                . 'dell’incantesimo.',
        ]];
    }

    //Sincronizza gli effetti strutturati soltanto quando sono presenti
    //nel file dati, senza alterare i cataloghi precedenti.
    private function syncEffects(
        Spell $spell,
        array $data,
        Collection $abilities,
        Collection $damageTypes
    ): void {
        if (! array_key_exists('effects', $data)) {
            return;
        }

        $effects = $data['effects'];
        $effectKeys = $this->uniqueKeys(
            $effects,
            "gli effetti dell'incantesimo {$data['key']}"
        );

        //Elimina gli effetti rimossi attivando anche la pulizia
        //delle progressioni polimorfiche collegate.
        $obsoleteEffects = $spell->effectDefinitions();

        if ($effectKeys !== []) {
            $obsoleteEffects->whereNotIn('key', $effectKeys);
        }

        $obsoleteEffects->get()->each(function (
            EffectDefinition $effect
        ): void {
            $effect->delete();
        });

        foreach ($effects as $effectData) {
            $effect = $spell->effectDefinitions()->updateOrCreate(
                ['key' => $effectData['key']],
                [
                    'name' => $effectData['name'] ?? null,
                    'application_type' =>
                        $effectData['application_type'] ?? 'automatic',
                    'target_scope' =>
                        $effectData['target_scope'] ?? 'target',
                    'ends_with_source' =>
                        $effectData['ends_with_source'] ?? true,
                    'condition' => $effectData['condition'] ?? null,
                    'description' =>
                        $effectData['description'] ?? null,
                    'sort_order' => $effectData['sort_order'] ?? 0,
                    'notes' => $effectData['notes'] ?? null,
                ]
            );

            $this->syncDamages(
                $effect,
                $effectData['damages'] ?? [],
                $abilities,
                $damageTypes,
                $data['key']
            );

            $this->syncHealings(
                $effect,
                $effectData['healings'] ?? [],
                $abilities,
                $data['key']
            );

            $this->syncRollModifiers(
                $effect,
                $effectData['roll_modifiers'] ?? [],
                $abilities,
                $data['key']
            );

            $this->syncForcedMovements(
                $effect,
                $effectData['forced_movements'] ?? [],
                $data['key']
            );

            $this->syncDurations(
                $effect,
                $effectData['durations'] ?? [],
                $data['key']
            );

            $this->syncScalings(
                $effect,
                $effectData['scalings'] ?? [],
                $abilities,
                $data['key']
            );
        }
    }

    //Sincronizza le formule di danno appartenenti a un effetto
    private function syncDamages(
        EffectDefinition $effect,
        array $damages,
        Collection $abilities,
        Collection $damageTypes,
        string $spellKey
    ): void {
        $damageKeys = $this->uniqueKeys(
            $damages,
            "i danni dell'incantesimo {$spellKey}"
        );

        $obsoleteDamages = $effect->damages();

        if ($damageKeys !== []) {
            $obsoleteDamages->whereNotIn('key', $damageKeys);
        }

        $obsoleteDamages->get()->each(function (
            EffectDefinitionDamage $damage
        ): void {
            $damage->delete();
        });

        foreach ($damages as $damageData) {
            $damageTypeName = $damageData['damage_type'];
            $damageTypeId = $damageTypes->get($damageTypeName);

            if ($damageTypeId === null) {
                throw new RuntimeException(
                    "Tipo di danno {$damageTypeName} non trovato per "
                    . "l'incantesimo {$spellKey}."
                );
            }

            $modifierAbilityId = $this->resolveAbilityId(
                $damageData['modifier_ability'] ?? null,
                $abilities,
                $spellKey
            );

            $damage = $effect->damages()->updateOrCreate(
                ['key' => $damageData['key']],
                [
                    'damage_type_id' => $damageTypeId,
                    'dice_count' => $damageData['dice_count'] ?? null,
                    'die_size' => $damageData['die_size'] ?? null,
                    'flat_bonus' => $damageData['flat_bonus'] ?? 0,
                    'modifier_source_type' =>
                        $damageData['modifier_source_type'] ?? 'none',
                    'modifier_ability_id' => $modifierAbilityId,
                    'modifier_multiplier' =>
                        $damageData['modifier_multiplier'] ?? 1,
                    'average_damage' =>
                        $damageData['average_damage'] ?? null,
                    'is_primary' => $damageData['is_primary'] ?? false,
                    'condition' => $damageData['condition'] ?? null,
                    'sort_order' => $damageData['sort_order'] ?? 0,
                    'notes' => $damageData['notes'] ?? null,
                ]
            );

            $this->syncScalings(
                $damage,
                $damageData['scalings'] ?? [],
                $abilities,
                $spellKey
            );
        }
    }

    //Sincronizza le formule di guarigione appartenenti a un effetto
    private function syncHealings(
        EffectDefinition $effect,
        array $healings,
        Collection $abilities,
        string $spellKey
    ): void {
        $healingKeys = $this->uniqueKeys(
            $healings,
            "le guarigioni dell'incantesimo {$spellKey}"
        );

        $obsoleteHealings = $effect->healings();

        if ($healingKeys !== []) {
            $obsoleteHealings->whereNotIn('key', $healingKeys);
        }

        $obsoleteHealings->get()->each(function (
            EffectDefinitionHealing $healing
        ): void {
            $healing->delete();
        });

        foreach ($healings as $healingData) {
            $modifierAbilityId = $this->resolveAbilityId(
                $healingData['modifier_ability'] ?? null,
                $abilities,
                $spellKey
            );

            $healing = $effect->healings()->updateOrCreate(
                ['key' => $healingData['key']],
                [
                    'healing_type' =>
                        $healingData['healing_type'] ?? 'hit_points',
                    'dice_count' => $healingData['dice_count'] ?? null,
                    'die_size' => $healingData['die_size'] ?? null,
                    'flat_bonus' => $healingData['flat_bonus'] ?? 0,
                    'modifier_source_type' =>
                        $healingData['modifier_source_type'] ?? 'none',
                    'modifier_ability_id' => $modifierAbilityId,
                    'modifier_multiplier' =>
                        $healingData['modifier_multiplier'] ?? 1,
                    'average_healing' =>
                        $healingData['average_healing'] ?? null,
                    'temporary_hit_point_rule' =>
                        $healingData['temporary_hit_point_rule'] ?? null,
                    'is_primary' => $healingData['is_primary'] ?? false,
                    'condition' => $healingData['condition'] ?? null,
                    'sort_order' => $healingData['sort_order'] ?? 0,
                    'notes' => $healingData['notes'] ?? null,
                ]
            );

            $this->syncScalings(
                $healing,
                $healingData['scalings'] ?? [],
                $abilities,
                $spellKey
            );
        }
    }

    //Sincronizza modificatori a tiri, prove e tiri salvezza
    private function syncRollModifiers(
        EffectDefinition $effect,
        array $modifiers,
        Collection $abilities,
        string $spellKey
    ): void {
        $sortOrders = collect($modifiers)
            ->map(fn (array $modifier): int =>
                $modifier['sort_order'] ?? 0)
            ->all();

        if (count($sortOrders) !== count(array_unique($sortOrders))) {
            throw new RuntimeException(
                "L'incantesimo {$spellKey} contiene modificatori "
                . 'con lo stesso ordine.'
            );
        }

        $obsoleteModifiers = $effect->rollModifiers();

        if ($sortOrders !== []) {
            $obsoleteModifiers->whereNotIn('sort_order', $sortOrders);
        }

        $obsoleteModifiers->get()->each(function (
            EffectDefinitionRollModifier $modifier
        ): void {
            $modifier->delete();
        });

        foreach ($modifiers as $modifierData) {
            $sortOrder = $modifierData['sort_order'] ?? 0;
            $abilityId = $this->resolveAbilityId(
                $modifierData['ability'] ?? null,
                $abilities,
                $spellKey
            );

            $modifier = $effect->rollModifiers()->updateOrCreate(
                ['sort_order' => $sortOrder],
                [
                    'roll_type' => $modifierData['roll_type'],
                    'ability_id' => $abilityId,
                    'skill_id' => null,
                    'modifier_type' => $modifierData['modifier_type'],
                    'value' => $modifierData['value'] ?? null,
                    'dice_count' =>
                        $modifierData['dice_count'] ?? null,
                    'die_size' => $modifierData['die_size'] ?? null,
                    'condition' => $modifierData['condition'] ?? null,
                    'notes' => $modifierData['notes'] ?? null,
                ]
            );

            //Riusa la tabella polimorfica già presente per le progressioni.
            $this->syncScalings(
                $modifier,
                $modifierData['scalings'] ?? [],
                $abilities,
                $spellKey
            );
        }
    }

    //Sincronizza i movimenti forzati prodotti dall'effetto
    private function syncForcedMovements(
        EffectDefinition $effect,
        array $movements,
        string $spellKey
    ): void {
        $movementKeys = $this->uniqueKeys(
            $movements,
            "i movimenti forzati dell'incantesimo {$spellKey}"
        );

        $obsoleteMovements = $effect->forcedMovements();

        if ($movementKeys !== []) {
            $obsoleteMovements->whereNotIn('key', $movementKeys);
        }

        $obsoleteMovements->delete();

        foreach ($movements as $movementData) {
            $effect->forcedMovements()->updateOrCreate(
                ['key' => $movementData['key']],
                [
                    'movement_type' => $movementData['movement_type'],
                    'origin_type' =>
                        $movementData['origin_type'] ?? 'source',
                    'direction_type' => $movementData['direction_type'],
                    'distance' => $movementData['distance'] ?? null,
                    'up_to_distance' =>
                        $movementData['up_to_distance'] ?? false,
                    'straight_line' =>
                        $movementData['straight_line'] ?? true,
                    'stops_at_obstacle' =>
                        $movementData['stops_at_obstacle'] ?? true,
                    'opportunity_attack_rule' =>
                        $movementData['opportunity_attack_rule']
                            ?? 'default',
                    'condition' => $movementData['condition'] ?? null,
                    'sort_order' => $movementData['sort_order'] ?? 0,
                    'notes' => $movementData['notes'] ?? null,
                ]
            );
        }
    }

    //Sincronizza le regole che determinano la durata dell'effetto
    private function syncDurations(
        EffectDefinition $effect,
        array $durations,
        string $spellKey
    ): void {
        $durationKeys = $this->uniqueKeys(
            $durations,
            "le durate dell'incantesimo {$spellKey}"
        );

        $obsoleteDurations = $effect->durations();

        if ($durationKeys !== []) {
            $obsoleteDurations->whereNotIn('key', $durationKeys);
        }

        $obsoleteDurations->delete();

        foreach ($durations as $durationData) {
            $effect->durations()->updateOrCreate(
                ['key' => $durationData['key']],
                [
                    'duration_type' => $durationData['duration_type'],
                    'duration_value' =>
                        $durationData['duration_value'] ?? null,
                    'duration_unit' =>
                        $durationData['duration_unit'] ?? null,
                    'turn_reference' =>
                        $durationData['turn_reference'] ?? null,
                    'condition' => $durationData['condition'] ?? null,
                    'sort_order' => $durationData['sort_order'] ?? 0,
                    'notes' => $durationData['notes'] ?? null,
                ]
            );
        }
    }

    //Sincronizza una progressione matematica polimorfica
    private function syncScalings(
        EffectDefinition|EffectDefinitionDamage|EffectDefinitionHealing|EffectDefinitionRollModifier $scalable,
        array $scalings,
        Collection $abilities,
        string $spellKey
    ): void {
        $scalingKeys = $this->uniqueKeys(
            $scalings,
            "le progressioni dell'incantesimo {$spellKey}"
        );

        $obsoleteScalings = $scalable->scalings();

        if ($scalingKeys !== []) {
            $obsoleteScalings->whereNotIn('key', $scalingKeys);
        }

        $obsoleteScalings->delete();

        foreach ($scalings as $scalingData) {
            $abilityId = $this->resolveAbilityId(
                $scalingData['ability'] ?? null,
                $abilities,
                $spellKey
            );

            $scalable->scalings()->updateOrCreate(
                ['key' => $scalingData['key']],
                [
                    'target_field' => $scalingData['target_field'],
                    'source_type' => $scalingData['source_type'],
                    'class_id' => null,
                    'ability_id' => $abilityId,
                    'operation' => $scalingData['operation'] ?? 'add',
                    'minimum_source' =>
                        $scalingData['minimum_source'] ?? null,
                    'maximum_source' =>
                        $scalingData['maximum_source'] ?? null,
                    'fixed_value' =>
                        $scalingData['fixed_value'] ?? null,
                    'source_offset' =>
                        $scalingData['source_offset'] ?? 0,
                    'multiplier' => $scalingData['multiplier'] ?? 1,
                    'divisor' => $scalingData['divisor'] ?? 1,
                    'flat_value' => $scalingData['flat_value'] ?? 0,
                    'rounding' => $scalingData['rounding'] ?? 'none',
                    'minimum_result' =>
                        $scalingData['minimum_result'] ?? null,
                    'maximum_result' =>
                        $scalingData['maximum_result'] ?? null,
                    'condition' => $scalingData['condition'] ?? null,
                    'sort_order' => $scalingData['sort_order'] ?? 0,
                    'notes' => $scalingData['notes'] ?? null,
                ]
            );
        }
    }

    //Sincronizza le evocazioni e gli stat block dichiarati nel file dati
    private function syncSummons(
        Spell $spell,
        array $data,
        Collection $abilities,
        Collection $damageTypes,
        Collection $creatureTypes,
        Collection $sizes,
        Collection $movementTypes
    ): void {
        //I cataloghi precedenti rimangono invariati se non dichiarano
        //esplicitamente la sezione summons
        if (! array_key_exists('summons', $data)) {
            return;
        }

        $summons = $data['summons'];
        $summonNames = $this->uniqueNames(
            $summons,
            "le evocazioni dell'incantesimo {$data['key']}"
        );

        //Elimina attraverso i modelli le evocazioni non più presenti,
        //così vengono puliti anche gli stat block dedicati
        $obsoleteSummons = $spell->summons();

        if ($summonNames !== []) {
            $obsoleteSummons->whereNotIn('name', $summonNames);
        }

        $obsoleteSummons->get()->each(function (
            SpellSummon $summon
        ): void {
            $summon->delete();
        });

        foreach ($summons as $summonData) {
            $summon = $spell->summons()->updateOrCreate(
                ['name' => $summonData['name']],
                [
                    'selection_type' =>
                        $summonData['selection_type'] ?? 'special',
                    'quantity_type' =>
                        $summonData['quantity_type'] ?? 'exact',
                    'quantity' => $summonData['quantity'] ?? 1,
                    'min_challenge_rating' =>
                        $summonData['min_challenge_rating'] ?? null,
                    'max_challenge_rating' =>
                        $summonData['max_challenge_rating'] ?? null,
                    'controlled_by_caster' =>
                        $summonData['controlled_by_caster'] ?? true,
                    'friendly_to_caster' =>
                        $summonData['friendly_to_caster'] ?? true,
                    'ends_with_spell' =>
                        $summonData['ends_with_spell'] ?? true,
                    'selection_condition' =>
                        $summonData['selection_condition'] ?? null,
                    'control_rules' =>
                        $summonData['control_rules'] ?? null,
                    'sort_order' => $summonData['sort_order'] ?? 0,
                    'notes' => $summonData['notes'] ?? null,
                ]
            );

            $this->syncSummonTemplates(
                $summon,
                $summonData['templates'] ?? [],
                $abilities,
                $damageTypes,
                $creatureTypes,
                $sizes,
                $movementTypes,
                $data['key']
            );
        }
    }

    //Sincronizza i template appartenenti a una evocazione
    private function syncSummonTemplates(
        SpellSummon $summon,
        array $templates,
        Collection $abilities,
        Collection $damageTypes,
        Collection $creatureTypes,
        Collection $sizes,
        Collection $movementTypes,
        string $spellKey
    ): void {
        $templateNames = $this->uniqueNames(
            $templates,
            "i template evocati dell'incantesimo {$spellKey}"
        );

        $obsoleteTemplates = $summon->templates();

        if ($templateNames !== []) {
            $obsoleteTemplates->whereNotIn('name', $templateNames);
        }

        $obsoleteTemplates->get()->each(function (
            SpellSummonTemplate $template
        ): void {
            $template->delete();
        });

        foreach ($templates as $templateData) {
            $creatureTypeId = $creatureTypes->get(
                $templateData['creature_type_key']
            );
            $sizeId = $sizes->get($templateData['size_name']);

            if ($creatureTypeId === null) {
                throw new RuntimeException(
                    "Tipo di creatura {$templateData['creature_type_key']} "
                    . "non trovato per l'incantesimo {$spellKey}."
                );
            }

            if ($sizeId === null) {
                throw new RuntimeException(
                    "Taglia {$templateData['size_name']} non trovata per "
                    . "l'incantesimo {$spellKey}."
                );
            }

            $template = $summon->templates()->updateOrCreate(
                ['name' => $templateData['name']],
                [
                    'creature_type_id' => $creatureTypeId,
                    'size_id' => $sizeId,
                    'description' =>
                        $templateData['description'] ?? null,
                    'sort_order' => $templateData['sort_order'] ?? 0,
                    'notes' => $templateData['notes'] ?? null,
                ]
            );

            $this->syncSummonForms(
                $template,
                $templateData['forms'] ?? [],
                $templateData,
                $abilities,
                $damageTypes,
                $creatureTypes,
                $sizes,
                $movementTypes,
                $spellKey
            );
        }
    }

    //Sincronizza le forme alternative di uno stat block evocato
    private function syncSummonForms(
        SpellSummonTemplate $template,
        array $forms,
        array $templateData,
        Collection $abilities,
        Collection $damageTypes,
        Collection $creatureTypes,
        Collection $sizes,
        Collection $movementTypes,
        string $spellKey
    ): void {
        $formNames = $this->uniqueNames(
            $forms,
            "le forme evocate dell'incantesimo {$spellKey}"
        );

        $obsoleteForms = $template->forms();

        if ($formNames !== []) {
            $obsoleteForms->whereNotIn('name', $formNames);
        }

        $obsoleteForms->get()->each(function (
            SpellSummonTemplateForm $form
        ): void {
            $form->delete();
        });

        foreach ($forms as $formData) {
            $form = $template->forms()
                ->where('name', $formData['name'])
                ->first();

            //Ogni forma mantiene uno stat block indipendente, perché
            //movimenti, punti ferita e azioni possono cambiare
            $statBlock = $form?->creatureStatBlock;

            if ($statBlock === null) {
                $statBlock = CreatureStatBlock::query()->create([
                    'name' => $formData['stat_block']['name'],
                    'alignment_mode' => 'unaligned',
                ]);
            }

            $this->syncSummonedStatBlock(
                $statBlock,
                $formData['stat_block'],
                $templateData,
                $abilities,
                $damageTypes,
                $creatureTypes,
                $sizes,
                $movementTypes,
                $spellKey
            );

            $form = $template->forms()->updateOrCreate(
                ['name' => $formData['name']],
                [
                    'creature_stat_block_id' => $statBlock->id,
                    'description' => $formData['description'] ?? null,
                    'is_default' => $formData['is_default'] ?? false,
                    'sort_order' => $formData['sort_order'] ?? 0,
                    'notes' => $formData['notes'] ?? null,
                ]
            );

            $this->syncSummonTemplateScalings(
                $form,
                $formData['scalings'] ?? [],
                $statBlock,
                $abilities,
                $spellKey
            );
        }
    }

    //Sincronizza i dati principali e le azioni dello stat block evocato
    private function syncSummonedStatBlock(
        CreatureStatBlock $statBlock,
        array $statData,
        array $templateData,
        Collection $abilities,
        Collection $damageTypes,
        Collection $creatureTypes,
        Collection $sizes,
        Collection $movementTypes,
        string $spellKey
    ): void {
        $creatureTypeKey = $statData['creature_type_key']
            ?? $templateData['creature_type_key'];
        $sizeName = $statData['size_name']
            ?? $templateData['size_name'];
        $creatureTypeId = $creatureTypes->get($creatureTypeKey);
        $sizeId = $sizes->get($sizeName);

        if ($creatureTypeId === null || $sizeId === null) {
            throw new RuntimeException(
                "Tipo o taglia dello stat block non trovati per "
                . "l'incantesimo {$spellKey}."
            );
        }

        $statBlock->update([
            'name' => $statData['name'],
            'creature_type_id' => $creatureTypeId,
            'size_id' => $sizeId,
            'challenge_rating_id' => null,
            'experience_points_override' => null,
            'proficiency_bonus_override' => null,
            'alignment' => null,
            'alignment_mode' => 'unaligned',
            'description' => $statData['description'] ?? null,
            'notes' => $statData['notes'] ?? null,
            'is_swarm' => false,
            'swarm_component_size_id' => null,
        ]);

        //Punteggi delle sei caratteristiche
        $abilityIds = [];

        foreach ($statData['abilities'] ?? [] as $shortName => $score) {
            $abilityId = $this->resolveAbilityId(
                $shortName,
                $abilities,
                $spellKey
            );
            $abilityIds[] = $abilityId;

            $statBlock->abilityScores()->updateOrCreate(
                ['ability_id' => $abilityId],
                [
                    'score' => $score,
                    'notes' => null,
                ]
            );
        }

        $obsoleteAbilities = $statBlock->abilityScores();

        if ($abilityIds !== []) {
            $obsoleteAbilities->whereNotIn('ability_id', $abilityIds);
        }

        $obsoleteAbilities->delete();

        //Classe Armatura base, completata dalle regole di scaling
        $armorData = $statData['armor_class'];
        $statBlock->armorClasses()
            ->where('sort_order', '!=', 1)
            ->delete();
        $statBlock->armorClasses()->updateOrCreate(
            ['sort_order' => 1],
            [
                'armor_class' => $armorData['value'],
                'armor_class_type' =>
                    $armorData['type'] ?? 'natural_armor',
                'is_default' => true,
                'description' => $armorData['description'] ?? null,
                'condition' => $armorData['condition'] ?? null,
                'notes' => $armorData['notes'] ?? null,
            ]
        );

        //Punti Ferita base, completati dalle regole di scaling
        $hitPointData = $statData['hit_points'];
        $statBlock->hitPoints()->updateOrCreate(
            [],
            [
                'average_hit_points' =>
                    $hitPointData['average_hit_points'] ?? null,
                'hit_dice_count' => null,
                'hit_die_size' => null,
                'hit_dice_modifier' => 0,
                'special_calculation' =>
                    $hitPointData['special_calculation'] ?? null,
                'notes' => $hitPointData['notes'] ?? null,
            ]
        );

        $this->syncSummonedMovements(
            $statBlock,
            $statData['movements'] ?? [],
            $movementTypes,
            $spellKey
        );

        $this->syncSummonedActions(
            $statBlock,
            $statData['actions'] ?? [],
            $abilities,
            $damageTypes,
            $spellKey
        );
    }

    //Sincronizza le velocità dello stat block evocato
    private function syncSummonedMovements(
        CreatureStatBlock $statBlock,
        array $movements,
        Collection $movementTypes,
        string $spellKey
    ): void {
        $movementTypeIds = [];

        foreach ($movements as $movementData) {
            $movementTypeId = $movementTypes->get(
                $movementData['type']
            );

            if ($movementTypeId === null) {
                throw new RuntimeException(
                    "Movimento {$movementData['type']} non trovato per "
                    . "l'incantesimo {$spellKey}."
                );
            }

            $movementTypeIds[] = $movementTypeId;
            $statBlock->movements()->updateOrCreate(
                ['movement_type_id' => $movementTypeId],
                [
                    'speed' => $movementData['speed'],
                    'can_hover' =>
                        $movementData['can_hover'] ?? false,
                    'condition' =>
                        $movementData['condition'] ?? null,
                    'notes' => $movementData['notes'] ?? null,
                ]
            );
        }

        $obsoleteMovements = $statBlock->movements();

        if ($movementTypeIds !== []) {
            $obsoleteMovements->whereNotIn(
                'movement_type_id',
                $movementTypeIds
            );
        }

        $obsoleteMovements->delete();
    }

    //Sincronizza azioni, attacchi, danni e tiri salvezza dello stat block
    private function syncSummonedActions(
        CreatureStatBlock $statBlock,
        array $actions,
        Collection $abilities,
        Collection $damageTypes,
        string $spellKey
    ): void {
        $actionKeys = $this->uniqueKeys(
            $actions,
            "le azioni evocate dell'incantesimo {$spellKey}"
        );
        $obsoleteActions = $statBlock->actions();

        if ($actionKeys !== []) {
            $obsoleteActions->whereNotIn('key', $actionKeys);
        }

        $obsoleteActions->delete();

        foreach ($actions as $actionData) {
            $action = $statBlock->actions()->updateOrCreate(
                ['key' => $actionData['key']],
                [
                    'name' => $actionData['name'],
                    'action_type' =>
                        $actionData['action_type'] ?? 'action',
                    'description' =>
                        $actionData['description'] ?? null,
                    'trigger' => $actionData['trigger'] ?? null,
                    'max_uses' => $actionData['max_uses'] ?? null,
                    'recharge_type' =>
                        $actionData['recharge_type'] ?? 'at_will',
                    'recharge_min' =>
                        $actionData['recharge_min'] ?? null,
                    'recharge_max' =>
                        $actionData['recharge_max'] ?? null,
                    'legendary_action_cost' =>
                        $actionData['legendary_action_cost'] ?? null,
                    'condition' => $actionData['condition'] ?? null,
                    'sort_order' => $actionData['sort_order'] ?? 0,
                    'notes' => $actionData['notes'] ?? null,
                ]
            );

            $this->syncSummonedAttacks(
                $action,
                $actionData['attacks'] ?? [],
                $abilities,
                $spellKey
            );

            $this->syncSummonedActionDamages(
                $action,
                $actionData['damages'] ?? [],
                $damageTypes,
                $spellKey
            );

            $this->syncSummonedSavingThrows(
                $action,
                $actionData['saving_throws'] ?? [],
                $abilities,
                $spellKey
            );
        }
    }

    //Sincronizza gli attacchi appartenenti a una azione evocata
    private function syncSummonedAttacks(
        CreatureStatBlockAction $action,
        array $attacks,
        Collection $abilities,
        string $spellKey
    ): void {
        $attackKeys = $this->uniqueKeys(
            $attacks,
            "gli attacchi evocati dell'incantesimo {$spellKey}"
        );
        $obsoleteAttacks = $action->attacks();

        if ($attackKeys !== []) {
            $obsoleteAttacks->whereNotIn('key', $attackKeys);
        }

        $obsoleteAttacks->delete();

        foreach ($attacks as $attackData) {
            $abilityId = $this->resolveAbilityId(
                $attackData['attack_ability'] ?? null,
                $abilities,
                $spellKey
            );

            $action->attacks()->updateOrCreate(
                ['key' => $attackData['key']],
                [
                    'name' => $attackData['name'],
                    'attack_type' => $attackData['attack_type'],
                    'attack_kind' =>
                        $attackData['attack_kind'] ?? 'weapon',
                    'attack_bonus' =>
                        $attackData['attack_bonus'] ?? null,
                    'attack_ability_id' => $abilityId,
                    'reach' => $attackData['reach'] ?? null,
                    'range' => $attackData['range'] ?? null,
                    'long_range' =>
                        $attackData['long_range'] ?? null,
                    'target_count' =>
                        $attackData['target_count'] ?? 1,
                    'condition' => $attackData['condition'] ?? null,
                    'notes' => $attackData['notes'] ?? null,
                ]
            );
        }
    }

    //Sincronizza le formule di danno delle azioni evocate
    private function syncSummonedActionDamages(
        CreatureStatBlockAction $action,
        array $damages,
        Collection $damageTypes,
        string $spellKey
    ): void {
        $sortOrders = collect($damages)
            ->map(fn (array $damage): int =>
                $damage['sort_order'] ?? 0)
            ->all();

        if (count($sortOrders) !== count(array_unique($sortOrders))) {
            throw new RuntimeException(
                "L'incantesimo {$spellKey} contiene danni evocati "
                . 'con lo stesso ordine.'
            );
        }

        $obsoleteDamages = $action->damages();

        if ($sortOrders !== []) {
            $obsoleteDamages->whereNotIn('sort_order', $sortOrders);
        }

        $obsoleteDamages->delete();

        foreach ($damages as $damageData) {
            $damageTypeId = $damageTypes->get(
                $damageData['damage_type']
            );

            if ($damageTypeId === null) {
                throw new RuntimeException(
                    "Tipo di danno {$damageData['damage_type']} non "
                    . "trovato per l'incantesimo {$spellKey}."
                );
            }

            $attackId = null;
            $attackKey = $damageData['attack_key'] ?? null;

            if ($attackKey !== null) {
                $attackId = $action->attacks()
                    ->where('key', $attackKey)
                    ->firstOrFail()
                    ->id;
            }

            $sortOrder = $damageData['sort_order'] ?? 0;
            $action->damages()->updateOrCreate(
                ['sort_order' => $sortOrder],
                [
                    'creature_stat_block_attack_id' => $attackId,
                    'damage_type_id' => $damageTypeId,
                    'dice_count' => $damageData['dice_count'] ?? null,
                    'die_size' => $damageData['die_size'] ?? null,
                    'bonus' => $damageData['bonus'] ?? 0,
                    'average_damage' =>
                        $damageData['average_damage'] ?? null,
                    'is_primary' =>
                        $damageData['is_primary'] ?? false,
                    'condition' => $damageData['condition'] ?? null,
                    'notes' => $damageData['notes'] ?? null,
                ]
            );
        }
    }

    //Sincronizza i tiri salvezza richiesti dalle azioni evocate
    private function syncSummonedSavingThrows(
        CreatureStatBlockAction $action,
        array $savingThrows,
        Collection $abilities,
        string $spellKey
    ): void {
        $savingThrowKeys = $this->uniqueKeys(
            $savingThrows,
            "i tiri salvezza evocati dell'incantesimo {$spellKey}"
        );
        $obsoleteSavingThrows = $action->savingThrows();

        if ($savingThrowKeys !== []) {
            $obsoleteSavingThrows->whereNotIn(
                'key',
                $savingThrowKeys
            );
        }

        $obsoleteSavingThrows->delete();

        foreach ($savingThrows as $savingThrowData) {
            $abilityId = $this->resolveAbilityId(
                $savingThrowData['ability'],
                $abilities,
                $spellKey
            );

            $action->savingThrows()->updateOrCreate(
                ['key' => $savingThrowData['key']],
                [
                    'ability_id' => $abilityId,
                    'save_dc' => $savingThrowData['save_dc'] ?? null,
                    'success_type' =>
                        $savingThrowData['success_type'] ?? 'no_effect',
                    'failure_description' =>
                        $savingThrowData['failure_description'] ?? null,
                    'success_description' =>
                        $savingThrowData['success_description'] ?? null,
                    'condition' =>
                        $savingThrowData['condition'] ?? null,
                    'sort_order' =>
                        $savingThrowData['sort_order'] ?? 0,
                    'notes' => $savingThrowData['notes'] ?? null,
                ]
            );
        }
    }

    //Sincronizza le progressioni dello stat block evocato
    private function syncSummonTemplateScalings(
        SpellSummonTemplateForm $form,
        array $scalings,
        CreatureStatBlock $statBlock,
        Collection $abilities,
        string $spellKey
    ): void {
        $scalingKeys = $this->uniqueKeys(
            $scalings,
            "le progressioni evocate dell'incantesimo {$spellKey}"
        );
        $obsoleteScalings = $form->scalings();

        if ($scalingKeys !== []) {
            $obsoleteScalings->whereNotIn('key', $scalingKeys);
        }

        $obsoleteScalings->delete();

        foreach ($scalings as $scalingData) {
            $abilityId = $this->resolveAbilityId(
                $scalingData['source_ability'] ?? null,
                $abilities,
                $spellKey
            );
            $targetId = $this->resolveSummonScalingTargetId(
                $statBlock,
                $scalingData['target_type'],
                $scalingData['target_ref'] ?? null,
                $spellKey
            );

            $form->scalings()->updateOrCreate(
                ['key' => $scalingData['key']],
                [
                    'target_type' => $scalingData['target_type'],
                    'target_id' => $targetId,
                    'source_type' => $scalingData['source_type'],
                    'source_ability_id' => $abilityId,
                    'operation' => $scalingData['operation'] ?? 'add',
                    'source_offset' =>
                        $scalingData['source_offset'] ?? 0,
                    'multiplier' => $scalingData['multiplier'] ?? 1,
                    'divisor' => $scalingData['divisor'] ?? 1,
                    'flat_value' => $scalingData['flat_value'] ?? 0,
                    'rounding' => $scalingData['rounding'] ?? 'none',
                    'minimum_source' =>
                        $scalingData['minimum_source'] ?? null,
                    'maximum_source' =>
                        $scalingData['maximum_source'] ?? null,
                    'minimum_result' =>
                        $scalingData['minimum_result'] ?? null,
                    'maximum_result' =>
                        $scalingData['maximum_result'] ?? null,
                    'condition' => $scalingData['condition'] ?? null,
                    'sort_order' => $scalingData['sort_order'] ?? 0,
                    'notes' => $scalingData['notes'] ?? null,
                ]
            );
        }
    }

    //Recupera l'elemento dello stat block modificato da una progressione
    private function resolveSummonScalingTargetId(
        CreatureStatBlock $statBlock,
        string $targetType,
        ?string $targetReference,
        string $spellKey
    ): ?int {
        if ($targetType === 'armor_class') {
            return $statBlock->defaultArmorClass()->firstOrFail()->id;
        }

        if ($targetType === 'hit_points') {
            return $statBlock->hitPoints()->firstOrFail()->id;
        }

        if ($targetType === 'movement_speed') {
            $movementTypeId = MovementType::query()
                ->where('name', $targetReference)
                ->value('id');

            return $statBlock->movements()
                ->where('movement_type_id', $movementTypeId)
                ->firstOrFail()
                ->id;
        }

        if ($targetType === 'attack_count') {
            return $statBlock->actions()
                ->where('key', $targetReference)
                ->firstOrFail()
                ->id;
        }

        if ($targetType === 'attack_bonus') {
            [$actionKey, $attackKey] = $this->splitTargetReference(
                $targetReference,
                $spellKey
            );

            return $statBlock->actions()
                ->where('key', $actionKey)
                ->firstOrFail()
                ->attacks()
                ->where('key', $attackKey)
                ->firstOrFail()
                ->id;
        }

        if (
            $targetType === 'damage_bonus'
            || $targetType === 'damage_dice_count'
        ) {
            [$actionKey, $sortOrder] = $this->splitTargetReference(
                $targetReference,
                $spellKey
            );

            return $statBlock->actions()
                ->where('key', $actionKey)
                ->firstOrFail()
                ->damages()
                ->where('sort_order', (int) $sortOrder)
                ->firstOrFail()
                ->id;
        }

        if ($targetType === 'save_dc') {
            [$actionKey, $savingThrowKey] =
                $this->splitTargetReference(
                    $targetReference,
                    $spellKey
                );

            return $statBlock->actions()
                ->where('key', $actionKey)
                ->firstOrFail()
                ->savingThrows()
                ->where('key', $savingThrowKey)
                ->firstOrFail()
                ->id;
        }

        //Le regole speciali possono essere descrittive e non puntare
        //a una singola riga dello stat block
        if ($targetType === 'other') {
            return null;
        }

        throw new RuntimeException(
            "Bersaglio {$targetType} non supportato per la "
            . "progressione dell'incantesimo {$spellKey}."
        );
    }

    //Divide un riferimento nel formato azione:elemento
    private function splitTargetReference(
        ?string $reference,
        string $spellKey
    ): array {
        if ($reference === null || ! str_contains($reference, ':')) {
            throw new RuntimeException(
                "Riferimento di progressione non valido per "
                . "l'incantesimo {$spellKey}."
            );
        }

        return explode(':', $reference, 2);
    }

    //Recupera una caratteristica facoltativa tramite abbreviazione
    private function resolveAbilityId(
        ?string $shortName,
        Collection $abilities,
        string $spellKey
    ): ?int {
        if ($shortName === null) {
            return null;
        }

        $abilityId = $abilities->get($shortName);

        if ($abilityId === null) {
            throw new RuntimeException(
                "Caratteristica {$shortName} non trovata per "
                . "l'incantesimo {$spellKey}."
            );
        }

        return (int) $abilityId;
    }

    //Controlla la presenza e l'unicità delle chiavi tecniche
    private function uniqueKeys(array $items, string $context): array
    {
        $keys = [];

        foreach ($items as $item) {
            $key = $item['key'] ?? null;

            if (! is_string($key) || $key === '') {
                throw new RuntimeException(
                    "Manca una chiave tecnica valida per {$context}."
                );
            }

            $keys[] = $key;
        }

        if (count($keys) !== count(array_unique($keys))) {
            throw new RuntimeException(
                "Sono presenti chiavi tecniche duplicate per {$context}."
            );
        }

        return $keys;
    }

    //Controlla presenza e unicità dei nomi usati come identità locale
    private function uniqueNames(array $items, string $context): array
    {
        $names = [];

        foreach ($items as $item) {
            $name = $item['name'] ?? null;

            if (! is_string($name) || trim($name) === '') {
                throw new RuntimeException(
                    "Manca un nome valido per {$context}."
                );
            }

            $names[] = $name;
        }

        if (count($names) !== count(array_unique($names))) {
            throw new RuntimeException(
                "Sono presenti nomi duplicati per {$context}."
            );
        }

        return $names;
    }
}
