<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Currency;
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
                    $currencies
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
        Collection $currencies
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
}
