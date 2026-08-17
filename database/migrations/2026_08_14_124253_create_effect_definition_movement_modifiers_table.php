<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_movement_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained(table: 'effect_definitions', indexName: 'fk_effect_definition_movement_modifiers_effect_definiti_3a94acd0')
                ->cascadeOnDelete();

            //Tipo di movimento interessato
            $table->foreignId('movement_type_id')
                ->nullable()
                ->constrained('movement_types')
                ->nullOnDelete();

            //Indica se la regola riguarda tutti i tipi di movimento
            $table->boolean('applies_to_all_movement_types')
                ->default(false);

            //Operazione applicata al movimento
            $table->enum('operation', [
                'add',
                'set',
                'minimum',
                'maximum',
                'multiply',
                'grant',
                'remove',
            ]);

            //Valore della velocità in metri
            $table->decimal('value', 10, 3)
                ->nullable();

            //Modifica della capacità di restare sospesi
            $table->enum('hover_operation', [
                'none',
                'grant',
                'remove',
            ])->default('none');

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

            //Velocizza il recupero dei modificatori per tipo di movimento
            $table->index([
                'effect_definition_id',
                'movement_type_id',
            ], 'effect_definition_movement_modifiers_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_movement_modifiers');
    }
};
