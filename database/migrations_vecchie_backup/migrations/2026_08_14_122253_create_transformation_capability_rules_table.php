<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transformation_capability_rules', function (Blueprint $table) {
            $table->id();

            //Trasformazione a cui appartiene la regola
            $table->foreignId('transformation_id')
                ->constrained('transformations')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Capacità interessata dalla trasformazione
            $table->enum('capability_type', [
                'spellcasting',
                'concentration',
                'speech',
                'verbal_components',
                'somatic_components',
                'material_components',
                'feature_use',
                'action_use',
                'reaction_use',
                'bonus_action_use',
                'object_interaction',
                'equipment_use',
                'other',
            ]);

            //Comportamento della capacità durante la trasformazione
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

            //Evita chiavi duplicate nella stessa trasformazione
            $table->unique([
                'transformation_id',
                'key',
            ], 'transformation_capability_rules_unique');

            //Velocizza il recupero delle regole per capacità
            $table->index([
                'transformation_id',
                'capability_type',
            ], 'transformation_capability_rules_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_capability_rules');
    }
};
