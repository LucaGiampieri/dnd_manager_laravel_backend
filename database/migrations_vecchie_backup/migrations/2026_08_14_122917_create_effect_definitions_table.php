<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definitions', function (Blueprint $table) {
            $table->id();

            //Elemento che genera o concede l'effetto
            $table->morphs('source');

            //Chiave tecnica dell'effetto
            $table->string('key');

            //Nome dell'effetto
            $table->string('name')
                ->nullable();

            //Momento in cui viene applicato l'effetto
            $table->enum('application_type', [
                'automatic',
                'on_hit',
                'failed_save',
                'successful_save',
                'on_damage',
                'on_start_turn',
                'on_end_turn',
                'on_enter_area',
                'on_leave_area',
                'manual',
                'special',
            ])->default('automatic');

            //Indica se l'effetto termina insieme alla sua sorgente
            $table->boolean('ends_with_source')
                ->default(true);

            //Condizione necessaria per applicare l'effetto
            $table->text('condition')
                ->nullable();

            //Descrizione completa dell'effetto
            $table->text('description')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate per la stessa sorgente
            $table->unique([
                'source_type',
                'source_id',
                'key',
            ], 'effect_definitions_source_key_unique');

            //Velocizza il recupero degli effetti della sorgente
            $table->index([
                'source_type',
                'source_id',
                'sort_order',
            ], 'effect_definitions_source_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definitions');
    }
};
