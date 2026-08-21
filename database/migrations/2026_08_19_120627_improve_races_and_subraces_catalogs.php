<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Migliora l'identificazione e la gestione delle razze
    public function up(): void
    {
        //Aggiunge ordinamento e regole di selezione alle razze
        Schema::table('races', function (Blueprint $table) {
            //Indica se la razza può essere scelta normalmente
            $table->boolean('selectable')
                ->default(true)
                ->after('can_replace_race');

            //Indica se la scelta richiede il permesso del DM
            $table->boolean('requires_dm_permission')
                ->default(false)
                ->after('selectable');

            //Definisce l'ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0)
                ->after('typical_alignment');
        });

        //Aggiunge identità stabile e regole di selezione alle sottorazze
        Schema::table('subraces', function (Blueprint $table) {
            //Chiave tecnica stabile usata da seeder e API
            $table->string('key')
                ->after('race_id');

            //Tendenza di allineamento specifica della sottorazza
            $table->string('typical_alignment')
                ->nullable()
                ->after('name');

            //Indica una variante alternativa della razza principale
            $table->boolean('is_variant')
                ->default(false)
                ->after('typical_alignment');

            //Indica se la sottorazza può essere scelta normalmente
            $table->boolean('selectable')
                ->default(true)
                ->after('is_variant');

            //Indica se la scelta richiede il permesso del DM
            $table->boolean('requires_dm_permission')
                ->default(false)
                ->after('selectable');

            //Definisce l'ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0)
                ->after('requires_dm_permission');

            //Impedisce chiavi duplicate nella stessa razza
            $table->unique(
                [
                    'race_id',
                    'key',
                ],
                'subraces_race_key_unique'
            );
        });
    }

    //Ripristina la struttura precedente
    public function down(): void
    {
        //Rimuove prima il vincolo basato sulla chiave
        Schema::table('subraces', function (Blueprint $table) {
            $table->dropUnique(
                'subraces_race_key_unique'
            );

            //Rimuove i campi aggiunti alle sottorazze
            $table->dropColumn([
                'key',
                'typical_alignment',
                'is_variant',
                'selectable',
                'requires_dm_permission',
                'sort_order',
            ]);
        });

        //Rimuove i campi aggiunti alle razze
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn([
                'selectable',
                'requires_dm_permission',
                'sort_order',
            ]);
        });
    }
};
