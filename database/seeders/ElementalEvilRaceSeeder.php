<?php

namespace Database\Seeders;

use App\Models\CreatureType;
use App\Models\MovementType;
use App\Models\Race;
use App\Models\Ruleset;
use App\Models\Size;
use App\Models\Subrace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class ElementalEvilRaceSeeder extends Seeder
{
    //Identifica le versioni pubblicate nel manuale EEPC del 2015
    private const VERSION_KEY = 'eepc_2015';

    //Crea le razze e le sottorazze del Compendio
    //del Giocatore del Male Elementale
    public function run(): void
    {
        //Crea prima le razze del Manuale del Giocatore:
        //lo Gnomo delle Profondità dipende dalla razza Gnomo
        $this->call(RaceSeeder::class);

        //Recupera il regolamento D&D 5e del 2014
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Le razze del manuale sono di tipo Umanoide
        $humanoid = CreatureType::query()
            ->where('key', 'humanoid')
            ->firstOrFail();

        //Recupera le taglie utilizzate dal manuale
        $sizes = Size::query()
            ->whereIn('name', [
                'Piccola',
                'Media',
            ])
            ->get()
            ->keyBy('name');

        //Recupera i tipi di movimento necessari
        $movementTypes = MovementType::query()
            ->whereIn('name', [
                'Terrestre',
                'Volare',
                'Nuotare',
            ])
            ->get()
            ->keyBy('name');

        //Crea Aarakocra, Genasi e Goliath
        foreach ($this->races() as $raceData) {
            $this->seedRace(
                $ruleset,
                $humanoid,
                $sizes,
                $movementTypes,
                $raceData
            );
        }

        //Lo Gnomo delle Profondità è una sottorazza
        //dello Gnomo del Manuale del Giocatore
        $this->seedDeepGnome(
            $sizes,
            $movementTypes
        );
    }

    //Crea o aggiorna una razza principale dell'EEPC
    private function seedRace(
        Ruleset $ruleset,
        CreatureType $humanoid,
        Collection $sizes,
        Collection $movementTypes,
        array $raceData
    ): Race {
        //Crea la razza senza duplicarla
        $race = $ruleset->races()->updateOrCreate(
            [
                'key' => $raceData['key'],
            ],
            [
                //Identità condivisa dalle varie versioni editoriali
                'canonical_key' => $raceData['canonical_key'],

                //Versione specifica del manuale EEPC
                'version_key' => self::VERSION_KEY,

                //Queste versioni sono state successivamente
                //revisionate in Mordenkainen Presents:
                //Monsters of the Multiverse
                'is_legacy' => true,

                'name' => $raceData['name'],
                'creature_type_id' => $humanoid->id,
                'is_lineage' => false,
                'can_replace_race' => false,
                'selectable' => true,

                //Il manuale richiede il consenso del DM
                'requires_dm_permission' => true,

                'description' => $raceData['description'],
                'typical_alignment' =>
                    $raceData['typical_alignment'],
                'sort_order' => $raceData['sort_order'],
                'notes' => null,
            ]
        );

        //Assegna la taglia della razza
        $this->syncSize(
            $race,
            $sizes,
            $raceData['size_name']
        );

        //Assegna tutti i suoi tipi di movimento
        $this->syncMovements(
            $race,
            $movementTypes,
            $raceData['movements']
        );

        //Inserisce i dati relativi a età, altezza e peso
        $this->syncPhysicalTraits(
            $race,
            $raceData['physical_traits']
        );

        //Crea le eventuali sottorazze
        foreach ($raceData['subraces'] as $subraceData) {
            $this->seedSubrace(
                $race,
                $movementTypes,
                $subraceData
            );
        }

        return $race;
    }

    //Crea o aggiorna una sottorazza dell'EEPC
    private function seedSubrace(
        Race $race,
        Collection $movementTypes,
        array $subraceData
    ): Subrace {
        //Crea la sottorazza senza duplicarla
        $subrace = $race->subraces()->updateOrCreate(
            [
                'key' => $subraceData['key'],
            ],
            [
                'canonical_key' =>
                    $subraceData['canonical_key'],
                'version_key' => self::VERSION_KEY,
                'is_legacy' => true,
                'name' => $subraceData['name'],
                'typical_alignment' =>
                    $subraceData['typical_alignment'],
                'is_variant' => false,
                'replaces_race_ability_bonuses' => false,
                'selectable' => true,
                'requires_dm_permission' => true,
                'sort_order' => $subraceData['sort_order'],
                'description' =>
                    $subraceData['description'],
                'notes' => null,
            ]
        );

        //Inserisce eventuali caratteristiche fisiche specifiche
        $this->syncPhysicalTraits(
            $subrace,
            $subraceData['physical_traits']
        );

        //Inserisce eventuali movimenti aggiuntivi
        $this->syncMovements(
            $subrace,
            $movementTypes,
            $subraceData['movements']
        );

        return $subrace;
    }

    //Assegna una taglia alla razza
    private function syncSize(
        Race $race,
        Collection $sizes,
        string $sizeName
    ): void {
        //Recupera la taglia richiesta
        $size = $sizes->get($sizeName);

        //Interrompe il seeding se la taglia non esiste
        if ($size === null) {
            throw new RuntimeException(
                "Taglia {$sizeName} non trovata."
            );
        }

        //Ogni razza possiede una sola taglia base
        $race->sizeAssignment()->updateOrCreate(
            [],
            [
                'size_id' => $size->id,
                'notes' => null,
            ]
        );
    }

    //Sincronizza i movimenti di una razza o sottorazza
    private function syncMovements(
        Race|Subrace $owner,
        Collection $movementTypes,
        array $movementDefinitions
    ): void {
        //Memorizza i movimenti che devono rimanere assegnati
        $expectedMovementTypeIds = [];

        foreach (
            $movementDefinitions as $movementDefinition
        ) {
            //Recupera il tipo di movimento richiesto
            $movementType = $movementTypes->get(
                $movementDefinition['name']
            );

            //Interrompe il seeding se il movimento non esiste
            if ($movementType === null) {
                throw new RuntimeException(
                    'Tipo di movimento '
                    . "{$movementDefinition['name']} "
                    . 'non trovato.'
                );
            }

            $expectedMovementTypeIds[] = $movementType->id;

            //Crea o aggiorna il movimento
            $owner->movements()->updateOrCreate(
                [
                    'movement_type_id' => $movementType->id,
                ],
                [
                    'speed_meters' =>
                        $movementDefinition['speed_meters'],
                    'condition' =>
                        $movementDefinition['condition'],
                ]
            );
        }

        //Rimuove eventuali movimenti obsoleti
        $owner->movements()
            ->whereNotIn(
                'movement_type_id',
                $expectedMovementTypeIds
            )
            ->delete();
    }

    //Sincronizza i dati fisici di una razza o sottorazza
    private function syncPhysicalTraits(
        Race|Subrace $owner,
        ?array $physicalTraits
    ): void {
        //Se il manuale non fornisce dati specifici,
        //la sottorazza utilizza quelli della razza principale
        if ($physicalTraits === null) {
            $owner->physicalTraits()->delete();

            return;
        }

        //Crea o aggiorna l'unica configurazione fisica
        $owner->physicalTraits()->updateOrCreate(
            [],
            $physicalTraits
        );
    }

    //Crea lo Gnomo delle Profondità come sottorazza
    //dello Gnomo del Manuale del Giocatore
    private function seedDeepGnome(
        Collection $sizes,
        Collection $movementTypes
    ): void {
        //Recupera la versione PHB 2014 dello Gnomo
        $gnome = Race::query()
            ->where('key', 'gnome')
            ->where('version_key', 'phb_2014')
            ->firstOrFail();

        //Crea la sottorazza utilizzando la funzione condivisa
        $deepGnome = $this->seedSubrace(
            $gnome,
            $movementTypes,
            [
                'key' => 'deep_gnome_eepc_2015',
                'canonical_key' => 'deep_gnome',
                'name' => 'Gnomo delle Profondità',
                'typical_alignment' =>
                    'Tende verso la neutralità e cerca '
                    . 'di evitare conflitti non necessari.',
                'sort_order' => 3,
                'description' =>
                    'Gnomo adattato alla vita nel Sottosuolo, '
                    . 'dotato di grande cautela, scurovisione '
                    . 'superiore e capacità di mimetizzarsi '
                    . 'negli ambienti rocciosi.',
                'physical_traits' => [
                    'maturity_age_years' => 25,
                    'lifespan_years' => 250,
                    'min_height_cm' => '90.000',
                    'max_height_cm' => '100.000',
                    'min_weight_kg' => '40.000',
                    'max_weight_kg' => '60.000',
                    'appearance' =>
                        'Gli gnomi delle profondità hanno '
                        . 'corporature compatte, pelle grigia, '
                        . 'marrone o olivastra e tratti adatti '
                        . 'alla vita sotterranea.',
                    'notes' =>
                        'Il manuale indica una durata della vita '
                        . 'generalmente compresa tra 200 e 250 anni.',
                ],

                //La velocità terrestre di 7,5 metri viene
                //ereditata dalla razza Gnomo
                'movements' => [],
            ]
        );

        //Lo Gnomo delle Profondità mantiene la taglia Piccola
        //ereditata dalla razza principale. Verifichiamo comunque
        //che il catalogo richiesto sia disponibile.
        if ($sizes->get('Piccola') === null) {
            throw new RuntimeException(
                'Taglia Piccola non trovata.'
            );
        }

        //Evita un avviso dell'analizzatore mantenendo evidente
        //che la sottorazza è stata creata correttamente
        if (! $deepGnome->exists) {
            throw new RuntimeException(
                'Impossibile creare lo Gnomo delle Profondità.'
            );
        }
    }

    //Restituisce le tre razze principali dell'EEPC
    private function races(): array
    {
        return [
            [
                'key' => 'aarakocra_eepc_2015',
                'canonical_key' => 'aarakocra',
                'name' => 'Aarakocra',
                'description' =>
                    'Popolo aviario originario principalmente '
                    . 'del Piano Elementale dell’Aria, capace '
                    . 'di volare grazie alle proprie ali.',
                'typical_alignment' =>
                    'Generalmente buono e raramente schierato '
                    . 'in modo deciso tra legge e caos.',
                'sort_order' => 10,
                'size_name' => 'Media',
                'movements' => [
                    [
                        'name' => 'Terrestre',
                        'speed_meters' => '7.500',
                        'condition' => null,
                    ],
                    [
                        'name' => 'Volare',
                        'speed_meters' => '15.000',
                        'condition' =>
                            'Non può utilizzare questa velocità '
                            . 'mentre indossa un’armatura media '
                            . 'o pesante.',
                    ],
                ],
                'physical_traits' => [
                    'maturity_age_years' => 3,
                    'lifespan_years' => 30,
                    'max_height_cm' => '150.000',
                    'min_weight_kg' => '40.000',
                    'max_weight_kg' => '50.000',
                    'appearance' =>
                        'Gli aarakocra hanno corporature leggere, '
                        . 'ali, piumaggio e lineamenti simili '
                        . 'a quelli di grandi uccelli.',
                    'notes' =>
                        'Il manuale indica un’altezza di circa '
                        . '1,5 metri e specifica che raramente '
                        . 'vivono oltre i 30 anni.',
                ],
                'subraces' => [],
            ],
            [
                'key' => 'genasi_eepc_2015',
                'canonical_key' => 'genasi',
                'name' => 'Genasi',
                'description' =>
                    'Popolo toccato dal potere dei Piani '
                    . 'Elementali e caratterizzato da un retaggio '
                    . 'legato ad aria, acqua, fuoco o terra.',
                'typical_alignment' =>
                    'Generalmente indipendente e orientato '
                    . 'verso la neutralità.',
                'sort_order' => 11,
                'size_name' => 'Media',
                'movements' => [
                    [
                        'name' => 'Terrestre',
                        'speed_meters' => '9.000',
                        'condition' => null,
                    ],
                ],
                'physical_traits' => [
                    'maturity_age_years' => 18,
                    'lifespan_years' => 120,
                    'min_height_cm' => '150.000',
                    'max_height_cm' => '180.000',
                    'appearance' =>
                        'I genasi hanno generalmente forma '
                        . 'umanoide e presentano caratteristiche '
                        . 'fisiche collegate al proprio elemento.',
                    'notes' =>
                        'Maturano approssimativamente alla stessa '
                        . 'velocità degli umani e possono vivere '
                        . 'fino a circa 120 anni.',
                ],
                'subraces' => [
                    [
                        'key' => 'water_genasi_eepc_2015',
                        'canonical_key' => 'water_genasi',
                        'name' => 'Genasi dell’Acqua',
                        'typical_alignment' => null,
                        'sort_order' => 1,
                        'description' =>
                            'Genasi legato all’acqua, capace di '
                            . 'respirare sia in aria sia sott’acqua '
                            . 'e dotato di naturale abilità nel nuoto.',
                        'physical_traits' => null,
                        'movements' => [
                            [
                                'name' => 'Nuotare',
                                'speed_meters' => '9.000',
                                'condition' => null,
                            ],
                        ],
                    ],
                    [
                        'key' => 'air_genasi_eepc_2015',
                        'canonical_key' => 'air_genasi',
                        'name' => 'Genasi dell’Aria',
                        'typical_alignment' => null,
                        'sort_order' => 2,
                        'description' =>
                            'Genasi legato all’aria, accompagnato '
                            . 'da manifestazioni del vento e capace '
                            . 'di trattenere il respiro indefinitamente.',
                        'physical_traits' => null,
                        'movements' => [],
                    ],
                    [
                        'key' => 'fire_genasi_eepc_2015',
                        'canonical_key' => 'fire_genasi',
                        'name' => 'Genasi del Fuoco',
                        'typical_alignment' => null,
                        'sort_order' => 3,
                        'description' =>
                            'Genasi legato al fuoco, dotato di '
                            . 'scurovisione, resistenza alle fiamme '
                            . 'e capacità magiche innate.',
                        'physical_traits' => null,
                        'movements' => [],
                    ],
                    [
                        'key' => 'earth_genasi_eepc_2015',
                        'canonical_key' => 'earth_genasi',
                        'name' => 'Genasi della Terra',
                        'typical_alignment' => null,
                        'sort_order' => 4,
                        'description' =>
                            'Genasi legato alla terra e alla pietra, '
                            . 'capace di attraversare più facilmente '
                            . 'alcuni terreni difficili.',
                        'physical_traits' => null,
                        'movements' => [],
                    ],
                ],
            ],
            [
                'key' => 'goliath_eepc_2015',
                'canonical_key' => 'goliath',
                'name' => 'Goliath',
                'description' =>
                    'Popolo montano dalla corporatura imponente, '
                    . 'abituato alle altitudini elevate, al freddo '
                    . 'e a una vita fondata sull’autosufficienza.',
                'typical_alignment' =>
                    'Generalmente legale e orientato '
                    . 'verso la neutralità.',
                'sort_order' => 12,
                'size_name' => 'Media',
                'movements' => [
                    [
                        'name' => 'Terrestre',
                        'speed_meters' => '9.000',
                        'condition' => null,
                    ],
                ],
                'physical_traits' => [
                    'maturity_age_years' => 20,
                    'lifespan_years' => 100,
                    'min_height_cm' => '210.000',
                    'max_height_cm' => '240.000',
                    'min_weight_kg' => '140.000',
                    'max_weight_kg' => '170.000',
                    'appearance' =>
                        'I goliath hanno corporature alte, '
                        . 'massicce e potenti, con caratteristiche '
                        . 'che richiamano la pietra montana.',
                    'notes' =>
                        'Il manuale specifica che generalmente '
                        . 'vivono meno di un secolo.',
                ],
                'subraces' => [],
            ],
        ];
    }
}
