<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_checks', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene la prova
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della prova
            $table->string('key');

            //Tipo di prova richiesta
            $table->enum('check_type', [
                'saving_throw',
                'ability_check',
                'skill_check',
                'escape_check',
                'special',
            ]);

            //Caratteristica utilizzata
            $table->foreignId('ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Skill utilizzata
            $table->foreignId('skill_id')
                ->nullable()
                ->constrained('skills')
                ->nullOnDelete();

            //CD fissa della prova
            $table->unsignedSmallInteger('dc')
                ->nullable();

            //Origine della CD
            $table->enum('dc_source_type', [
                'fixed',
                'source_save_dc',
                'caster_spell_save_dc',
                'source_ability_dc',
                'other',
            ])->default('fixed');

            //Momento in cui viene effettuata la prova
            $table->enum('trigger_type', [
                'on_apply',
                'start_turn',
                'end_turn',
                'start_source_turn',
                'end_source_turn',
                'after_damage',
                'action',
                'special',
            ])->default('on_apply');

            //Indica se la prova può essere ripetuta
            $table->boolean('repeatable')
                ->default(false);

            //Indica se richiede un'azione
            $table->boolean('requires_action')
                ->default(false);

            //Numero massimo di tentativi
            $table->unsignedSmallInteger('max_attempts')
                ->nullable();

            //Risultato ottenuto con un successo
            $table->enum('success_result', [
                'avoid_effect',
                'end_effect',
                'remove_condition',
                'escape',
                'half_effect',
                'special',
            ])->default('avoid_effect');

            //Risultato ottenuto con un fallimento
            $table->enum('failure_result', [
                'apply_effect',
                'continue_effect',
                'special',
            ])->default('apply_effect');

            //Condizione necessaria per effettuare la prova
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
            ], 'effect_definition_checks_unique');

            //Velocizza il recupero delle prove
            $table->index([
                'effect_definition_id',
                'check_type',
                'trigger_type',
            ], 'effect_definition_checks_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_checks');
    }
};
