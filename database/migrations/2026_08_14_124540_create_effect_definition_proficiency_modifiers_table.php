<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_proficiency_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained(table: 'effect_definitions', indexName: 'fk_effect_definition_proficiency_modifiers_effect_defin_d6731d9b')
                ->cascadeOnDelete();

            //Tipo di competenza interessata
            $table->enum('proficiency_type', [
                'skill',
                'saving_throw',
                'weapon',
                'armor',
                'tool',
                'other',
            ]);

            //Elemento specifico interessato
            $table->unsignedBigInteger('target_id')
                ->nullable();

            //Indica se la regola riguarda tutti gli elementi del tipo
            $table->boolean('applies_to_all')
                ->default(false);

            //Operazione applicata alla competenza
            $table->enum('operation', [
                'grant',
                'remove',
                'suppress',
            ])->default('grant');

            //Moltiplicatore del bonus di competenza
            $table->decimal('proficiency_multiplier', 3, 2)
                ->nullable();

            //Condizione necessaria per applicare il modificatore
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero delle competenze modificate
            $table->index([
                'effect_definition_id',
                'proficiency_type',
                'target_id',
            ], 'effect_definition_proficiency_modifiers_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_proficiency_modifiers');
    }
};
