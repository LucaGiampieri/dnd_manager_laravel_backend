<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_resources', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede questa risorsa
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Definizione della risorsa posseduta dal personaggio
            $table->foreignId('resource_definition_id')
                ->constrained('resource_definitions')
                ->cascadeOnDelete();

            //Quantità massima della risorsa
            $table->unsignedInteger('max_value')
                ->default(0);

            //Quantità attualmente disponibile
            $table->unsignedInteger('current_value')
                ->default(0);

            //Numero effettivo di dadi della risorsa per il personaggio
            $table->unsignedSmallInteger('dice_count')
                ->nullable();

            //Numero effettivo di facce del dado della risorsa
            $table->unsignedSmallInteger('die_size')
                ->nullable();

            //Eventuali note specifiche del personaggio
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di creare due stati per la stessa risorsa dello stesso personaggio
            $table->unique([
                'character_id',
                'resource_definition_id',
            ], 'character_resources_definition_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_resources');
    }
};
