<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge la possibilità di riassegnare i bonus razziali
    public function up(): void
    {
        //Aggiorna i bonus concessi dalle razze principali
        Schema::table(
            'race_ability_bonuses',
            function (Blueprint $table) {
                //Indica se il bonus può essere spostato
                //utilizzando una regola opzionale come quella di Tasha
                $table->boolean('can_be_reassigned')
                    ->default(false)
                    ->after('bonus');
            }
        );

        //Aggiorna i bonus concessi dalle sottorazze
        Schema::table(
            'subrace_ability_bonuses',
            function (Blueprint $table) {
                //Indica se il bonus può essere spostato
                //utilizzando una regola opzionale come quella di Tasha
                $table->boolean('can_be_reassigned')
                    ->default(false)
                    ->after('bonus');
            }
        );
    }

    //Rimuove la possibilità di riassegnare i bonus razziali
    public function down(): void
    {
        //Rimuove il campo dai bonus delle razze
        Schema::table(
            'race_ability_bonuses',
            function (Blueprint $table) {
                $table->dropColumn('can_be_reassigned');
            }
        );

        //Rimuove il campo dai bonus delle sottorazze
        Schema::table(
            'subrace_ability_bonuses',
            function (Blueprint $table) {
                $table->dropColumn('can_be_reassigned');
            }
        );
    }
};
