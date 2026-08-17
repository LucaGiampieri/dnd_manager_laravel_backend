<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('capability_rules', function (Blueprint $table) {
            $table->id();

            //Elemento a cui appartiene la regola
            $table->morphs('capabilityable');

            //Chiave tecnica della regola
            $table->string('key');

            //Capacità interessata
            $table->enum('capability_type', [
                'spellcasting',
                'concentration',
                'speech',
                'breathing',
                'verbal_components',
                'somatic_components',
                'material_components',
                'feature_use',
                'action_use',
                'bonus_action_use',
                'reaction_use',
                'object_interaction',
                'equipment_use',
                'other',
            ]);

            //Regola applicata alla capacità
            $table->enum('rule_type', [
                'allow',
                'disallow',
                'retain',
                'conditional',
                'special',
            ]);

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

            //Evita chiavi duplicate sullo stesso elemento
            $table->unique([
                'capabilityable_type',
                'capabilityable_id',
                'key',
            ], 'capability_rules_source_key_unique');

            //Velocizza il recupero delle regole per tipo di capacità
            $table->index([
                'capabilityable_type',
                'capabilityable_id',
                'capability_type',
            ], 'capability_rules_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capability_rules');
    }
};
