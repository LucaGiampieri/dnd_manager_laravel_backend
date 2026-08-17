<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_conditions', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene la condizione
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Condizione interessata
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Operazione effettuata sulla condizione
            $table->enum('operation', [
                'apply',
                'remove',
                'suppress',
                'special',
            ])->default('apply');

            //Condizione necessaria per applicare questa regola
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di applicare due volte la stessa operazione alla stessa condizione nello stesso effetto
            $table->unique([
                'effect_definition_id',
                'condition_id',
                'operation',
            ], 'effect_definition_conditions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_conditions');
    }
};
