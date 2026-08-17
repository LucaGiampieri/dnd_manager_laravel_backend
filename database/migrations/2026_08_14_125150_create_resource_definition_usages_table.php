<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('resource_definition_usages', function (Blueprint $table) {
            $table->id();

            //Risorsa utilizzata
            $table->foreignId('resource_definition_id')
                ->constrained('resource_definitions')
                ->cascadeOnDelete();

            //Elemento che utilizza la risorsa
            $table->morphs('resourceable', 'ix_resource_definition_usages_resourceable_type_resourc_bc2dbf18');

            //Costo della risorsa per ogni utilizzo
            $table->unsignedInteger('cost')
                ->default(1);

            //Modalità con cui la risorsa viene utilizzata
            $table->enum('usage_type', [
                'consume',
                'require',
                'restore',
                'special',
            ])->default('consume');

            //Quantità ripristinata
            $table->unsignedInteger('restore_value')
                ->nullable();

            //Condizione necessaria per utilizzare la risorsa
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita collegamenti duplicati
            $table->unique([
                'resource_definition_id',
                'resourceable_type',
                'resourceable_id',
                'usage_type',
            ], 'resource_definition_usages_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_definition_usages');
    }
};
