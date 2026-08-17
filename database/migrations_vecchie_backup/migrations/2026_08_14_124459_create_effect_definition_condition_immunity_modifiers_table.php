<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_condition_immunity_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Condizione interessata
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Operazione applicata all'immunità
            $table->enum('operation', [
                'grant',
                'remove',
                'suppress',
            ])->default('grant');

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

            //Evita di applicare due volte la stessa operazione alla stessa immunità
            $table->unique([
                'effect_definition_id',
                'condition_id',
                'operation',
            ], 'effect_definition_condition_immunities_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_condition_immunity_modifiers');
    }
};
