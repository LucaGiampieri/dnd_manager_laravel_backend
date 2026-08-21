<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Race;
use App\Models\Ruleset;
use App\Models\Subrace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class ElementalEvilRaceFeatureSeeder extends Seeder
{
    //Inserisce le capacità razziali pubblicate nell'EEPC
    public function run(): void
    {
        //Crea le capacità PHB ereditate dalle razze principali
        $this->call(RaceFeatureSeeder::class);

        //Crea le razze e sottorazze pubblicate nell'EEPC
        $this->call(ElementalEvilRaceSeeder::class);

        //Recupera il regolamento corretto
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Crea il catalogo delle capacità EEPC
        $features = $this->seedFeatures($ruleset);

        //Assegna le capacità alle razze principali
        $this->assignRaceFeatures(
            $ruleset,
            $features
        );

        //Assegna le capacità alle sottorazze
        $this->assignSubraceFeatures(
            $ruleset,
            $features
        );
    }

    //Crea o aggiorna tutte le capacità EEPC
    private function seedFeatures(
        Ruleset $ruleset
    ): Collection {
        $features = collect();

        foreach (
            $this->featureDefinitions() as $key => $definition
        ) {
            $feature = $ruleset->features()->updateOrCreate(
                [
                    'key' => $key,
                ],
                array_merge(
                    [
                        'level' => 1,
                        'max_uses' => null,
                        'recharge' => null,
                        'notes' => null,
                    ],
                    $definition
                )
            );

            $features->put($key, $feature);
        }

        return $features;
    }

    //Assegna le capacità alle razze principali
    private function assignRaceFeatures(
        Ruleset $ruleset,
        Collection $features
    ): void {
        foreach (
            $this->raceAssignments() as $raceKey => $assignments
        ) {
            $race = Race::query()
                ->where('ruleset_id', $ruleset->id)
                ->where('key', $raceKey)
                ->firstOrFail();

            $this->syncAssignments(
                $race,
                $features,
                $assignments
            );
        }
    }

    //Assegna le capacità alle sottorazze
    private function assignSubraceFeatures(
        Ruleset $ruleset,
        Collection $features
    ): void {
        foreach (
            $this->subraceAssignments() as $subraceKey => $assignments
        ) {
            $subrace = Subrace::query()
                ->where('key', $subraceKey)
                ->whereHas(
                    'race',
                    fn ($query) => $query->where(
                        'ruleset_id',
                        $ruleset->id
                    )
                )
                ->firstOrFail();

            $this->syncAssignments(
                $subrace,
                $features,
                $assignments
            );
        }
    }

    //Crea o aggiorna le assegnazioni
    private function syncAssignments(
        Race|Subrace $owner,
        Collection $features,
        array $assignments
    ): void {
        foreach ($assignments as $featureKey => $sortOrder) {
            $feature = $features->get($featureKey);

            if (! $feature instanceof Feature) {
                throw new RuntimeException(
                    "Capacità {$featureKey} non trovata."
                );
            }

            $owner->featureAssignments()->updateOrCreate(
                [
                    'feature_id' => $feature->id,
                    'level' => 1,
                ],
                [
                    'sort_order' => $sortOrder,
                    'notes' => null,
                ]
            );
        }
    }

    //Restituisce le capacità pubblicate nell'EEPC
    private function featureDefinitions(): array
    {
        return [
            //Capacità degli Aarakocra
            'aarakocra_flight_eepc_2015' => [
                'name' => 'Volo',
                'type' => 'race',
                'description' =>
                    'Conferisce una velocità di volare di 15 metri. '
                    . 'Non può essere utilizzata mentre il personaggio '
                    . 'indossa un’armatura media o pesante.',
            ],
            'aarakocra_talons_eepc_2015' => [
                'name' => 'Speroni',
                'type' => 'race',
                'description' =>
                    'Rende il personaggio competente nei propri colpi '
                    . 'senz’armi, che infliggono 1d4 danni taglienti.',
            ],

            //Capacità dei Genasi dell'Acqua
            'water_genasi_acid_resistance_eepc_2015' => [
                'name' => 'Resistenza all’Acido',
                'type' => 'subrace',
                'description' =>
                    'Conferisce resistenza ai danni da acido.',
            ],
            'water_genasi_amphibious_eepc_2015' => [
                'name' => 'Anfibio',
                'type' => 'subrace',
                'description' =>
                    'Permette di respirare normalmente sia in aria '
                    . 'sia sott’acqua.',
            ],
            'water_genasi_swim_eepc_2015' => [
                'name' => 'Nuotare',
                'type' => 'subrace',
                'description' =>
                    'Conferisce una velocità di nuotare di 9 metri.',
            ],
            'water_genasi_call_to_the_wave_eepc_2015' => [
                'name' => 'Richiamare l’Onda',
                'type' => 'subrace',
                'description' =>
                    'Conferisce il trucchetto Modellare Acqua e, '
                    . 'dal 3° livello, consente di lanciare Creare '
                    . 'o Distruggere Acqua usando Costituzione.',
            ],

            //Capacità dei Genasi dell'Aria
            'air_genasi_unending_breath_eepc_2015' => [
                'name' => 'Respiro Senza Fine',
                'type' => 'subrace',
                'description' =>
                    'Permette di trattenere il respiro a tempo '
                    . 'indeterminato finché il personaggio non '
                    . 'è incapacitato.',
            ],
            'air_genasi_mingle_with_the_wind_eepc_2015' => [
                'name' => 'Mescolarsi al Vento',
                'type' => 'subrace',
                'description' =>
                    'Permette di lanciare Levitazione senza componenti '
                    . 'materiali una volta per riposo lungo, usando '
                    . 'Costituzione.',
            ],

            //Capacità dei Genasi del Fuoco
            'fire_genasi_darkvision_eepc_2015' => [
                'name' => 'Scurovisione',
                'type' => 'subrace',
                'description' =>
                    'Permette di vedere entro 18 metri in luce fioca '
                    . 'e oscurità, dove tutto assume tonalità rossastre.',
            ],
            'fire_genasi_fire_resistance_eepc_2015' => [
                'name' => 'Resistenza al Fuoco',
                'type' => 'subrace',
                'description' =>
                    'Conferisce resistenza ai danni da fuoco.',
            ],
            'fire_genasi_reach_to_the_blaze_eepc_2015' => [
                'name' => 'Mani Fiammeggianti',
                'type' => 'subrace',
                'description' =>
                    'Conferisce il trucchetto Produrre Fiamma e, '
                    . 'dal 3° livello, consente di lanciare Mani '
                    . 'Brucianti usando Costituzione.',
            ],

            //Capacità dei Genasi della Terra
            'earth_genasi_earth_walk_eepc_2015' => [
                'name' => 'Camminare sulla Terra',
                'type' => 'subrace',
                'description' =>
                    'Permette di attraversare terreno difficile '
                    . 'composto da terra o pietra senza spendere '
                    . 'movimento aggiuntivo.',
            ],
            'earth_genasi_merge_with_stone_eepc_2015' => [
                'name' => 'Fondersi nella Pietra',
                'type' => 'subrace',
                'description' =>
                    'Permette di lanciare Passare Senza Tracce senza '
                    . 'componenti materiali una volta per riposo lungo, '
                    . 'usando Costituzione.',
            ],

            //Capacità degli Gnomi delle Profondità
            'deep_gnome_superior_darkvision_eepc_2015' => [
                'name' => 'Scurovisione Superiore',
                'type' => 'subrace',
                'description' =>
                    'Estende la scurovisione fino a 36 metri.',
            ],
            'deep_gnome_stone_camouflage_eepc_2015' => [
                'name' => 'Mimetismo nella Pietra',
                'type' => 'subrace',
                'description' =>
                    'Conferisce vantaggio alle prove di Destrezza '
                    . '(Furtività) effettuate per nascondersi '
                    . 'nei terreni rocciosi.',
            ],

            //Capacità dei Goliath
            'goliath_natural_athlete_eepc_2015' => [
                'name' => 'Atleta Nato',
                'type' => 'race',
                'description' =>
                    'Conferisce competenza nell’abilità Atletica.',
            ],
            'goliath_stones_endurance_eepc_2015' => [
                'name' => 'Resistenza della Pietra',
                'type' => 'race',
                'description' =>
                    'Permette di usare una reazione per ridurre '
                    . 'i danni subiti di 1d12 più il modificatore '
                    . 'di Costituzione.',
                'max_uses' => 1,
                'recharge' => 'short_rest',
            ],
            'goliath_powerful_build_eepc_2015' => [
                'name' => 'Corporatura Possente',
                'type' => 'race',
                'description' =>
                    'Considera il personaggio più grande di una taglia '
                    . 'nel calcolo della capacità di trasporto e del '
                    . 'peso che può spingere, trascinare o sollevare.',
            ],
            'goliath_mountain_born_eepc_2015' => [
                'name' => 'Nato sulle Montagne',
                'type' => 'race',
                'description' =>
                    'Rende il personaggio acclimatato alle altitudini '
                    . 'elevate e naturalmente adattato ai climi freddi.',
            ],
        ];
    }

    //Capacità assegnate alle razze principali
    private function raceAssignments(): array
    {
        return [
            'aarakocra_eepc_2015' => [
                'aarakocra_flight_eepc_2015' => 10,
                'aarakocra_talons_eepc_2015' => 20,
            ],
            'goliath_eepc_2015' => [
                'goliath_natural_athlete_eepc_2015' => 10,
                'goliath_stones_endurance_eepc_2015' => 20,
                'goliath_powerful_build_eepc_2015' => 30,
                'goliath_mountain_born_eepc_2015' => 40,
            ],
        ];
    }

    //Capacità assegnate alle sottorazze
    private function subraceAssignments(): array
    {
        return [
            'water_genasi_eepc_2015' => [
                'water_genasi_acid_resistance_eepc_2015' => 10,
                'water_genasi_amphibious_eepc_2015' => 20,
                'water_genasi_swim_eepc_2015' => 30,
                'water_genasi_call_to_the_wave_eepc_2015' => 40,
            ],
            'air_genasi_eepc_2015' => [
                'air_genasi_unending_breath_eepc_2015' => 10,
                'air_genasi_mingle_with_the_wind_eepc_2015' => 20,
            ],
            'fire_genasi_eepc_2015' => [
                'fire_genasi_darkvision_eepc_2015' => 10,
                'fire_genasi_fire_resistance_eepc_2015' => 20,
                'fire_genasi_reach_to_the_blaze_eepc_2015' => 30,
            ],
            'earth_genasi_eepc_2015' => [
                'earth_genasi_earth_walk_eepc_2015' => 10,
                'earth_genasi_merge_with_stone_eepc_2015' => 20,
            ],
            'deep_gnome_eepc_2015' => [
                'deep_gnome_superior_darkvision_eepc_2015' => 10,
                'deep_gnome_stone_camouflage_eepc_2015' => 20,
            ],
        ];
    }
}
