<?php

namespace Database\Seeders;

use App\Models\Ruleset;
use Illuminate\Database\Seeder;

class AlignmentSeeder extends Seeder
{
    //Inserisce gli allineamenti del regolamento D&D 5e 2014
    public function run(): void
    {
        //Recupera il regolamento a cui collegare gli allineamenti
        $ruleset = Ruleset::query()
            ->where('key', 'dnd5e_2014')
            ->firstOrFail();

        //Definisce i nove allineamenti e le loro posizioni sui due assi
        $alignments = [
            //Legale Buono
            [
                'key' => 'lawful_good',
                'name' => 'Legale Buono',
                'abbreviation' => 'LB',
                'ethical_axis' => 'lawful',
                'moral_axis' => 'good',
                'description' => 'Agisce per il bene rispettando doveri, regole e responsabilità verso gli altri.',
                'sort_order' => 1,
            ],

            //Neutrale Buono
            [
                'key' => 'neutral_good',
                'name' => 'Neutrale Buono',
                'abbreviation' => 'NB',
                'ethical_axis' => 'neutral',
                'moral_axis' => 'good',
                'description' => 'Cerca di aiutare gli altri senza considerare l’ordine o la libertà come valori assoluti.',
                'sort_order' => 2,
            ],

            //Caotico Buono
            [
                'key' => 'chaotic_good',
                'name' => 'Caotico Buono',
                'abbreviation' => 'CB',
                'ethical_axis' => 'chaotic',
                'moral_axis' => 'good',
                'description' => 'Segue la propria coscienza per fare il bene e attribuisce grande valore alla libertà.',
                'sort_order' => 3,
            ],

            //Legale Neutrale
            [
                'key' => 'lawful_neutral',
                'name' => 'Legale Neutrale',
                'abbreviation' => 'LN',
                'ethical_axis' => 'lawful',
                'moral_axis' => 'neutral',
                'description' => 'Agisce secondo leggi, tradizioni o codici personali senza schierarsi necessariamente tra bene e male.',
                'sort_order' => 4,
            ],

            //Neutrale
            [
                'key' => 'neutral',
                'name' => 'Neutrale',
                'abbreviation' => 'N',
                'ethical_axis' => 'neutral',
                'moral_axis' => 'neutral',
                'description' => 'Evita posizioni assolute e decide in base alle circostanze senza favorire stabilmente uno degli estremi.',
                'sort_order' => 5,
            ],

            //Caotico Neutrale
            [
                'key' => 'chaotic_neutral',
                'name' => 'Caotico Neutrale',
                'abbreviation' => 'CN',
                'ethical_axis' => 'chaotic',
                'moral_axis' => 'neutral',
                'description' => 'Protegge la propria libertà e segue i propri impulsi senza aderire stabilmente al bene o al male.',
                'sort_order' => 6,
            ],

            //Legale Malvagio
            [
                'key' => 'lawful_evil',
                'name' => 'Legale Malvagio',
                'abbreviation' => 'LM',
                'ethical_axis' => 'lawful',
                'moral_axis' => 'evil',
                'description' => 'Persegue i propri interessi attraverso gerarchie, regole, tradizioni o codici che può sfruttare a proprio vantaggio.',
                'sort_order' => 7,
            ],

            //Neutrale Malvagio
            [
                'key' => 'neutral_evil',
                'name' => 'Neutrale Malvagio',
                'abbreviation' => 'NM',
                'ethical_axis' => 'neutral',
                'moral_axis' => 'evil',
                'description' => 'Persegue il proprio vantaggio senza compassione e senza una particolare preferenza per l’ordine o il caos.',
                'sort_order' => 8,
            ],

            //Caotico Malvagio
            [
                'key' => 'chaotic_evil',
                'name' => 'Caotico Malvagio',
                'abbreviation' => 'CM',
                'ethical_axis' => 'chaotic',
                'moral_axis' => 'evil',
                'description' => 'Agisce con egoismo, crudeltà e violenza, rifiutando regole e limitazioni esterne.',
                'sort_order' => 9,
            ],
        ];

        //Inserisce o aggiorna ogni allineamento usando la chiave stabile
        foreach ($alignments as $alignment) {
            $ruleset->alignments()->updateOrCreate(
                //Identifica univocamente l'allineamento nel regolamento
                [
                    'key' => $alignment['key'],
                ],

                //Aggiorna tutti gli altri dati dell'allineamento
                [
                    'name' => $alignment['name'],
                    'abbreviation' => $alignment['abbreviation'],
                    'ethical_axis' => $alignment['ethical_axis'],
                    'moral_axis' => $alignment['moral_axis'],
                    'description' => $alignment['description'],
                    'sort_order' => $alignment['sort_order'],
                ]
            );
        }
    }
}
