<?php

namespace Database\Seeders;

use App\Models\CreatureType;
use App\Models\MovementType;
use App\Models\Ruleset;
use App\Models\Size;
use Illuminate\Database\Seeder;

class RaceSeeder extends Seeder
{
    //Identifica la versione delle razze del Manuale del Giocatore
    private const VERSION_KEY = 'phb_2014';

    //Crea razze, sottorazze e dati fisici di base
    public function run(): void
    {
        //Esegue i cataloghi richiesti anche quando
        //questo seeder viene avviato singolarmente
        $this->call([
            RulesetSeeder::class,
            CreatureTypeSeeder::class,
            SizeSeeder::class,
            MovementTypeSeeder::class,
        ]);

        //Recupera il regolamento di riferimento
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Recupera il tipo Umanoide
        $humanoid = CreatureType::query()
            ->where('key', 'humanoid')
            ->firstOrFail();

        //Recupera le taglie utilizzate dalle razze
        $sizes = Size::query()
            ->whereIn('name', [
                'Piccola',
                'Media',
            ])
            ->get()
            ->keyBy('name');

        //Recupera il movimento terrestre
        $walking = MovementType::query()
            ->where('name', 'Terrestre')
            ->firstOrFail();

        //Inserisce o aggiorna ogni razza
        foreach ($this->races() as $raceData) {
            //Crea o aggiorna la razza principale
            $race = $ruleset->races()->updateOrCreate(
                [
                    'key' => $raceData['key'],
                ],
                [
                    //Registra l'identità canonica
                    //e la specifica versione editoriale
                    'canonical_key' => $raceData['key'],
                    'version_key' => self::VERSION_KEY,
                    'is_legacy' => false,

                    //Registra i dati generali della razza
                    'name' => $raceData['name'],
                    'creature_type_id' => $humanoid->id,
                    'is_lineage' => false,
                    'can_replace_race' => false,
                    'selectable' => true,
                    'requires_dm_permission' => false,
                    'description' => $raceData['description'],
                    'typical_alignment' =>
                        $raceData['typical_alignment'],
                    'sort_order' => $raceData['sort_order'],
                    'notes' => null,
                ]
            );

            //Assegna la taglia principale
            $race->sizeAssignment()->updateOrCreate(
                [],
                [
                    'size_id' =>
                        $sizes[$raceData['size_name']]->id,
                    'notes' => null,
                ]
            );

            //Assegna la velocità terrestre principale
            $race->movements()->updateOrCreate(
                [
                    'movement_type_id' => $walking->id,
                ],
                [
                    'speed_meters' =>
                        $raceData['walking_speed_meters'],
                    'condition' => null,
                ]
            );

            //Inserisce i tratti fisici della razza
            if ($raceData['physical_traits'] !== null) {
                $race->physicalTraits()->updateOrCreate(
                    [],
                    $raceData['physical_traits']
                );
            }

            //Inserisce le eventuali sottorazze
            foreach ($raceData['subraces'] as $subraceData) {
                //Crea o aggiorna la sottorazza
                $subrace = $race->subraces()->updateOrCreate(
                    [
                        'key' => $subraceData['key'],
                    ],
                    [
                        //Registra l'identità canonica
                        //e la specifica versione editoriale
                        'canonical_key' => $subraceData['key'],
                        'version_key' => self::VERSION_KEY,
                        'is_legacy' => false,

                        //Registra i dati generali della sottorazza
                        'name' => $subraceData['name'],
                        'typical_alignment' =>
                            $subraceData['typical_alignment'],
                        'is_variant' =>
                            $subraceData['is_variant'],
                        'replaces_race_ability_bonuses' =>
                            $subraceData[
                                'replaces_race_ability_bonuses'
                            ] ?? false,
                        'selectable' =>
                            $subraceData['selectable'],
                        'requires_dm_permission' =>
                            $subraceData[
                                'requires_dm_permission'
                            ],
                        'sort_order' =>
                            $subraceData['sort_order'],
                        'description' =>
                            $subraceData['description'],
                        'notes' => null,
                    ]
                );

                //Inserisce gli eventuali tratti fisici specifici
                if ($subraceData['physical_traits'] !== null) {
                    $subrace
                        ->physicalTraits()
                        ->updateOrCreate(
                            [],
                            $subraceData['physical_traits']
                        );
                }

                //Inserisce un'eventuale velocità sostitutiva
                if (
                    $subraceData[
                        'walking_speed_meters'
                    ] !== null
                ) {
                    $subrace->movements()->updateOrCreate(
                        [
                            'movement_type_id' => $walking->id,
                        ],
                        [
                            'speed_meters' =>
                                $subraceData[
                                    'walking_speed_meters'
                                ],
                            'condition' =>
                                'Sostituisce la velocità '
                                . 'terrestre della razza.',
                        ]
                    );
                }
            }
        }
    }

    //Restituisce le razze base del Manuale del Giocatore 2014
    private function races(): array
    {
        return [
            [
                'key' => 'dwarf',
                'name' => 'Nano',
                'description' =>
                    'Popolo robusto e longevo, legato a clan, '
                    . 'tradizioni, artigianato e insediamenti '
                    . 'costruiti nella pietra.',
                'typical_alignment' =>
                    'Spesso legale, con una frequente inclinazione '
                    . 'verso il bene.',
                'sort_order' => 1,
                'size_name' => 'Media',
                'walking_speed_meters' => '7.500',
                'physical_traits' => [
                    'maturity_age_years' => 50,
                    'lifespan_years' => 350,
                    'appearance' =>
                        'I nani hanno corporature compatte e robuste.',
                    'notes' =>
                        'La formula di altezza e peso dipende '
                        . 'dalla sottorazza.',
                ],
                'subraces' => [
                    [
                        'key' => 'hill_dwarf',
                        'name' => 'Nano delle Colline',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 1,
                        'description' =>
                            'Nano caratterizzato da intuito, '
                            . 'resistenza e particolare tenacia.',
                        'physical_traits' => [
                            'base_height_cm' => '111.760',
                            'height_modifier_dice_count' => 2,
                            'height_modifier_die_size' => 4,
                            'height_modifier_unit_cm' => '2.540',
                            'base_weight_kg' => '52.163',
                            'weight_modifier_dice_count' => 2,
                            'weight_modifier_die_size' => 6,
                            'weight_modifier_unit_kg' => '0.453592',
                            'weight_modifier_fixed_kg' => null,
                            'weight_uses_height_modifier' => true,
                        ],
                        'walking_speed_meters' => null,
                    ],
                    [
                        'key' => 'mountain_dwarf',
                        'name' => 'Nano delle Montagne',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 2,
                        'description' =>
                            'Nano abituato a territori difficili, '
                            . 'fisicamente forte e addestrato alla guerra.',
                        'physical_traits' => [
                            'base_height_cm' => '121.920',
                            'height_modifier_dice_count' => 2,
                            'height_modifier_die_size' => 4,
                            'height_modifier_unit_cm' => '2.540',
                            'base_weight_kg' => '58.967',
                            'weight_modifier_dice_count' => 2,
                            'weight_modifier_die_size' => 6,
                            'weight_modifier_unit_kg' => '0.453592',
                            'weight_modifier_fixed_kg' => null,
                            'weight_uses_height_modifier' => true,
                        ],
                        'walking_speed_meters' => null,
                    ],
                ],
            ],
            [
                'key' => 'elf',
                'name' => 'Elfo',
                'description' =>
                    'Popolo longevo, agile e legato alla magia, '
                    . 'alla natura e a tradizioni molto antiche.',
                'typical_alignment' =>
                    'Spesso caotico e frequentemente orientato al bene.',
                'sort_order' => 2,
                'size_name' => 'Media',
                'walking_speed_meters' => '9.000',
                'physical_traits' => [
                    'maturity_age_years' => 100,
                    'lifespan_years' => 750,
                    'appearance' =>
                        'Gli elfi hanno corporature snelle e '
                        . 'lineamenti delicati.',
                    'notes' =>
                        'La formula di altezza e peso dipende '
                        . 'dalla sottorazza.',
                ],
                'subraces' => [
                    [
                        'key' => 'high_elf',
                        'name' => 'Elfo Alto',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 1,
                        'description' =>
                            'Elfo dotato di mente acuta e familiarità '
                            . 'con le arti arcane.',
                        'physical_traits' => [
                            'base_height_cm' => '137.160',
                            'height_modifier_dice_count' => 2,
                            'height_modifier_die_size' => 10,
                            'height_modifier_unit_cm' => '2.540',
                            'base_weight_kg' => '40.823',
                            'weight_modifier_dice_count' => 1,
                            'weight_modifier_die_size' => 4,
                            'weight_modifier_unit_kg' => '0.453592',
                            'weight_modifier_fixed_kg' => null,
                            'weight_uses_height_modifier' => true,
                        ],
                        'walking_speed_meters' => null,
                    ],
                    [
                        'key' => 'wood_elf',
                        'name' => 'Elfo dei Boschi',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 2,
                        'description' =>
                            'Elfo rapido, percettivo e particolarmente '
                            . 'legato agli ambienti naturali.',
                        'physical_traits' => [
                            'base_height_cm' => '137.160',
                            'height_modifier_dice_count' => 2,
                            'height_modifier_die_size' => 10,
                            'height_modifier_unit_cm' => '2.540',
                            'base_weight_kg' => '45.359',
                            'weight_modifier_dice_count' => 1,
                            'weight_modifier_die_size' => 4,
                            'weight_modifier_unit_kg' => '0.453592',
                            'weight_modifier_fixed_kg' => null,
                            'weight_uses_height_modifier' => true,
                        ],
                        'walking_speed_meters' => '10.500',
                    ],
                    [
                        'key' => 'drow',
                        'name' => 'Drow',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 3,
                        'description' =>
                            'Elfo originario del Sottosuolo, adattato '
                            . 'all’oscurità e dotato di magia innata.',
                        'physical_traits' => [
                            'base_height_cm' => '134.620',
                            'height_modifier_dice_count' => 2,
                            'height_modifier_die_size' => 6,
                            'height_modifier_unit_cm' => '2.540',
                            'base_weight_kg' => '34.019',
                            'weight_modifier_dice_count' => 1,
                            'weight_modifier_die_size' => 6,
                            'weight_modifier_unit_kg' => '0.453592',
                            'weight_modifier_fixed_kg' => null,
                            'weight_uses_height_modifier' => true,
                        ],
                        'walking_speed_meters' => null,
                    ],
                ],
            ],
            [
                'key' => 'halfling',
                'name' => 'Halfling',
                'description' =>
                    'Popolo di piccola statura, pratico e coraggioso, '
                    . 'spesso legato alla famiglia e alla comunità.',
                'typical_alignment' =>
                    'Spesso legale e generalmente orientato al bene.',
                'sort_order' => 3,
                'size_name' => 'Piccola',
                'walking_speed_meters' => '7.500',
                'physical_traits' => [
                    'maturity_age_years' => 20,
                    'lifespan_years' => 150,
                    'base_height_cm' => '78.740',
                    'height_modifier_dice_count' => 2,
                    'height_modifier_die_size' => 4,
                    'height_modifier_unit_cm' => '2.540',
                    'base_weight_kg' => '15.876',
                    'weight_modifier_dice_count' => null,
                    'weight_modifier_die_size' => null,
                    'weight_modifier_unit_kg' => null,
                    'weight_modifier_fixed_kg' => '0.453592',
                    'weight_uses_height_modifier' => true,
                    'appearance' =>
                        'Gli halfling hanno statura ridotta e '
                        . 'corporature generalmente robuste.',
                ],
                'subraces' => [
                    [
                        'key' => 'lightfoot_halfling',
                        'name' => 'Halfling Piedelesto',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 1,
                        'description' =>
                            'Halfling socievole e capace di nascondersi '
                            . 'sfruttando la presenza di creature più grandi.',
                        'physical_traits' => null,
                        'walking_speed_meters' => null,
                    ],
                    [
                        'key' => 'stout_halfling',
                        'name' => 'Halfling Tozzo',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 2,
                        'description' =>
                            'Halfling particolarmente resistente '
                            . 'e meno vulnerabile agli effetti del veleno.',
                        'physical_traits' => null,
                        'walking_speed_meters' => null,
                    ],
                ],
            ],
            [
                'key' => 'human',
                'name' => 'Umano',
                'description' =>
                    'Popolo molto diversificato, adattabile e diffuso '
                    . 'in numerose culture e regioni.',
                'typical_alignment' =>
                    'Non presenta una tendenza dominante.',
                'sort_order' => 4,
                'size_name' => 'Media',
                'walking_speed_meters' => '9.000',
                'physical_traits' => [
                    'maturity_age_years' => 18,
                    'lifespan_years' => 100,
                    'base_height_cm' => '142.240',
                    'height_modifier_dice_count' => 2,
                    'height_modifier_die_size' => 10,
                    'height_modifier_unit_cm' => '2.540',
                    'base_weight_kg' => '49.895',
                    'weight_modifier_dice_count' => 2,
                    'weight_modifier_die_size' => 4,
                    'weight_modifier_unit_kg' => '0.453592',
                    'weight_modifier_fixed_kg' => null,
                    'weight_uses_height_modifier' => true,
                    'appearance' =>
                        'Gli esseri umani presentano una grande '
                        . 'varietà di stature, corporature e aspetti.',
                ],
                'subraces' => [
                    [
                        'key' => 'variant_human',
                        'name' => 'Umano Variante',
                        'typical_alignment' => null,
                        'is_variant' => true,
                        'replaces_race_ability_bonuses' => true,
                        'selectable' => true,
                        'requires_dm_permission' => true,
                        'sort_order' => 1,
                        'description' =>
                            'Variante opzionale che sostituisce '
                            . 'l’incremento standard delle caratteristiche.',
                        'physical_traits' => null,
                        'walking_speed_meters' => null,
                    ],
                ],
            ],
            [
                'key' => 'dragonborn',
                'name' => 'Dragonide',
                'description' =>
                    'Umanoide di stirpe draconica, caratterizzato '
                    . 'da corporatura imponente e retaggio elementale.',
                'typical_alignment' =>
                    'Spesso orientato verso posizioni definite '
                    . 'piuttosto che verso la neutralità.',
                'sort_order' => 5,
                'size_name' => 'Media',
                'walking_speed_meters' => '9.000',
                'physical_traits' => [
                    'maturity_age_years' => 15,
                    'lifespan_years' => 80,
                    'base_height_cm' => '167.640',
                    'height_modifier_dice_count' => 2,
                    'height_modifier_die_size' => 8,
                    'height_modifier_unit_cm' => '2.540',
                    'base_weight_kg' => '79.379',
                    'weight_modifier_dice_count' => 2,
                    'weight_modifier_die_size' => 6,
                    'weight_modifier_unit_kg' => '0.453592',
                    'weight_modifier_fixed_kg' => null,
                    'weight_uses_height_modifier' => true,
                    'appearance' =>
                        'I dragonidi sono alti, robusti e coperti '
                        . 'da scaglie legate al loro retaggio.',
                ],
                'subraces' => [],
            ],
            [
                'key' => 'gnome',
                'name' => 'Gnomo',
                'description' =>
                    'Popolo di piccola statura, curioso, ingegnoso '
                    . 'e animato da grande entusiasmo.',
                'typical_alignment' =>
                    'Generalmente orientato al bene.',
                'sort_order' => 6,
                'size_name' => 'Piccola',
                'walking_speed_meters' => '7.500',
                'physical_traits' => [
                    'maturity_age_years' => 40,
                    'lifespan_years' => 500,
                    'base_height_cm' => '88.900',
                    'height_modifier_dice_count' => 2,
                    'height_modifier_die_size' => 4,
                    'height_modifier_unit_cm' => '2.540',
                    'base_weight_kg' => '15.876',
                    'weight_modifier_dice_count' => null,
                    'weight_modifier_die_size' => null,
                    'weight_modifier_unit_kg' => null,
                    'weight_modifier_fixed_kg' => '0.453592',
                    'weight_uses_height_modifier' => true,
                    'appearance' =>
                        'Gli gnomi hanno statura ridotta e '
                        . 'lineamenti vivaci ed espressivi.',
                ],
                'subraces' => [
                    [
                        'key' => 'forest_gnome',
                        'name' => 'Gnomo delle Foreste',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 1,
                        'description' =>
                            'Gnomo riservato, legato alla natura '
                            . 'e capace di semplici illusioni.',
                        'physical_traits' => null,
                        'walking_speed_meters' => null,
                    ],
                    [
                        'key' => 'rock_gnome',
                        'name' => 'Gnomo delle Rocce',
                        'typical_alignment' => null,
                        'is_variant' => false,
                        'selectable' => true,
                        'requires_dm_permission' => false,
                        'sort_order' => 2,
                        'description' =>
                            'Gnomo inventivo, resistente e particolarmente '
                            . 'esperto nella costruzione di congegni.',
                        'physical_traits' => null,
                        'walking_speed_meters' => null,
                    ],
                ],
            ],
            [
                'key' => 'half_elf',
                'name' => 'Mezzelfo',
                'description' =>
                    'Discendente di umani ed elfi, combina adattabilità, '
                    . 'presenza personale e retaggio fatato.',
                'typical_alignment' =>
                    'Spesso incline alla libertà personale.',
                'sort_order' => 7,
                'size_name' => 'Media',
                'walking_speed_meters' => '9.000',
                'physical_traits' => [
                    'maturity_age_years' => 20,
                    'lifespan_years' => 180,
                    'base_height_cm' => '144.780',
                    'height_modifier_dice_count' => 2,
                    'height_modifier_die_size' => 8,
                    'height_modifier_unit_cm' => '2.540',
                    'base_weight_kg' => '49.895',
                    'weight_modifier_dice_count' => 2,
                    'weight_modifier_die_size' => 4,
                    'weight_modifier_unit_kg' => '0.453592',
                    'weight_modifier_fixed_kg' => null,
                    'weight_uses_height_modifier' => true,
                    'appearance' =>
                        'I mezzelfi uniscono caratteristiche '
                        . 'fisiche umane ed elfiche.',
                ],
                'subraces' => [],
            ],
            [
                'key' => 'half_orc',
                'name' => 'Mezzorco',
                'description' =>
                    'Discendente di umani e orchi, dotato di forza, '
                    . 'resistenza e presenza fisica notevoli.',
                'typical_alignment' =>
                    'Spesso incline al caos, senza un orientamento '
                    . 'morale obbligatorio.',
                'sort_order' => 8,
                'size_name' => 'Media',
                'walking_speed_meters' => '9.000',
                'physical_traits' => [
                    'maturity_age_years' => 14,
                    'lifespan_years' => 75,
                    'base_height_cm' => '147.320',
                    'height_modifier_dice_count' => 2,
                    'height_modifier_die_size' => 10,
                    'height_modifier_unit_cm' => '2.540',
                    'base_weight_kg' => '63.503',
                    'weight_modifier_dice_count' => 2,
                    'weight_modifier_die_size' => 6,
                    'weight_modifier_unit_kg' => '0.453592',
                    'weight_modifier_fixed_kg' => null,
                    'weight_uses_height_modifier' => true,
                    'appearance' =>
                        'I mezzorchi hanno corporature robuste '
                        . 'e tratti che richiamano il retaggio orchesco.',
                ],
                'subraces' => [],
            ],
            [
                'key' => 'tiefling',
                'name' => 'Tiefling',
                'description' =>
                    'Umanoide segnato da un retaggio infernale, '
                    . 'riconoscibile attraverso diversi tratti soprannaturali.',
                'typical_alignment' =>
                    'Il retaggio infernale non determina '
                    . 'automaticamente l’allineamento individuale.',
                'sort_order' => 9,
                'size_name' => 'Media',
                'walking_speed_meters' => '9.000',
                'physical_traits' => [
                    'maturity_age_years' => 18,
                    'lifespan_years' => null,
                    'base_height_cm' => '144.780',
                    'height_modifier_dice_count' => 2,
                    'height_modifier_die_size' => 8,
                    'height_modifier_unit_cm' => '2.540',
                    'base_weight_kg' => '49.895',
                    'weight_modifier_dice_count' => 2,
                    'weight_modifier_die_size' => 4,
                    'weight_modifier_unit_kg' => '0.453592',
                    'weight_modifier_fixed_kg' => null,
                    'weight_uses_height_modifier' => true,
                    'appearance' =>
                        'I tiefling possono presentare corna, coda, '
                        . 'occhi insoliti e tonalità della pelle particolari.',
                ],
                'subraces' => [],
            ],
        ];
    }
}
