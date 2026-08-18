<?php

namespace Database\Seeders;

use App\Models\CreatureTag;
use Illuminate\Database\Seeder;

class CreatureTagSeeder extends Seeder
{
    //Inserisce i tag aggiuntivi utilizzati dalle creature
    public function run(): void
    {
        //Definisce i tag generali, planari e relativi ai popoli
        $creatureTags = [
            //Capacità o categorie speciali
            [
                'key' => 'shapechanger',
                'name' => 'Mutaforma',
                'description' => 'Identifica una creatura capace di assumere una o più forme differenti tramite una capacità naturale, soprannaturale o magica.',
                'sort_order' => 1,
            ],
            [
                'key' => 'titan',
                'name' => 'Titano',
                'description' => 'Identifica una creatura di potere e dimensioni eccezionali, spesso legata a forze divine, primordiali o cosmiche.',
                'sort_order' => 2,
            ],
            [
                'key' => 'any_race',
                'name' => 'Qualsiasi razza',
                'description' => 'Indica un blocco statistiche umanoide generico che può rappresentare un individuo appartenente a una qualsiasi razza compatibile.',
                'sort_order' => 3,
            ],

            //Categorie di immondi
            [
                'key' => 'demon',
                'name' => 'Demone',
                'description' => 'Identifica un immondo originario dell’Abisso e normalmente legato alle forze del caos e del male.',
                'sort_order' => 4,
            ],
            [
                'key' => 'devil',
                'name' => 'Diavolo',
                'description' => 'Identifica un immondo originario dei Nove Inferi e normalmente legato a strutture gerarchiche e al male legale.',
                'sort_order' => 5,
            ],
            [
                'key' => 'yugoloth',
                'name' => 'Yugoloth',
                'description' => 'Identifica un immondo mercenario dei Piani Inferiori, generalmente interessato al proprio vantaggio e disposto a servire chi lo ricompensa.',
                'sort_order' => 6,
            ],

            //Popoli e lignaggi delle creature
            [
                'key' => 'aarakocra',
                'name' => 'Aarakocra',
                'description' => 'Identifica una creatura appartenente al popolo degli aarakocra.',
                'sort_order' => 7,
            ],
            [
                'key' => 'bullywug',
                'name' => 'Bullywug',
                'description' => 'Identifica una creatura appartenente al popolo anfibio dei bullywug.',
                'sort_order' => 8,
            ],
            [
                'key' => 'dwarf',
                'name' => 'Nano',
                'description' => 'Identifica una creatura appartenente a un popolo o lignaggio nanico.',
                'sort_order' => 9,
            ],
            [
                'key' => 'elf',
                'name' => 'Elfo',
                'description' => 'Identifica una creatura appartenente a un popolo o lignaggio elfico.',
                'sort_order' => 10,
            ],
            [
                'key' => 'gith',
                'name' => 'Gith',
                'description' => 'Identifica una creatura appartenente ai popoli gith.',
                'sort_order' => 11,
            ],
            [
                'key' => 'gnoll',
                'name' => 'Gnoll',
                'description' => 'Identifica una creatura appartenente al popolo degli gnoll, anche quando il suo tipo principale non è Umanoide.',
                'sort_order' => 12,
            ],
            [
                'key' => 'gnome',
                'name' => 'Gnomo',
                'description' => 'Identifica una creatura appartenente a un popolo o lignaggio gnomesco.',
                'sort_order' => 13,
            ],
            [
                'key' => 'goblinoid',
                'name' => 'Goblinoide',
                'description' => 'Identifica una creatura appartenente ai popoli goblinoidi, come goblin, hobgoblin e bugbear.',
                'sort_order' => 14,
            ],
            [
                'key' => 'grimlock',
                'name' => 'Grimlock',
                'description' => 'Identifica una creatura appartenente al popolo sotterraneo dei grimlock.',
                'sort_order' => 15,
            ],
            [
                'key' => 'human',
                'name' => 'Umano',
                'description' => 'Identifica una creatura appartenente a un popolo o lignaggio umano.',
                'sort_order' => 16,
            ],
            [
                'key' => 'kenku',
                'name' => 'Kenku',
                'description' => 'Identifica una creatura appartenente al popolo dei kenku.',
                'sort_order' => 17,
            ],
            [
                'key' => 'kobold',
                'name' => 'Coboldo',
                'description' => 'Identifica una creatura appartenente al popolo dei coboldi.',
                'sort_order' => 18,
            ],
            [
                'key' => 'kuo_toa',
                'name' => 'Kuo-toa',
                'description' => 'Identifica una creatura appartenente al popolo acquatico dei kuo-toa.',
                'sort_order' => 19,
            ],
            [
                'key' => 'lizardfolk',
                'name' => 'Lucertoloide',
                'description' => 'Identifica una creatura appartenente al popolo dei lucertoloidi.',
                'sort_order' => 20,
            ],
            [
                'key' => 'merfolk',
                'name' => 'Marinide',
                'description' => 'Identifica una creatura appartenente ai popoli umanoidi acquatici dei marinidi.',
                'sort_order' => 21,
            ],
            [
                'key' => 'orc',
                'name' => 'Orco',
                'description' => 'Identifica una creatura appartenente a un popolo o lignaggio orchesco.',
                'sort_order' => 22,
            ],
            [
                'key' => 'quaggoth',
                'name' => 'Quaggoth',
                'description' => 'Identifica una creatura appartenente al popolo sotterraneo dei quaggoth.',
                'sort_order' => 23,
            ],
            [
                'key' => 'sahuagin',
                'name' => 'Sahuagin',
                'description' => 'Identifica una creatura appartenente al popolo acquatico dei sahuagin.',
                'sort_order' => 24,
            ],
            [
                'key' => 'thri_kreen',
                'name' => 'Thri-kreen',
                'description' => 'Identifica una creatura appartenente al popolo insettoide dei thri-kreen.',
                'sort_order' => 25,
            ],
            [
                'key' => 'troglodyte',
                'name' => 'Troglodita',
                'description' => 'Identifica una creatura appartenente al popolo sotterraneo dei trogloditi.',
                'sort_order' => 26,
            ],
            [
                'key' => 'yuan_ti',
                'name' => 'Yuan-ti',
                'description' => 'Identifica una creatura appartenente ai popoli e lignaggi degli yuan-ti.',
                'sort_order' => 27,
            ],
        ];

        //Inserisce o aggiorna ogni tag
        foreach ($creatureTags as $creatureTag) {
            CreatureTag::updateOrCreate(
                //Identifica il tag tramite la chiave stabile
                [
                    'key' => $creatureTag['key'],
                ],

                //Inserisce o aggiorna tutti i dati
                [
                    'name' => $creatureTag['name'],
                    'description' => $creatureTag['description'],
                    'sort_order' => $creatureTag['sort_order'],
                    'notes' => null,
                ]
            );
        }
    }
}
