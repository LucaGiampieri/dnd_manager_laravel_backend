<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_life_state_rules', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene la regola
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Momento in cui viene applicata la regola
            $table->enum('trigger_type', [
                'on_apply',
                'on_reduced_to_zero',
                'on_would_die',
                'on_death',
                'on_failed_death_save',
                'on_successful_death_save',
                'special',
            ])->default('on_apply');

            //Operazione applicata allo stato vitale
            $table->enum('operation', [
                'stabilize',
                'revive',
                'prevent_zero_hit_points',
                'prevent_death',
                'set_hit_points',
                'kill',
                'special',
            ]);

            //Punti ferita impostati dalla regola
            $table->unsignedInteger('hit_points_value')
                ->nullable();

            //Tempo massimo trascorso dalla morte
            $table->unsignedInteger('maximum_time_dead_value')
                ->nullable();

            //Unità del tempo massimo trascorso dalla morte
            $table->enum('maximum_time_dead_unit', [
                'round',
                'minute',
                'hour',
                'day',
                'year',
                'other',
            ])->nullable();

            //Indica se i successi e fallimenti dei TS contro morte vengono azzerati
            $table->boolean('reset_death_saves')
                ->default(false);

            //Indica se la regola termina dopo essersi attivata
            $table->boolean('ends_effect_after_trigger')
                ->default(false);

            //Condizione necessaria per applicare la regola
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
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
            ], 'effect_definition_life_state_rules_unique');

            //Velocizza il recupero delle regole in base al momento di attivazione
            $table->index([
                'effect_definition_id',
                'trigger_type',
            ], 'effect_definition_life_state_rules_trigger_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_life_state_rules');
    }
};
