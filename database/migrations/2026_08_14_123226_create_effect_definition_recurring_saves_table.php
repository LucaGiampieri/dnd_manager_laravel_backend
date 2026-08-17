<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_recurring_saves', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il tiro salvezza
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica del tiro salvezza
            $table->string('key');

            //Caratteristica utilizzata per il tiro salvezza
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //CD fissa del tiro salvezza
            $table->unsignedSmallInteger('save_dc')
                ->nullable();

            //Origine della CD quando non è fissa
            $table->enum('dc_source_type', [
                'fixed',
                'source_save_dc',
                'caster_spell_save_dc',
                'other',
            ])->default('fixed');

            //Momento in cui viene ripetuto il tiro salvezza
            $table->enum('trigger_type', [
                'start_turn',
                'end_turn',
                'start_source_turn',
                'end_source_turn',
                'after_damage',
                'special',
            ]);

            //Risultato di un tiro salvezza superato
            $table->enum('success_result', [
                'end_effect',
                'remove_condition',
                'suppress_effect',
                'special',
            ])->default('end_effect');

            //Risultato di un tiro salvezza fallito
            $table->enum('failure_result', [
                'continue_effect',
                'special',
            ])->default('continue_effect');

            //Numero massimo di tentativi consentiti
            $table->unsignedSmallInteger('max_attempts')
                ->nullable();

            //Condizione necessaria per effettuare il tiro salvezza
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
            ], 'effect_definition_recurring_saves_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_recurring_saves');
    }
};
