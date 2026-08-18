<?php

use App\Models\CreatureTag;
use Database\Seeders\CreatureTagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per evitare interferenze con i dati di altri test
uses(RefreshDatabase::class);

it('crea i tag base delle creature senza duplicati', function () {
    //Esegue due volte il seeder per verificare
    //che non vengano creati tag duplicati
    $this->seed(CreatureTagSeeder::class);
    $this->seed(CreatureTagSeeder::class);

    //Recupera tutti i tag nell’ordine stabilito dal regolamento
    $creatureTags = CreatureTag::query()
        ->orderBy('sort_order')
        ->get();

    //Verifica che siano stati creati esattamente ventisette tag
    expect($creatureTags)->toHaveCount(27);

    //Verifica le chiavi tecniche e il loro ordine
    expect($creatureTags->pluck('key')->all())->toBe([
        'shapechanger',
        'titan',
        'any_race',
        'demon',
        'devil',
        'yugoloth',
        'aarakocra',
        'bullywug',
        'dwarf',
        'elf',
        'gith',
        'gnoll',
        'gnome',
        'goblinoid',
        'grimlock',
        'human',
        'kenku',
        'kobold',
        'kuo_toa',
        'lizardfolk',
        'merfolk',
        'orc',
        'quaggoth',
        'sahuagin',
        'thri_kreen',
        'troglodyte',
        'yuan_ti',
    ]);

    //Verifica i nomi italiani e il loro ordine
    expect($creatureTags->pluck('name')->all())->toBe([
        'Mutaforma',
        'Titano',
        'Qualsiasi razza',
        'Demone',
        'Diavolo',
        'Yugoloth',
        'Aarakocra',
        'Bullywug',
        'Nano',
        'Elfo',
        'Gith',
        'Gnoll',
        'Gnomo',
        'Goblinoide',
        'Grimlock',
        'Umano',
        'Kenku',
        'Coboldo',
        'Kuo-toa',
        'Lucertoloide',
        'Marinide',
        'Orco',
        'Quaggoth',
        'Sahuagin',
        'Thri-kreen',
        'Troglodita',
        'Yuan-ti',
    ]);

    //Verifica che ogni tag possieda una descrizione
    expect(
        CreatureTag::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);
});
