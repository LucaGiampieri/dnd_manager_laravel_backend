<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Race;
use App\Models\Ruleset;
use App\Models\Subrace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use RuntimeException;

class RaceFeatureSeeder extends Seeder
{
    //Inserisce le capacità delle razze del Manuale del Giocatore
    public function run(): void
    {
        //Crea prima le razze e tutte le dipendenze necessarie
        $this->call(RaceSeeder::class);

        //Recupera il regolamento utilizzato dalle capacità
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Crea o aggiorna il catalogo delle capacità
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

    //Crea tutte le capacità senza duplicati
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

    //Crea o aggiorna le assegnazioni di una razza o sottorazza
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

    //Restituisce le capacità del Manuale del Giocatore
    private function featureDefinitions(): array
    {
        return [
            //Capacità dei Nani
            'dwarf_darkvision_phb_2014' => [
                'name' => 'Scurovisione',
                'type' => 'race',
                'description' =>
                    'Permette di vedere entro 18 metri in condizioni '
                    . 'di luce fioca e oscurità, distinguendo soltanto '
                    . 'le tonalità di grigio nell’oscurità.',
            ],
            'dwarf_resilience_phb_2014' => [
                'name' => 'Resilienza Nanica',
                'type' => 'race',
                'description' =>
                    'Conferisce vantaggio ai tiri salvezza contro '
                    . 'il veleno e resistenza ai danni da veleno.',
            ],
            'dwarf_combat_training_phb_2014' => [
                'name' => 'Addestramento da Combattimento Nanico',
                'type' => 'race',
                'description' =>
                    'Conferisce competenza nelle asce, nelle asce da '
                    . 'battaglia, nei martelli leggeri e nei martelli '
                    . 'da guerra.',
            ],
            'dwarf_tool_proficiency_phb_2014' => [
                'name' => 'Competenza negli Strumenti',
                'type' => 'race',
                'description' =>
                    'Permette di scegliere una competenza tra strumenti '
                    . 'da fabbro, scorte da mescitore e strumenti '
                    . 'da costruttore.',
            ],
            'dwarf_stonecunning_phb_2014' => [
                'name' => 'Esperto Minatore',
                'type' => 'race',
                'description' =>
                    'Migliora le prove di Intelligenza (Storia) '
                    . 'relative all’origine delle strutture in pietra.',
            ],

            //Capacità delle sottorazze naniche
            'hill_dwarf_toughness_phb_2014' => [
                'name' => 'Robustezza Nanica',
                'type' => 'subrace',
                'description' =>
                    'Aumenta di 1 il massimo dei punti ferita per ogni '
                    . 'livello posseduto dal personaggio.',
            ],
            'mountain_dwarf_armor_training_phb_2014' => [
                'name' => 'Addestramento nelle Armature Naniche',
                'type' => 'subrace',
                'description' =>
                    'Conferisce competenza nelle armature leggere '
                    . 'e medie.',
            ],

            //Capacità degli Elfi
            'elf_darkvision_phb_2014' => [
                'name' => 'Scurovisione',
                'type' => 'race',
                'description' =>
                    'Permette di vedere entro 18 metri in condizioni '
                    . 'di luce fioca e oscurità.',
            ],
            'elf_keen_senses_phb_2014' => [
                'name' => 'Sensi Acuti',
                'type' => 'race',
                'description' =>
                    'Conferisce competenza nell’abilità Percezione.',
            ],
            'elf_fey_ancestry_phb_2014' => [
                'name' => 'Retaggio Fatato',
                'type' => 'race',
                'description' =>
                    'Conferisce vantaggio ai tiri salvezza contro '
                    . 'l’essere affascinato e impedisce che la magia '
                    . 'faccia addormentare il personaggio.',
            ],
            'elf_trance_phb_2014' => [
                'name' => 'Trance',
                'type' => 'race',
                'description' =>
                    'L’elfo non dorme normalmente e può meditare '
                    . 'profondamente per quattro ore.',
            ],

            //Capacità degli Elfi Alti
            'high_elf_weapon_training_phb_2014' => [
                'name' => 'Addestramento nelle Armi Elfiche',
                'type' => 'subrace',
                'description' =>
                    'Conferisce competenza nelle spade lunghe, '
                    . 'nelle spade corte, negli archi corti '
                    . 'e negli archi lunghi.',
            ],
            'high_elf_cantrip_phb_2014' => [
                'name' => 'Trucchetto',
                'type' => 'subrace',
                'description' =>
                    'Permette di conoscere un trucchetto scelto '
                    . 'dalla lista degli incantesimi del mago, '
                    . 'utilizzando Intelligenza.',
            ],
            'high_elf_extra_language_phb_2014' => [
                'name' => 'Linguaggio Extra',
                'type' => 'subrace',
                'description' =>
                    'Permette di parlare, leggere e scrivere '
                    . 'un linguaggio aggiuntivo a scelta.',
            ],

            //Capacità degli Elfi dei Boschi
            'wood_elf_weapon_training_phb_2014' => [
                'name' => 'Addestramento nelle Armi Elfiche',
                'type' => 'subrace',
                'description' =>
                    'Conferisce competenza nelle spade lunghe, '
                    . 'nelle spade corte, negli archi corti '
                    . 'e negli archi lunghi.',
            ],
            'wood_elf_fleet_of_foot_phb_2014' => [
                'name' => 'Piè Veloce',
                'type' => 'subrace',
                'description' =>
                    'Aumenta la velocità base sul terreno '
                    . 'dell’elfo dei boschi a 10,5 metri.',
            ],
            'wood_elf_mask_of_the_wild_phb_2014' => [
                'name' => 'Maschera delle Terre Selvagge',
                'type' => 'subrace',
                'description' =>
                    'Permette di nascondersi quando il personaggio '
                    . 'è leggermente oscurato da fenomeni naturali.',
            ],

            //Capacità dei Drow
            'drow_superior_darkvision_phb_2014' => [
                'name' => 'Scurovisione Superiore',
                'type' => 'subrace',
                'description' =>
                    'Estende la scurovisione fino a 36 metri.',
            ],
            'drow_sunlight_sensitivity_phb_2014' => [
                'name' => 'Sensibilità alla Luce del Sole',
                'type' => 'subrace',
                'description' =>
                    'Impartisce svantaggio agli attacchi e alle prove '
                    . 'di Percezione basate sulla vista quando il '
                    . 'personaggio o il bersaglio si trovano nella '
                    . 'luce solare diretta.',
            ],
            'drow_magic_phb_2014' => [
                'name' => 'Magia Drow',
                'type' => 'subrace',
                'description' =>
                    'Conferisce Luci Danzanti e, avanzando di livello, '
                    . 'Luminescenza e Oscurità, utilizzando Carisma.',
            ],
            'drow_weapon_training_phb_2014' => [
                'name' => 'Addestramento nelle Armi Drow',
                'type' => 'subrace',
                'description' =>
                    'Conferisce competenza negli stocchi, nelle spade '
                    . 'corte e nelle balestre a mano.',
            ],

            //Capacità degli Halfling
            'halfling_lucky_phb_2014' => [
                'name' => 'Fortunato',
                'type' => 'race',
                'description' =>
                    'Permette di ripetere un tiro per colpire, '
                    . 'una prova di caratteristica o un tiro salvezza '
                    . 'quando il dado mostra 1.',
            ],
            'halfling_brave_phb_2014' => [
                'name' => 'Coraggioso',
                'type' => 'race',
                'description' =>
                    'Conferisce vantaggio ai tiri salvezza contro '
                    . 'l’essere spaventato.',
            ],
            'halfling_nimbleness_phb_2014' => [
                'name' => 'Agilità Halfling',
                'type' => 'race',
                'description' =>
                    'Permette di attraversare lo spazio occupato '
                    . 'da una creatura di taglia superiore.',
            ],

            //Capacità delle sottorazze Halfling
            'lightfoot_naturally_stealthy_phb_2014' => [
                'name' => 'Furtività Innata',
                'type' => 'subrace',
                'description' =>
                    'Permette di nascondersi dietro una creatura '
                    . 'più grande del personaggio.',
            ],
            'stout_resilience_phb_2014' => [
                'name' => 'Resilienza dei Tozzi',
                'type' => 'subrace',
                'description' =>
                    'Conferisce vantaggio ai tiri salvezza contro '
                    . 'il veleno e resistenza ai danni da veleno.',
            ],

            //Capacità dei Dragonidi
            'dragonborn_ancestry_phb_2014' => [
                'name' => 'Discendenza Draconica',
                'type' => 'race',
                'description' =>
                    'La scelta dell’antenato draconico determina '
                    . 'il tipo di danno e la forma dell’arma a soffio.',
            ],
            'dragonborn_breath_weapon_phb_2014' => [
                'name' => 'Arma a Soffio',
                'type' => 'race',
                'description' =>
                    'Permette di usare un’azione per emettere energia '
                    . 'distruttiva; il danno aumenta con il livello.',
                'max_uses' => 1,
                'recharge' => 'short_rest',
            ],
            'dragonborn_damage_resistance_phb_2014' => [
                'name' => 'Resistenza ai Danni',
                'type' => 'race',
                'description' =>
                    'Conferisce resistenza al tipo di danno associato '
                    . 'alla discendenza draconica scelta.',
            ],

            //Capacità degli Gnomi
            'gnome_darkvision_phb_2014' => [
                'name' => 'Scurovisione',
                'type' => 'race',
                'description' =>
                    'Permette di vedere entro 18 metri in condizioni '
                    . 'di luce fioca e oscurità.',
            ],
            'gnome_cunning_phb_2014' => [
                'name' => 'Astuzia Gnomesca',
                'type' => 'race',
                'description' =>
                    'Conferisce vantaggio ai tiri salvezza su '
                    . 'Intelligenza, Saggezza e Carisma contro '
                    . 'gli effetti magici.',
            ],

            //Capacità degli Gnomi delle Foreste
            'forest_gnome_natural_illusionist_phb_2014' => [
                'name' => 'Illusionista Nato',
                'type' => 'subrace',
                'description' =>
                    'Conferisce il trucchetto Illusione Minore, '
                    . 'utilizzando Intelligenza.',
            ],
            'forest_gnome_speak_with_small_beasts_phb_2014' => [
                'name' => 'Parlare con le Piccole Bestie',
                'type' => 'subrace',
                'description' =>
                    'Permette di comunicare concetti semplici '
                    . 'alle bestie di taglia Piccola o inferiore.',
            ],

            //Capacità degli Gnomi delle Rocce
            'rock_gnome_artificers_lore_phb_2014' => [
                'name' => 'Conoscenze dell’Artefice',
                'type' => 'subrace',
                'description' =>
                    'Migliora le prove di Intelligenza (Storia) '
                    . 'relative a oggetti magici, alchemici '
                    . 'o tecnologici.',
            ],
            'rock_gnome_tinker_phb_2014' => [
                'name' => 'Inventore',
                'type' => 'subrace',
                'description' =>
                    'Conferisce competenza negli strumenti da '
                    . 'inventore e permette di costruire piccoli '
                    . 'congegni meccanici temporanei.',
            ],

            //Capacità dei Mezzelfi
            'half_elf_darkvision_phb_2014' => [
                'name' => 'Scurovisione',
                'type' => 'race',
                'description' =>
                    'Permette di vedere entro 18 metri in condizioni '
                    . 'di luce fioca e oscurità.',
            ],
            'half_elf_fey_ancestry_phb_2014' => [
                'name' => 'Retaggio Fatato',
                'type' => 'race',
                'description' =>
                    'Conferisce vantaggio contro l’essere affascinato '
                    . 'e impedisce che la magia faccia addormentare '
                    . 'il personaggio.',
            ],
            'half_elf_skill_versatility_phb_2014' => [
                'name' => 'Versatilità nelle Abilità',
                'type' => 'race',
                'description' =>
                    'Conferisce competenza in due abilità a scelta.',
            ],

            //Capacità dei Mezzorchi
            'half_orc_darkvision_phb_2014' => [
                'name' => 'Scurovisione',
                'type' => 'race',
                'description' =>
                    'Permette di vedere entro 18 metri in condizioni '
                    . 'di luce fioca e oscurità.',
            ],
            'half_orc_menacing_phb_2014' => [
                'name' => 'Minaccioso',
                'type' => 'race',
                'description' =>
                    'Conferisce competenza nell’abilità Intimidire.',
            ],
            'half_orc_relentless_endurance_phb_2014' => [
                'name' => 'Tenacia Implacabile',
                'type' => 'race',
                'description' =>
                    'Permette di rimanere a 1 punto ferita quando '
                    . 'il personaggio sarebbe ridotto a 0 senza '
                    . 'essere ucciso sul colpo.',
                'max_uses' => 1,
                'recharge' => 'long_rest',
            ],
            'half_orc_savage_attacks_phb_2014' => [
                'name' => 'Attacchi Selvaggi',
                'type' => 'race',
                'description' =>
                    'Aggiunge un dado dell’arma quando il personaggio '
                    . 'realizza un colpo critico con un attacco '
                    . 'in mischia.',
            ],

            //Capacità dei Tiefling
            'tiefling_darkvision_phb_2014' => [
                'name' => 'Scurovisione',
                'type' => 'race',
                'description' =>
                    'Permette di vedere entro 18 metri in condizioni '
                    . 'di luce fioca e oscurità.',
            ],
            'tiefling_hellish_resistance_phb_2014' => [
                'name' => 'Resistenza Infernale',
                'type' => 'race',
                'description' =>
                    'Conferisce resistenza ai danni da fuoco.',
            ],
            'tiefling_infernal_legacy_phb_2014' => [
                'name' => 'Retaggio Infernale',
                'type' => 'race',
                'description' =>
                    'Conferisce Taumaturgia e, avanzando di livello, '
                    . 'Punizione Infernale e Oscurità, utilizzando '
                    . 'Carisma.',
            ],
        ];
    }

    //Capacità concesse dalle razze principali
    private function raceAssignments(): array
    {
        return [
            'dwarf' => [
                'dwarf_darkvision_phb_2014' => 10,
                'dwarf_resilience_phb_2014' => 20,
                'dwarf_combat_training_phb_2014' => 30,
                'dwarf_tool_proficiency_phb_2014' => 40,
                'dwarf_stonecunning_phb_2014' => 50,
            ],
            'elf' => [
                'elf_darkvision_phb_2014' => 10,
                'elf_keen_senses_phb_2014' => 20,
                'elf_fey_ancestry_phb_2014' => 30,
                'elf_trance_phb_2014' => 40,
            ],
            'halfling' => [
                'halfling_lucky_phb_2014' => 10,
                'halfling_brave_phb_2014' => 20,
                'halfling_nimbleness_phb_2014' => 30,
            ],
            'dragonborn' => [
                'dragonborn_ancestry_phb_2014' => 10,
                'dragonborn_breath_weapon_phb_2014' => 20,
                'dragonborn_damage_resistance_phb_2014' => 30,
            ],
            'gnome' => [
                'gnome_darkvision_phb_2014' => 10,
                'gnome_cunning_phb_2014' => 20,
            ],
            'half_elf' => [
                'half_elf_darkvision_phb_2014' => 10,
                'half_elf_fey_ancestry_phb_2014' => 20,
                'half_elf_skill_versatility_phb_2014' => 30,
            ],
            'half_orc' => [
                'half_orc_darkvision_phb_2014' => 10,
                'half_orc_menacing_phb_2014' => 20,
                'half_orc_relentless_endurance_phb_2014' => 30,
                'half_orc_savage_attacks_phb_2014' => 40,
            ],
            'tiefling' => [
                'tiefling_darkvision_phb_2014' => 10,
                'tiefling_hellish_resistance_phb_2014' => 20,
                'tiefling_infernal_legacy_phb_2014' => 30,
            ],
        ];
    }

    //Capacità concesse dalle sottorazze
    private function subraceAssignments(): array
    {
        return [
            'hill_dwarf' => [
                'hill_dwarf_toughness_phb_2014' => 10,
            ],
            'mountain_dwarf' => [
                'mountain_dwarf_armor_training_phb_2014' => 10,
            ],
            'high_elf' => [
                'high_elf_weapon_training_phb_2014' => 10,
                'high_elf_cantrip_phb_2014' => 20,
                'high_elf_extra_language_phb_2014' => 30,
            ],
            'wood_elf' => [
                'wood_elf_weapon_training_phb_2014' => 10,
                'wood_elf_fleet_of_foot_phb_2014' => 20,
                'wood_elf_mask_of_the_wild_phb_2014' => 30,
            ],
            'drow' => [
                'drow_superior_darkvision_phb_2014' => 10,
                'drow_sunlight_sensitivity_phb_2014' => 20,
                'drow_magic_phb_2014' => 30,
                'drow_weapon_training_phb_2014' => 40,
            ],
            'lightfoot_halfling' => [
                'lightfoot_naturally_stealthy_phb_2014' => 10,
            ],
            'stout_halfling' => [
                'stout_resilience_phb_2014' => 10,
            ],
            'forest_gnome' => [
                'forest_gnome_natural_illusionist_phb_2014' => 10,
                'forest_gnome_speak_with_small_beasts_phb_2014' => 20,
            ],
            'rock_gnome' => [
                'rock_gnome_artificers_lore_phb_2014' => 10,
                'rock_gnome_tinker_phb_2014' => 20,
            ],
        ];
    }
}
