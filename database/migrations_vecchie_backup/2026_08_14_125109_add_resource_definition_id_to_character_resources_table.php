<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('character_resources', function (Blueprint $table) {

            //Definizione della risorsa posseduta dal personaggio
            $table->foreignId('resource_definition_id')
                ->nullable()
                ->constrained('resource_definitions')
                ->cascadeOnDelete();

            //Evita che il personaggio abbia due stati
            //per la stessa definizione di risorsa
            $table->unique([
                'character_id',
                'resource_definition_id',
            ], 'character_resources_definition_unique');
        });
    }

    public function down(): void
    {
        Schema::table('character_resources', function (Blueprint $table) {

            //Rimuove il vincolo di unicità
            $table->dropUnique(
                'character_resources_definition_unique'
            );

            //Rimuove il collegamento alla definizione della risorsa
            $table->dropConstrainedForeignId(
                'resource_definition_id'
            );
        });
    }
};
