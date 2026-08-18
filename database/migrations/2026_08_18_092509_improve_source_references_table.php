<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Aggiunge i dati necessari per identificare e ordinare le fonti
    public function up(): void
    {
        Schema::table('source_references', function (Blueprint $table) {
            //Chiave tecnica stabile del riferimento
            $table->string('key', 100);

            //Indica se questa è la fonte principale del contenuto
            $table->boolean('is_primary')
                ->default(false);

            //Ordine con cui mostrare i riferimenti del contenuto
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Testo ufficiale privato, mai inserito nei seeder pubblici
            $table->longText('official_text')
                ->nullable();

            //Impedisce di duplicare la stessa chiave
            //nei riferimenti dello stesso contenuto
            $table->unique(
                [
                    'sourceable_type',
                    'sourceable_id',
                    'key',
                ],
                'source_references_sourceable_key_unique'
            );
        });
    }

    //Ripristina la struttura precedente della tabella
    public function down(): void
    {
        Schema::table('source_references', function (Blueprint $table) {
            //Rimuove l'indice prima delle colonne che utilizza
            $table->dropUnique(
                'source_references_sourceable_key_unique'
            );

            //Rimuove i campi aggiunti dalla migrazione
            $table->dropColumn([
                'key',
                'is_primary',
                'sort_order',
                'official_text',
            ]);
        });
    }
};
