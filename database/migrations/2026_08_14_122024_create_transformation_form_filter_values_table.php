<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transformation_form_filter_values', function (Blueprint $table) {
            $table->id();

            //Filtro a cui appartiene il valore dinamico
            $table->foreignId('transformation_form_filter_id')
                ->constrained(table: 'transformation_form_filters', indexName: 'fk_transformation_form_filter_values_transformation_for_3f4ab813')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Dato che determina quando questa regola è attiva
            $table->enum('activation_source_type', [
                'always',
                'character_level',
                'class_level',
                'target_level',
                'target_challenge_rating',
                'target_level_or_challenge_rating',
                'other',
            ])->default('always');

            //Classe utilizzata quando la sorgente è il livello di classe
            $table->foreignId('activation_class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            //Valore minimo della sorgente per attivare la regola
            $table->decimal('minimum_source', 10, 3)
                ->nullable();

            //Valore massimo della sorgente per attivare la regola
            $table->decimal('maximum_source', 10, 3)
                ->nullable();

            //Modalità con cui viene ottenuto il valore del filtro
            $table->enum('value_type', [
                'none',
                'fixed',
                'source',
            ])->default('fixed');

            //Dato utilizzato come valore dinamico del filtro
            $table->enum('value_source_type', [
                'character_level',
                'class_level',
                'target_level',
                'target_challenge_rating',
                'target_level_or_challenge_rating',
                'source_challenge_rating',
                'other',
            ])->nullable();

            //Classe utilizzata per calcolare il valore dinamico
            $table->foreignId('value_source_class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            //Valore fisso utilizzato dal filtro
            $table->decimal('fixed_value', 10, 3)
                ->nullable();

            //Valore sottratto alla sorgente prima del calcolo
            $table->decimal('source_offset', 10, 3)
                ->default(0);

            //Moltiplicatore applicato alla sorgente
            $table->decimal('multiplier', 10, 3)
                ->default(1);

            //Divisore applicato alla sorgente
            $table->decimal('divisor', 10, 3)
                ->default(1);

            //Valore fisso aggiunto al risultato
            $table->decimal('flat_value', 10, 3)
                ->default(0);

            //Tipo di arrotondamento del risultato
            $table->enum('rounding', [
                'none',
                'floor',
                'ceil',
                'round',
            ])->default('none');

            //Condizione particolare della regola
            $table->text('condition')
                ->nullable();

            //Ordine di valutazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso filtro
            $table->unique([
                'transformation_form_filter_id',
                'key',
            ], 'transformation_form_filter_values_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_form_filter_values');
    }
};
