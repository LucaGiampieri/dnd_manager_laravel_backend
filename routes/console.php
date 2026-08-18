<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

//Registra il comando dimostrativo "php artisan inspire"
Artisan::command('inspire', function () {
    //Mostra nel terminale una citazione casuale
    $this->comment(Inspiring::quote());
})
    ->purpose('Mostra una citazione motivazionale');
