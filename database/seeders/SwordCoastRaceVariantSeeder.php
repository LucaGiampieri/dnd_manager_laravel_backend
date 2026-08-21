<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Feature;
use App\Models\Race;
use App\Models\RaceChoice;
use App\Models\Ruleset;
use App\Models\Subrace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class SwordCoastRaceVariantSeeder extends Seeder
{
    //Identifica la versione delle varianti SCAG
    private const VERSION_KEY = 'scag_2015';

    //Crea le varianti razziali opzionali dello SCAG
    public function run(): void
    {
        //Crea prima tutti i cataloghi necessari
        $this->call([
            AbilitySeeder::class,
            SwordCoastRaceAbilityBonusSeeder::class,
            SwordCoastRaceFeatureSeeder::class,
        ]);

        //Recupera il regolamento di riferimento
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Crea le capacità utilizzabili come alternative
        $features = $this->seedVariantFeatures($ruleset);

        //Crea il Tiefling Ferino e i suoi bonus
        $this->seedFeralTiefling();

        //Crea le alternative del Mezzelfo
        $this->seedHalfElfVariants($features);

        //Crea le alternative del Tiefling
        $this->seedTieflingVariants($features);
    }

    //Crea il Tiefling Ferino come variante del Tiefling
    private function seedFeralTiefling(): void
    {
        //Recupera la razza principale
        $tiefling = Race::query()
            ->where('key', 'tiefling')
            ->firstOrFail();

        //Crea o aggiorna la variante
        $feralTiefling = $tiefling->subraces()->updateOrCreate(
            [
                'key' => 'feral_tiefling_scag_2015',
            ],
            [
                'canonical_key' => 'feral_tiefling',
                'version_key' => self::VERSION_KEY,
                'is_legacy' => false,
                'name' => 'Tiefling Ferino',
                'typical_alignment' => null,
                'is_variant' => true,
                'replaces_race_ability_bonuses' => true,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => 30,
                'description' =>
                    'Variante del Tiefling che sostituisce gli '
                    . 'incrementi di caratteristica standard con '
                    . 'una maggiore agilità.',
                'notes' =>
                    'Eredita dal Tiefling la taglia, la velocità '
                    . 'e le capacità che non vengono sostituite.',
            ]
        );

        //Recupera le caratteristiche interessate
        $abilities = Ability::query()
            ->whereIn('short_name', [
                'DES',
                'INT',
            ])
            ->get()
            ->keyBy('short_name');

        //Definisce i bonus sostitutivi
        $bonuses = [
            'DES' => 2,
            'INT' => 1,
        ];

        //Crea o aggiorna i bonus senza duplicarli
        foreach ($bonuses as $shortName => $bonus) {
            $ability = $abilities->get($shortName);

            if ($ability === null) {
                throw new RuntimeException(
                    "Caratteristica {$shortName} non trovata."
                );
            }

            $feralTiefling->abilityBonuses()->updateOrCreate(
                [
                    'ability_id' => $ability->id,
                ],
                [
                    'bonus' => $bonus,
                    'can_be_reassigned' => true,
                    'notes' =>
                        'Bonus sostitutivo compatibile con le '
                        . 'regole opzionali di personalizzazione.',
                ]
            );
        }
    }

    //Crea le alternative a Versatilità nelle Abilità
    private function seedHalfElfVariants(
        Collection $features
    ): void {
        $halfElf = Race::query()
            ->where('key', 'half_elf')
            ->firstOrFail();

        //Recupera la capacità PHB che viene sostituita
        $skillVersatility = Feature::query()
            ->where(
                'key',
                'half_elf_skill_versatility_phb_2014'
            )
            ->firstOrFail();

        //Crea la scelta opzionale
        $choice = $halfElf->choices()->updateOrCreate(
            [
                'key' => 'half_elf_ancestry_trait_scag_2015',
            ],
            [
                'name' => 'Tratto dell’Ascendenza Elfica',
                'choice_type' => 'feature',
                'replaces_feature_id' => $skillVersatility->id,
                'choose' => 1,
                'level' => 1,
                'required' => false,
                'requires_dm_permission' => true,
                'sort_order' => 20,
                'description' =>
                    'Il Mezzelfo può sostituire Versatilità nelle '
                    . 'Abilità con un tratto collegato alla propria '
                    . 'ascendenza elfica.',
                'notes' =>
                    'Se non viene selezionata alcuna opzione, il '
                    . 'personaggio mantiene Versatilità nelle Abilità.',
            ]
        );

        //Definisce le alternative disponibili
        $options = [
            [
                'key' => 'wood_elf_weapon_training',
                'feature_key' =>
                    'half_elf_wood_weapon_training_scag_2015',
                'ancestry_key' => 'wood_elf',
                'condition' =>
                    'Richiede ascendenza da Elfo dei Boschi.',
                'sort_order' => 10,
            ],
            [
                'key' => 'fleet_of_foot',
                'feature_key' =>
                    'half_elf_fleet_of_foot_scag_2015',
                'ancestry_key' => 'wood_elf',
                'condition' =>
                    'Richiede ascendenza da Elfo dei Boschi.',
                'sort_order' => 20,
            ],
            [
                'key' => 'mask_of_the_wild',
                'feature_key' =>
                    'half_elf_mask_of_the_wild_scag_2015',
                'ancestry_key' => 'wood_elf',
                'condition' =>
                    'Richiede ascendenza da Elfo dei Boschi.',
                'sort_order' => 30,
            ],
            [
                'key' => 'high_elf_weapon_training',
                'feature_key' =>
                    'half_elf_high_weapon_training_scag_2015',
                'ancestry_key' => 'high_elf',
                'condition' =>
                    'Richiede ascendenza da Elfo del Sole '
                    . 'o Elfo della Luna.',
                'sort_order' => 40,
            ],
            [
                'key' => 'high_elf_cantrip',
                'feature_key' =>
                    'half_elf_high_cantrip_scag_2015',
                'ancestry_key' => 'high_elf',
                'condition' =>
                    'Richiede ascendenza da Elfo del Sole '
                    . 'o Elfo della Luna.',
                'sort_order' => 50,
            ],
            [
                'key' => 'drow_magic',
                'feature_key' =>
                    'half_elf_drow_magic_scag_2015',
                'ancestry_key' => 'drow',
                'condition' =>
                    'Richiede ascendenza da Drow.',
                'sort_order' => 60,
            ],
            [
                'key' => 'aquatic_swim_speed',
                'feature_key' =>
                    'half_elf_swim_speed_scag_2015',
                'ancestry_key' => 'aquatic_elf',
                'condition' =>
                    'Richiede ascendenza da Elfo Acquatico.',
                'sort_order' => 70,
            ],
        ];

        $this->syncFeatureOptions(
            $choice,
            $features,
            $options
        );
    }

    //Crea le alternative al Retaggio Infernale
    private function seedTieflingVariants(
        Collection $features
    ): void {
        $tiefling = Race::query()
            ->where('key', 'tiefling')
            ->firstOrFail();

        //Recupera la capacità PHB che viene sostituita
        $infernalLegacy = Feature::query()
            ->where(
                'key',
                'tiefling_infernal_legacy_phb_2014'
            )
            ->firstOrFail();

        //Crea la scelta opzionale
        $choice = $tiefling->choices()->updateOrCreate(
            [
                'key' =>
                    'tiefling_infernal_legacy_variant_scag_2015',
            ],
            [
                'name' => 'Variante del Retaggio Infernale',
                'choice_type' => 'feature',
                'replaces_feature_id' => $infernalLegacy->id,
                'choose' => 1,
                'level' => 1,
                'required' => false,
                'requires_dm_permission' => true,
                'sort_order' => 20,
                'description' =>
                    'Il Tiefling può sostituire Retaggio Infernale '
                    . 'con una delle varianti autorizzate dal DM.',
                'notes' =>
                    'Se non viene selezionata alcuna opzione, il '
                    . 'personaggio mantiene Retaggio Infernale.',
            ]
        );

        //Definisce le tre alternative meccaniche
        $options = [
            [
                'key' => 'devils_tongue',
                'feature_key' =>
                    'tiefling_devils_tongue_scag_2015',
                'ancestry_key' => null,
                'condition' =>
                    'Richiede l’approvazione del DM.',
                'sort_order' => 10,
            ],
            [
                'key' => 'hellfire',
                'feature_key' =>
                    'tiefling_hellfire_scag_2015',
                'ancestry_key' => null,
                'condition' =>
                    'Richiede l’approvazione del DM.',
                'sort_order' => 20,
            ],
            [
                'key' => 'winged',
                'feature_key' =>
                    'tiefling_winged_scag_2015',
                'ancestry_key' => null,
                'condition' =>
                    'Richiede l’approvazione del DM.',
                'sort_order' => 30,
            ],
        ];

        $this->syncFeatureOptions(
            $choice,
            $features,
            $options
        );
    }

    //Sincronizza le opzioni di una scelta razziale
    private function syncFeatureOptions(
        RaceChoice $choice,
        Collection $features,
        array $options
    ): void {
        $expectedKeys = array_column($options, 'key');

        foreach ($options as $optionData) {
            $feature = $features->get(
                $optionData['feature_key']
            );

            if ($feature === null) {
                throw new RuntimeException(
                    'Capacità '
                    . $optionData['feature_key']
                    . ' non trovata.'
                );
            }

            $choice->options()->updateOrCreate(
                [
                    'key' => $optionData['key'],
                ],
                [
                    'option_type' => 'feature',
                    'option_id' => $feature->id,
                    'option_text' => null,
                    'ancestry_key' =>
                        $optionData['ancestry_key'],
                    'eligibility_condition' =>
                        $optionData['condition'],
                    'value' => null,
                    'quantity' => 1,
                    'sort_order' =>
                        $optionData['sort_order'],
                    'notes' => null,
                ]
            );
        }

        //Elimina eventuali opzioni non più previste
        $choice->options()
            ->whereNotIn('key', $expectedKeys)
            ->delete();
    }

    //Crea le capacità selezionabili dalle varianti
    private function seedVariantFeatures(
        Ruleset $ruleset
    ): Collection {
        $features = collect();

        foreach ($this->featureDefinitions() as $key => $data) {
            $feature = $ruleset->features()->updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'name' => $data['name'],
                    'type' => 'race',
                    'level' => 1,
                    'description' => $data['description'],
                    'max_uses' => null,
                    'recharge' => null,
                    'notes' => $data['notes'] ?? null,
                ]
            );

            $features->put($key, $feature);
        }

        return $features;
    }

    //Restituisce le capacità alternative descritte nello SCAG
    private function featureDefinitions(): array
    {
        return [
            'half_elf_wood_weapon_training_scag_2015' => [
                'name' => 'Addestramento nelle Armi degli Elfi dei Boschi',
                'description' =>
                    'Il Mezzelfo ottiene competenza nelle armi '
                    . 'tradizionalmente utilizzate dagli Elfi '
                    . 'dei Boschi.',
            ],
            'half_elf_fleet_of_foot_scag_2015' => [
                'name' => 'Piè Veloce',
                'description' =>
                    'La velocità terrestre del Mezzelfo aumenta '
                    . 'fino a 10,5 metri.',
            ],
            'half_elf_mask_of_the_wild_scag_2015' => [
                'name' => 'Maschera delle Terre Selvagge',
                'description' =>
                    'Il Mezzelfo può tentare di nascondersi anche '
                    . 'quando è solo parzialmente oscurato da '
                    . 'fenomeni naturali.',
            ],
            'half_elf_high_weapon_training_scag_2015' => [
                'name' => 'Addestramento nelle Armi degli Elfi Alti',
                'description' =>
                    'Il Mezzelfo ottiene competenza nelle armi '
                    . 'tradizionalmente utilizzate dagli Elfi Alti.',
            ],
            'half_elf_high_cantrip_scag_2015' => [
                'name' => 'Trucchetto degli Elfi Alti',
                'description' =>
                    'Il Mezzelfo conosce un trucchetto scelto '
                    . 'dalla lista degli incantesimi da mago e '
                    . 'utilizza Intelligenza per lanciarlo.',
                'notes' =>
                    'Il trucchetto sarà registrato mediante una '
                    . 'scelta strutturata degli incantesimi.',
            ],
            'half_elf_drow_magic_scag_2015' => [
                'name' => 'Magia Drow',
                'description' =>
                    'Il Mezzelfo conosce Luci Danzanti. Dal 3° '
                    . 'livello può utilizzare Luminescenza e dal '
                    . '5° livello Oscurità, recuperando gli usi '
                    . 'limitati con un riposo lungo.',
            ],
            'half_elf_swim_speed_scag_2015' => [
                'name' => 'Velocità di Nuoto',
                'description' =>
                    'Il Mezzelfo possiede una velocità di nuoto '
                    . 'pari a 9 metri.',
                'notes' =>
                    'La velocità sarà registrata anche nella '
                    . 'relazione strutturata dei movimenti.',
            ],
            'tiefling_devils_tongue_scag_2015' => [
                'name' => 'Lingua del Diavolo',
                'description' =>
                    'Il Tiefling conosce Beffa Crudele. Dal 3° '
                    . 'livello può utilizzare Charme su Persone '
                    . 'e dal 5° livello Estasiare. Carisma è la '
                    . 'caratteristica da incantatore.',
            ],
            'tiefling_hellfire_scag_2015' => [
                'name' => 'Fuoco Infernale',
                'description' =>
                    'Questa variante mantiene la progressione del '
                    . 'Retaggio Infernale, ma sostituisce '
                    . 'Rimprovero Infernale con Mani Brucianti.',
            ],
            'tiefling_winged_scag_2015' => [
                'name' => 'Alato',
                'description' =>
                    'Il Tiefling possiede ali simili a quelle di '
                    . 'un pipistrello e una velocità di volo di '
                    . '9 metri quando non indossa armature pesanti.',
                'notes' =>
                    'La velocità sarà registrata anche nella '
                    . 'relazione strutturata dei movimenti.',
            ],
        ];
    }
}
