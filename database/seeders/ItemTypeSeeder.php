<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    //Inserisce tutte le tipologie utilizzate dal catalogo degli oggetti
    public function run(): void
    {
        //Definisce tipologie comuni, strumenti, tesori
        //e categorie degli oggetti magici
        $itemTypes = [
            'weapon' => [
                'name' => 'Arma',
                'description' =>
                    'Oggetto utilizzato per effettuare attacchi '
                    . 'in mischia o a distanza.',
            ],
            'armor' => [
                'name' => 'Armatura',
                'description' =>
                    'Equipaggiamento indossato per determinare '
                    . 'o migliorare la Classe Armatura.',
            ],
            'shield' => [
                'name' => 'Scudo',
                'description' =>
                    'Oggetto impugnato per aumentare '
                    . 'la Classe Armatura.',
            ],
            'ammunition' => [
                'name' => 'Munizione',
                'description' =>
                    'Oggetto consumato normalmente da un’arma '
                    . 'che utilizza munizioni.',
            ],
            'adventuring_gear' => [
                'name' => 'Equipaggiamento d’avventura',
                'description' =>
                    'Oggetto generico utilizzato durante '
                    . 'viaggi, esplorazioni e avventure.',
            ],
            'container' => [
                'name' => 'Contenitore',
                'description' =>
                    'Oggetto utilizzato per trasportare '
                    . 'o conservare altri materiali.',
            ],
            'clothing' => [
                'name' => 'Abbigliamento',
                'description' =>
                    'Indumento o insieme di indumenti '
                    . 'utilizzato da un personaggio.',
            ],
            'artisan_tool' => [
                'name' => 'Strumento da artigiano',
                'description' =>
                    'Strumento professionale associato '
                    . 'a un mestiere artigianale.',
            ],
            'gaming_set' => [
                'name' => 'Set da gioco',
                'description' =>
                    'Insieme di oggetti utilizzato '
                    . 'per praticare un gioco.',
            ],
            'musical_instrument' => [
                'name' => 'Strumento musicale',
                'description' =>
                    'Strumento utilizzato per eseguire musica.',
            ],
            'kit' => [
                'name' => 'Kit',
                'description' =>
                    'Insieme di strumenti specializzati '
                    . 'destinato a una particolare attività.',
            ],
            'other_tool' => [
                'name' => 'Altro strumento',
                'description' =>
                    'Strumento che non appartiene alle categorie '
                    . 'artigianali, musicali o di gioco.',
            ],
            'spellcasting_focus' => [
                'name' => 'Focus da incantatore',
                'description' =>
                    'Oggetto utilizzato come focus '
                    . 'per il lancio degli incantesimi.',
            ],
            'holy_symbol' => [
                'name' => 'Simbolo sacro',
                'description' =>
                    'Oggetto religioso utilizzabile '
                    . 'come focus sacro.',
            ],
            'mount' => [
                'name' => 'Cavalcatura',
                'description' =>
                    'Creatura acquistabile e utilizzabile '
                    . 'per il trasporto.',
            ],
            'vehicle' => [
                'name' => 'Veicolo',
                'description' =>
                    'Mezzo utilizzato per il trasporto '
                    . 'terrestre, acquatico o aereo.',
            ],
            'tack_and_harness' => [
                'name' => 'Finimento',
                'description' =>
                    'Equipaggiamento destinato a cavalcature '
                    . 'e veicoli trainati.',
            ],
            'trade_good' => [
                'name' => 'Merce commerciale',
                'description' =>
                    'Bene utilizzato principalmente '
                    . 'per il commercio e lo scambio.',
            ],
            'food_and_drink' => [
                'name' => 'Vitto e bevanda',
                'description' =>
                    'Cibo, bevanda o razione acquistabile.',
            ],
            'poison' => [
                'name' => 'Veleno',
                'description' =>
                    'Sostanza capace di applicare danni '
                    . 'o altri effetti nocivi.',
            ],
            'explosive' => [
                'name' => 'Esplosivo',
                'description' =>
                    'Oggetto che produce un’esplosione '
                    . 'o un effetto distruttivo simile.',
            ],
            'potion' => [
                'name' => 'Pozione',
                'description' =>
                    'Preparato magico o alchemico '
                    . 'normalmente consumato durante l’utilizzo.',
            ],
            'scroll' => [
                'name' => 'Pergamena',
                'description' =>
                    'Supporto scritto che contiene '
                    . 'un incantesimo o un altro effetto.',
            ],
            'wand' => [
                'name' => 'Bacchetta',
                'description' =>
                    'Oggetto magico sottile utilizzato '
                    . 'per produrre effetti specifici.',
            ],
            'rod' => [
                'name' => 'Verga',
                'description' =>
                    'Oggetto magico rigido dotato '
                    . 'di proprietà particolari.',
            ],
            'staff' => [
                'name' => 'Bastone magico',
                'description' =>
                    'Bastone che possiede capacità '
                    . 'o poteri magici.',
            ],
            'ring' => [
                'name' => 'Anello',
                'description' =>
                    'Anello che può possedere '
                    . 'proprietà magiche.',
            ],
            'wondrous_item' => [
                'name' => 'Oggetto meraviglioso',
                'description' =>
                    'Oggetto magico che non appartiene '
                    . 'alle altre categorie specifiche.',
            ],
            'gemstone' => [
                'name' => 'Gemma',
                'description' =>
                    'Pietra preziosa utilizzabile come tesoro, '
                    . 'componente o merce.',
            ],
            'art_object' => [
                'name' => 'Oggetto d’arte',
                'description' =>
                    'Oggetto prezioso il cui valore dipende '
                    . 'anche dalla lavorazione artistica.',
            ],
            'treasure' => [
                'name' => 'Tesoro',
                'description' =>
                    'Oggetto di valore non appartenente '
                    . 'a una categoria più specifica.',
            ],
        ];

        //Inserisce o aggiorna ogni tipologia senza duplicarla
        $sortOrder = 10;

        foreach ($itemTypes as $key => $itemType) {
            ItemType::query()->updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'name' => $itemType['name'],
                    'description' => $itemType['description'],
                    'sort_order' => $sortOrder,
                ]
            );

            //Prepara l'ordinamento della tipologia successiva
            $sortOrder += 10;
        }
    }
}
