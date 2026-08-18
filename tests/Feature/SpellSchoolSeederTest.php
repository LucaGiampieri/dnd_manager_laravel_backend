<?php

use App\Models\SpellSchool;
use Database\Seeders\SpellSchoolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Ricrea il database di test prima di ogni test
//per evitare interferenze con scuole già presenti
uses(RefreshDatabase::class);

it('crea le otto scuole di magia senza duplicati', function () {
    //Esegue due volte il seeder per verificare
    //che non vengano create scuole duplicate
    $this->seed(SpellSchoolSeeder::class);
    $this->seed(SpellSchoolSeeder::class);

    //Verifica che siano state create esattamente otto scuole
    expect(SpellSchool::query()->count())->toBe(8);

    //Verifica i nomi italiani ordinandoli alfabeticamente
    expect(
        SpellSchool::query()
            ->orderBy('name')
            ->pluck('name')
            ->all()
    )->toBe([
        'Abiurazione',
        'Ammaliamento',
        'Divinazione',
        'Evocazione',
        'Illusione',
        'Invocazione',
        'Necromanzia',
        'Trasmutazione',
    ]);

    //Verifica che ogni scuola di magia possieda una descrizione
    expect(
        SpellSchool::query()
            ->whereNull('description')
            ->count()
    )->toBe(0);
});
