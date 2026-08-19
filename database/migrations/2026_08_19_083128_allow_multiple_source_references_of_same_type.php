<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    //Allinea la tabella esistente alla struttura definitiva
    public function up(): void
    {
        //Controlla quali colonne non sono ancora presenti
        $missingColumns = [
            'key' => ! Schema::hasColumn(
                'source_references',
                'key'
            ),
            'is_primary' => ! Schema::hasColumn(
                'source_references',
                'is_primary'
            ),
            'sort_order' => ! Schema::hasColumn(
                'source_references',
                'sort_order'
            ),
            'official_text' => ! Schema::hasColumn(
                'source_references',
                'official_text'
            ),
        ];

        //Aggiunge soltanto le colonne mancanti
        if (in_array(true, $missingColumns, true)) {
            Schema::table(
                'source_references',
                function (Blueprint $table) use ($missingColumns) {
                    //Chiave tecnica stabile del riferimento
                    if ($missingColumns['key']) {
                        $table->string('key', 100);
                    }

                    //Indica la fonte principale del contenuto
                    if ($missingColumns['is_primary']) {
                        $table->boolean('is_primary')
                            ->default(false);
                    }

                    //Ordine di visualizzazione dei riferimenti
                    if ($missingColumns['sort_order']) {
                        $table->unsignedSmallInteger('sort_order')
                            ->default(0);
                    }

                    //Testo ufficiale privato non inserito nei seeder
                    if ($missingColumns['official_text']) {
                        $table->longText('official_text')
                            ->nullable();
                    }
                }
            );
        }

        //Recupera gli indici dopo aver sistemato le colonne
        $indexNames = array_column(
            Schema::getIndexes('source_references'),
            'name'
        );

        //Rimuove il vecchio vincolo se è ancora presente
        if (
            in_array(
                'source_references_unique',
                $indexNames,
                true
            )
        ) {
            Schema::table(
                'source_references',
                function (Blueprint $table) {
                    //Il vecchio vincolo impediva più riferimenti
                    //dello stesso tipo nello stesso manuale
                    $table->dropUnique(
                        'source_references_unique'
                    );
                }
            );
        }

        //Aggiunge il vincolo basato sulla chiave tecnica
        if (
            ! in_array(
                'source_references_sourceable_key_unique',
                $indexNames,
                true
            )
        ) {
            Schema::table(
                'source_references',
                function (Blueprint $table) {
                    //Una chiave può essere usata una sola volta
                    //per ciascun contenuto
                    $table->unique(
                        [
                            'sourceable_type',
                            'sourceable_id',
                            'key',
                        ],
                        'source_references_sourceable_key_unique'
                    );
                }
            );
        }
    }

    //Ripristina il precedente limite per tipo e manuale
    public function down(): void
    {
        //Recupera gli indici presenti nel database
        $indexNames = array_column(
            Schema::getIndexes('source_references'),
            'name'
        );

        //Ripristina il vecchio vincolo soltanto se manca
        if (
            ! in_array(
                'source_references_unique',
                $indexNames,
                true
            )
        ) {
            Schema::table(
                'source_references',
                function (Blueprint $table) {
                    //Permette un solo riferimento per tipo,
                    //contenuto e manuale
                    $table->unique(
                        [
                            'source_book_id',
                            'sourceable_type',
                            'sourceable_id',
                            'reference_type',
                        ],
                        'source_references_unique'
                    );
                }
            );
        }
    }
};
