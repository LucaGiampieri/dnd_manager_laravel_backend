<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('source_references', function (Blueprint $table) {
            //Chiave tecnica del riferimento
            $table->string('key', 100);

            //Indica la fonte principale del contenuto
            $table->boolean('is_primary')
                ->default(false);

            //Ordine dei riferimenti
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Testo ufficiale privato, mai inserito nei seeder pubblici
            $table->longText('official_text')
                ->nullable();

            //Impedisce chiavi duplicate per lo stesso contenuto
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

    public function down(): void
    {
        Schema::table('source_references', function (Blueprint $table) {
            $table->dropUnique(
                'source_references_sourceable_key_unique'
            );

            $table->dropColumn([
                'key',
                'is_primary',
                'sort_order',
                'official_text',
            ]);
        });
    }
};
