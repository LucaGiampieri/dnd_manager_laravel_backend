<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Ruleset;
use App\Models\SourceBook;
use App\Models\Spell;
use App\Models\SpellSchool;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class PlayerHandbookSpellSeeder extends Seeder
{
    //Inserisce gli incantesimi del Manuale del Giocatore 2014
    public function run(): void
    {
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

        //Recupera il Manuale del Giocatore italiano
        $sourceBook = SourceBook::query()
            ->where('slug', 'phb-2014')
            ->firstOrFail();

        //Indicizza le scuole tramite la loro chiave tecnica
        $schools = SpellSchool::query()
            ->pluck('id', 'key');

        //Indicizza le caratteristiche tramite l'abbreviazione italiana
        $abilities = Ability::query()
            ->pluck('id', 'short_name');

        //Indicizza le valute tramite il loro codice italiano
        $currencies = Currency::query()
            ->pluck('id', 'code');

        //Raggruppa tutti i cataloghi del PHB 2014
        $spellGroups = [
            //Carica i trucchetti
            require database_path(
                'data/phb_2014_cantrips.php'
            ),

            //Carica gli incantesimi di 1° livello
            require database_path(
                'data/phb_2014_level_1_spells.php'
            ),

            //Carica gli incantesimi di 2° livello
            require database_path(
                'data/phb_2014_level_2_spells.php'
            ),

            //Carica gli incantesimi di 3° livello
            require database_path(
                'data/phb_2014_level_3_spells.php'
            ),

            //Carica tutti gli incantesimi di 4° livello del PHB 2014
            require database_path(
                'data/phb_2014_level_4_spells.php'
            ),

            //Carica tutti gli incantesimi di 5° livello del PHB 2014
            require database_path(
                'data/phb_2014_level_5_spells.php'
            ),
        ];

        //Inserisce ogni gruppo di incantesimi
        foreach ($spellGroups as $spellData) {
            foreach ($spellData as $data) {
                $schoolId = $schools->get($data['school_key']);

                //Interrompe il seeding se manca una scuola richiesta
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

                //Crea o aggiorna la versione PHB dell'incantesimo
                $spell = Spell::query()->updateOrCreate(
                    [
                        'ruleset_id' => $ruleset->id,
                        'key' => $data['key'],
                    ],
                    [
                        'canonical_key' => $data['key'],
                        'version_key' => 'phb_2014',
                        'is_legacy' => false,
                        'name' => $data['name'],
                        'level' => $data['level'],
                        'spell_school_id' => $schoolId,
                        'casting_time_value' =>
                            $data['casting_time_value'],
                        'casting_time_type' =>
                            $data['casting_time_type'],
                        'casting_trigger' =>
                            $data['casting_trigger'],
                        'range_type' => $data['range_type'],
                        'range' => $data['range'],
                        'verbal_component' =>
                            $data['verbal_component'],
                        'somatic_component' =>
                            $data['somatic_component'],
                        'material_component' =>
                            $data['material_component'],
                        'material_description' =>
                            $data['material_description'],
                        'material_consumed' =>
                            $data['material_consumed'],
                        'material_cost' =>
                            $data['material_cost'],
                        'duration_type' =>
                            $data['duration_type'],
                        'duration_value' =>
                            $data['duration_value'],
                        'concentration' =>
                            $data['concentration'],
                        'ritual' => $data['ritual'],
                        'attack_type' =>
                            $data['attack_type'],
                        'saving_throw_ability_id' =>
                            $savingThrowAbilityId,
                        'save_success_damage' =>
                            $data['save_success_damage'],
                        'description' =>
                            $data['description'],
                        'higher_levels' =>
                            $data['higher_levels'],
                        'notes' => $data['notes'],
                    ]
                );

                //Crea o aggiorna il profilo del bersaglio
                $spell->targetProfile()->updateOrCreate(
                    [],
                    $data['target']
                );

                //Crea o aggiorna i componenti materiali dettagliati
                $this->syncMaterialComponents(
                    $spell,
                    $data,
                    $currencies
                );

                //Collega l'incantesimo alla sua pagina del PHB
                $spell->sourceReferences()->updateOrCreate(
                    [
                        'source_book_id' => $sourceBook->id,
                        'reference_type' => 'definition',
                    ],
                    [
                        'key' => 'phb_2014_it_spell_definition',
                        'page_start' => $data['page'],
                        'page_end' => $data['page'],
                        'section' => 'Capitolo 11: Incantesimi',
                        'is_primary' => true,
                        'sort_order' => 10,
                        'official_text' => null,
                        'notes' => 'Riferimento bibliografico alla '
                            . 'versione italiana del Manuale '
                            . 'del Giocatore 2014.',
                    ]
                );
            }
        }
    }

    //Sincronizza tutti i componenti materiali di un incantesimo
    private function syncMaterialComponents(
        Spell $spell,
        array $data,
        Collection $currencies
    ): void {
        $materials = $data['materials']
            ?? $this->buildSummaryMaterial($data);

        //Una formula senza componente materiale non può avere dettagli
        if (! $data['material_component'] && $materials !== []) {
            throw new RuntimeException(
                "L'incantesimo {$data['key']} possiede dettagli materiali "
                . 'ma non usa la componente M.'
            );
        }

        $materialKeys = collect($materials)
            ->pluck('key')
            ->all();

        //Rifiuta chiavi duplicate prima di raggiungere il database
        if (count($materialKeys) !== count(array_unique($materialKeys))) {
            throw new RuntimeException(
                "L'incantesimo {$data['key']} contiene componenti "
                . 'materiali duplicati.'
            );
        }

        //Elimina dettagli rimossi mantenendo l'idempotenza
        if ($materialKeys === []) {
            $spell->materialComponents()->delete();
        } else {
            $spell->materialComponents()
                ->whereNotIn('key', $materialKeys)
                ->delete();
        }

        //Crea o aggiorna ogni requisito materiale
        foreach ($materials as $material) {
            $currencyCode = $material['currency_code'] ?? null;
            $currencyId = null;

            //Recupera la valuta del componente costoso
            if ($currencyCode !== null) {
                $currencyId = $currencies->get($currencyCode);

                if ($currencyId === null) {
                    throw new RuntimeException(
                        "Valuta {$currencyCode} non trovata per "
                        . "l'incantesimo {$data['key']}."
                    );
                }
            }

            //Crea o aggiorna il componente tramite la chiave stabile
            $spell->materialComponents()->updateOrCreate(
                [
                    'key' => $material['key'],
                ],
                [
                    'name' => $material['name'],
                    'description' =>
                        $material['description'] ?? null,
                    'quantity' =>
                        $material['quantity'] ?? null,
                    'unit' =>
                        $material['unit'] ?? null,
                    'cost_amount' =>
                        $material['cost_amount'] ?? null,
                    'currency_id' => $currencyId,
                    'cost_is_minimum' =>
                        $material['cost_is_minimum'] ?? false,
                    'consumed' =>
                        $material['consumed'] ?? false,
                    'focus_replaceable' =>
                        $material['focus_replaceable'] ?? true,
                    'sort_order' =>
                        $material['sort_order'] ?? 0,
                    'notes' =>
                        $material['notes'] ?? null,
                ]
            );
        }
    }

    //Converte i vecchi campi riassuntivi in un requisito dettagliato
    private function buildSummaryMaterial(array $data): array
    {
        if (! $data['material_component']) {
            return [];
        }

        $hasCost = $data['material_cost'] !== null;
        $isConsumed = $data['material_consumed'];
        $description = $data['material_description'];

        //Ogni descrizione diventa un requisito materiale ordinato
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
