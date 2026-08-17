<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_size_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Operazione applicata alla taglia
            $table->enum('operation', [
                'set',
                'increase_steps',
                'decrease_steps',
                'minimum',
                'maximum',
                'special',
            ]);

            //Taglia specifica utilizzata dalla regola
            $table->foreignId('size_id')
                ->nullable()
                ->constrained('sizes')
                ->nullOnDelete();

            //Numero di categorie di taglia da modificare
            $table->unsignedTinyInteger('steps')
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
            ], 'effect_definition_size_modifiers_unique');

            //Velocizza il recupero delle modifiche alla taglia
            $table->index([
                'effect_definition_id',
                'operation',
            ], 'effect_definition_size_modifiers_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_size_modifiers');
    }
};
