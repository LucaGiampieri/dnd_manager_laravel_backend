<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Sostituisce i valori liberi con il catalogo dei gradi di sfida
    public function up(): void
    {
        //Aggiunge il collegamento e l'eventuale eccezione dei PE
        Schema::table(
            'creature_stat_blocks',
            function (Blueprint $table) {
                //Grado di sfida ufficiale collegato allo stat block
                $table->foreignId('challenge_rating_id')
                    ->nullable()
                    ->after('size_id')
                    ->constrained('challenge_ratings')
                    ->nullOnDelete();

                //PE differenti dal valore normalmente previsto dal GS
                $table->unsignedInteger('experience_points_override')
                    ->nullable()
                    ->after('challenge_rating_id');
            }
        );

        //Rinomina il bonus manuale per chiarire che è un'eccezione
        Schema::table(
            'creature_stat_blocks',
            function (Blueprint $table) {
                $table->renameColumn(
                    'proficiency_bonus',
                    'proficiency_bonus_override'
                );
            }
        );

        //Rimuove il vecchio grado di sfida numerico senza vincoli
        Schema::table(
            'creature_stat_blocks',
            function (Blueprint $table) {
                $table->dropColumn('challenge_rating');
            }
        );
    }

    //Ripristina i precedenti campi numerici
    public function down(): void
    {
        //Ricrea il precedente campo numerico del grado di sfida
        Schema::table(
            'creature_stat_blocks',
            function (Blueprint $table) {
                $table->decimal('challenge_rating', 6, 3)
                    ->nullable()
                    ->after('alignment_mode');
            }
        );

        //Ripristina il nome originale del bonus di competenza
        Schema::table(
            'creature_stat_blocks',
            function (Blueprint $table) {
                $table->renameColumn(
                    'proficiency_bonus_override',
                    'proficiency_bonus'
                );
            }
        );

        //Rimuove il collegamento e l'eccezione dei PE
        Schema::table(
            'creature_stat_blocks',
            function (Blueprint $table) {
                //Rimuove prima il vincolo della chiave esterna
                $table->dropForeign([
                    'challenge_rating_id',
                ]);

                //Rimuove le colonne introdotte dalla migrazione
                $table->dropColumn([
                    'challenge_rating_id',
                    'experience_points_override',
                ]);
            }
        );
    }
};
