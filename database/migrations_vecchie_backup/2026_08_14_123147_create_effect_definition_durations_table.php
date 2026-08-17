<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_durations', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene la durata
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della regola di durata
            $table->string('key');

            //Modalità con cui termina l'effetto
            $table->enum('duration_type', [
                'instantaneous',
                'fixed',
                'until_source_ends',
                'until_start_turn',
                'until_end_turn',
                'until_save_success',
                'until_condition',
                'permanent',
                'special',
            ]);

            //Quantità della durata
            $table->unsignedSmallInteger('duration_value')
                ->nullable();

            //Unità utilizzata per la durata
            $table->enum('duration_unit', [
                'turn',
                'round',
                'minute',
                'hour',
                'day',
                'other',
            ])->nullable();

            //Creatura rispetto alla quale viene valutato il turno
            $table->enum('turn_reference', [
                'source',
                'target',
                'other',
            ])->nullable();

            //Condizione particolare che determina la durata
            $table->text('condition')
                ->nullable();

            //Ordine di valutazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso effetto
            $table->unique([
                'effect_definition_id',
                'key',
            ], 'effect_definition_durations_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_durations');
    }
};
