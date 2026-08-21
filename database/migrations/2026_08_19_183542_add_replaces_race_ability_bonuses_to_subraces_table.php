<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge il controllo sull'eredità dei bonus razziali
    public function up(): void
    {
        Schema::table('subraces', function (Blueprint $table) {
            //Indica che i bonus della sottorazza sostituiscono
            //completamente quelli definiti dalla razza principale
            $table->boolean('replaces_race_ability_bonuses')
                ->default(false)
                ->after('is_variant');
        });
    }

    //Rimuove il controllo sull'eredità dei bonus razziali
    public function down(): void
    {
        Schema::table('subraces', function (Blueprint $table) {
            //Rimuove il campo aggiunto dalla migrazione
            $table->dropColumn(
                'replaces_race_ability_bonuses'
            );
        });
    }
};
