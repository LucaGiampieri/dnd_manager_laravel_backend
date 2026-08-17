<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_resource_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained(table: 'effect_definitions', indexName: 'fk_effect_definition_resource_modifiers_effect_definiti_da3a9651')
                ->cascadeOnDelete();

            //Risorsa modificata
            $table->foreignId('resource_definition_id')
                ->constrained(table: 'resource_definitions', indexName: 'fk_effect_definition_resource_modifiers_resource_defini_d2d88a8b')
                ->cascadeOnDelete();

            //Valore della risorsa modificato
            $table->enum('target_type', [
                'max_value',
                'recovery_value',
                'die_size',
                'other',
            ])->default('max_value');

            //Operazione applicata al valore
            $table->enum('operation', [
                'add',
                'set',
                'minimum',
                'maximum',
                'multiply',
            ])->default('add');

            //Valore utilizzato dall'operazione
            $table->decimal('value', 10, 3);

            //Valore minimo ottenibile
            $table->decimal('minimum_result', 10, 3)
                ->nullable();

            //Valore massimo ottenibile
            $table->decimal('maximum_result', 10, 3)
                ->nullable();

            //Condizione necessaria per applicare il modificatore
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero dei modificatori di una specifica risorsa
            $table->index([
                'effect_definition_id',
                'resource_definition_id',
                'target_type',
            ], 'effect_definition_resource_modifiers_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_resource_modifiers');
    }
};
