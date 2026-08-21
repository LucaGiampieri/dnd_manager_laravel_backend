<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge il versionamento alle razze e alle sottorazze
    public function up(): void
    {
        //Aggiunge i campi necessari per distinguere
        //le diverse pubblicazioni della stessa razza
        Schema::table('races', function (Blueprint $table) {
            //Identifica la razza indipendentemente dal manuale
            $table->string('canonical_key', 100)
                ->nullable()
                ->after('key');

            //Identifica la specifica versione editoriale
            $table->string('version_key', 100)
                ->default('phb_2014')
                ->after('canonical_key');

            //Indica che questa versione è stata superata
            $table->boolean('is_legacy')
                ->default(false)
                ->after('version_key');
        });

        //Aggiunge gli stessi campi alle sottorazze
        Schema::table('subraces', function (Blueprint $table) {
            //Identifica la sottorazza indipendentemente dal manuale
            $table->string('canonical_key', 100)
                ->nullable()
                ->after('key');

            //Identifica la specifica versione editoriale
            $table->string('version_key', 100)
                ->default('phb_2014')
                ->after('canonical_key');

            //Indica che questa versione è stata superata
            $table->boolean('is_legacy')
                ->default(false)
                ->after('version_key');
        });

        //Assegna alle razze esistenti la loro chiave canonica
        DB::table('races')->update([
            'canonical_key' => DB::raw('`key`'),
        ]);

        //Assegna alle sottorazze esistenti la loro chiave canonica
        DB::table('subraces')->update([
            'canonical_key' => DB::raw('`key`'),
        ]);

        //Rende obbligatoria la chiave canonica delle razze
        Schema::table('races', function (Blueprint $table) {
            $table->string('canonical_key', 100)
                ->nullable(false)
                ->change();
        });

        //Rende obbligatoria la chiave canonica delle sottorazze
        Schema::table('subraces', function (Blueprint $table) {
            $table->string('canonical_key', 100)
                ->nullable(false)
                ->change();
        });

        //Aggiunge gli indici di ricerca e il vincolo
        //che impedisce di duplicare la stessa versione
        Schema::table('races', function (Blueprint $table) {
            $table->index(
                [
                    'ruleset_id',
                    'canonical_key',
                ],
                'races_ruleset_canonical_index'
            );

            $table->unique(
                [
                    'ruleset_id',
                    'canonical_key',
                    'version_key',
                ],
                'races_ruleset_canonical_version_unique'
            );
        });

        //Aggiunge gli indici equivalenti alle sottorazze
        Schema::table('subraces', function (Blueprint $table) {
            $table->index(
                [
                    'race_id',
                    'canonical_key',
                ],
                'subraces_race_canonical_index'
            );

            $table->unique(
                [
                    'race_id',
                    'canonical_key',
                    'version_key',
                ],
                'subraces_race_canonical_version_unique'
            );
        });
    }

    //Rimuove il versionamento ripristinando la struttura precedente
    public function down(): void
    {
        //Rimuove prima gli indici collegati alle nuove colonne
        Schema::table('subraces', function (Blueprint $table) {
            $table->dropUnique(
                'subraces_race_canonical_version_unique'
            );

            $table->dropIndex(
                'subraces_race_canonical_index'
            );
        });

        //Rimuove gli indici di versionamento delle razze
        Schema::table('races', function (Blueprint $table) {
            $table->dropUnique(
                'races_ruleset_canonical_version_unique'
            );

            $table->dropIndex(
                'races_ruleset_canonical_index'
            );
        });

        //Rimuove i campi aggiunti alle sottorazze
        Schema::table('subraces', function (Blueprint $table) {
            $table->dropColumn([
                'canonical_key',
                'version_key',
                'is_legacy',
            ]);
        });

        //Rimuove i campi aggiunti alle razze
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn([
                'canonical_key',
                'version_key',
                'is_legacy',
            ]);
        });
    }
};
