<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_healing_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Tipo di guarigione interessata
            $table->enum('healing_type', [
                'hit_points',
                'temporary_hit_points',
                'all',
                'other',
            ])->default('hit_points');

            //Operazione applicata alla guarigione ricevuta
            $table->enum('operation', [
                'add',
                'subtract',
                'multiply',
                'minimum',
                'maximum',
                'prevent',
                'special',
            ]);

            //Valore utilizzato dall'operazione
            $table->decimal('value', 10, 3)
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

            //Evita chiavi duplicate nello stesso effetto
            $table->unique([
                'effect_definition_id',
                'key',
            ], 'effect_definition_healing_modifiers_unique');

            //Velocizza il recupero dei modificatori per tipo di guarigione
            $table->index([
                'effect_definition_id',
                'healing_type',
            ], 'effect_definition_healing_modifiers_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_healing_modifiers');
    }
};
