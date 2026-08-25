<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Currency;
use App\Models\DamageType;
use App\Models\EffectDefinition;
use App\Models\EffectDefinitionDamage;
use App\Models\EffectDefinitionHealing;
use App\Models\Ruleset;
use App\Models\SourceBook;
use App\Models\Spell;
use App\Models\SpellSchool;
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
                    $damageTypes
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
        Collection $damageTypes
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

        $obsoleteModifiers->delete();

        foreach ($modifiers as $modifierData) {
            $sortOrder = $modifierData['sort_order'] ?? 0;
            $abilityId = $this->resolveAbilityId(
                $modifierData['ability'] ?? null,
                $abilities,
                $spellKey
            );

            $effect->rollModifiers()->updateOrCreate(
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
        EffectDefinition|EffectDefinitionDamage|EffectDefinitionHealing $scalable,
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
}
