<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

//Collega tutti i test Feature alla classe di base di Laravel
pest()
    ->extend(TestCase::class)

    //Ricrea il database di test per ogni singolo test
    ->use(RefreshDatabase::class)

    //Applica questa configurazione ai file della cartella Feature
    ->in('Feature');
