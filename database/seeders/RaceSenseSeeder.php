<?php

namespace Database\Seeders;

use App\Models\Race;
use App\Models\Sense;
use App\Models\Subrace;
use Illuminate\Database\Seeder;

class RaceSenseSeeder extends Seeder
{
    //Inserisce i sensi automatici di razze e sottorazze
    public function run(): void
    {
        //Crea prima tutti i cataloghi richiesti
        $this->call([
            SenseSeeder::class,
            RaceSeeder::class,
            ElementalEvilRaceSeeder::class,
            SwordCoastRaceSeeder::class,
        ]);

        //Recupera la Scurovisione dal catalogo
        $darkvision = Sense::query()
            ->where('key', 'darkvision')
            ->firstOrFail();

        //Assegna la Scurovisione alle razze principali
        foreach ($this->raceDarkvision() as $raceKey => $range) {
            $race = Race::query()
                ->where('key', $raceKey)
                ->firstOrFail();

            $race->senseAssignments()->updateOrCreate(
                [
                    'sense_id' => $darkvision->id,
                ],
                [
                    'range_meters' => $range,
                    'is_blind_beyond_range' => false,
                    'condition' =>
                        'Permette di vedere in luce fioca e '
                        . 'nell’oscurità entro la portata indicata.',
                    'notes' => null,
                ]
            );
        }

        //Assegna o sostituisce la Scurovisione delle sottorazze
        foreach (
            $this->subraceDarkvision() as $subraceKey => $range
        ) {
            $subrace = Subrace::query()
                ->where('key', $subraceKey)
                ->firstOrFail();

            $subrace->senseAssignments()->updateOrCreate(
                [
                    'sense_id' => $darkvision->id,
                ],
                [
                    'range_meters' => $range,
                    'is_blind_beyond_range' => false,
                    'condition' =>
                        'Sostituisce l’eventuale portata inferiore '
                        . 'ereditata dalla razza principale.',
                    'notes' => null,
                ]
            );
        }
    }

    //Restituisce le razze con Scurovisione a 18 metri
    private function raceDarkvision(): array
    {
        return [
            'dwarf' => '18.000',
            'elf' => '18.000',
            'gnome' => '18.000',
            'half_elf' => '18.000',
            'half_orc' => '18.000',
            'tiefling' => '18.000',
        ];
    }

    //Restituisce le sottorazze con portata propria
    private function subraceDarkvision(): array
    {
        return [
            //Il Drow possiede Scurovisione Superiore
            'drow' => '36.000',

            //Il Genasi del Fuoco possiede Scurovisione
            'fire_genasi_eepc_2015' => '18.000',

            //Lo Svirfneblin possiede Scurovisione Superiore
            'deep_gnome_eepc_2015' => '36.000',

            //Il Duergar possiede Scurovisione Superiore
            'duergar_scag_2015' => '36.000',
        ];
    }
}
