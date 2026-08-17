<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_ability_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Caratteristica modificata
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Parte della caratteristica modificata
            $table->enum('target_type', [
                'score',
                'modifier',
            ])->default('score');

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

            //Velocizza il recupero dei modificatori della stessa caratteristica
            $table->index([
                'effect_definition_id',
                'ability_id',
                'target_type',
            ], 'effect_definition_ability_modifiers_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_ability_modifiers');
    }
};
