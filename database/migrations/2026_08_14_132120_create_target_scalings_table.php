<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('target_scalings', function (Blueprint $table) {
            $table->id();

            //Regola di bersaglio che viene modificata
            $table->foreignId('target_id')
                ->constrained('targets')
                ->cascadeOnDelete();

            //Chiave tecnica della progressione
            $table->string('key');

            //Campo della regola di bersaglio modificato
            $table->enum('target_field', [
                'minimum_targets',
                'maximum_targets',
                'range',
                'other',
            ]);

            //Origine del valore utilizzato
            $table->enum('source_type', [
                'spell_slot_level',
                'spell_level',
                'character_level',
                'class_level',
                'proficiency_bonus',
                'ability_score',
                'ability_modifier',
                'fixed',
                'other',
            ]);

            //Classe usata quando la regola dipende dal livello di classe
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            //Caratteristica usata dalla regola
            $table->foreignId('ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Operazione applicata al valore
            $table->enum('operation', [
                'add',
                'set',
                'multiply',
                'minimum',
                'maximum',
            ])->default('add');

            //Valore minimo della sorgente per applicare la regola
            $table->decimal('minimum_source', 10, 3)
                ->nullable();

            //Valore massimo della sorgente per applicare la regola
            $table->decimal('maximum_source', 10, 3)
                ->nullable();

            //Valore fisso utilizzato dalla regola
            $table->decimal('fixed_value', 10, 3)
                ->nullable();

            //Valore aggiunto alla sorgente prima del calcolo
            $table->decimal('source_offset', 10, 3)
                ->default(0);

            //Moltiplicatore applicato alla sorgente
            $table->decimal('multiplier', 10, 3)
                ->default(1);

            //Divisore applicato alla sorgente
            $table->decimal('divisor', 10, 3)
                ->default(1);

            //Valore aggiunto dopo il calcolo
            $table->decimal('flat_value', 10, 3)
                ->default(0);

            //Modalità di arrotondamento
            $table->enum('rounding', [
                'none',
                'floor',
                'ceil',
                'round',
            ])->default('none');

            //Valore minimo ottenibile
            $table->decimal('minimum_result', 10, 3)
                ->nullable();

            //Valore massimo ottenibile
            $table->decimal('maximum_result', 10, 3)
                ->nullable();

            //Condizione necessaria per applicare la progressione
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'target_id',
                'key',
            ], 'target_scalings_unique');

            $table->index([
                'target_id',
                'target_field',
            ], 'target_scalings_field_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_scalings');
    }
};
