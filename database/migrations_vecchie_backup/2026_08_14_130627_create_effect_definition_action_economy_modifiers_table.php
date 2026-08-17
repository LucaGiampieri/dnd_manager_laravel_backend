<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_action_economy_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Tipo di azione interessata
            $table->enum('action_type', [
                'action',
                'bonus_action',
                'reaction',
                'object_interaction',
                'legendary_action',
                'other',
            ]);

            //Operazione applicata al numero di azioni disponibili
            $table->enum('operation', [
                'add',
                'set',
                'minimum',
                'maximum',
                'suppress',
                'special',
            ]);

            //Numero utilizzato dall'operazione
            $table->unsignedSmallInteger('value')
                ->nullable();

            //Limitazioni sulle azioni utilizzabili
            $table->text('restriction')
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
            ], 'effect_definition_action_economy_unique');

            //Velocizza il recupero delle modifiche per tipo di azione
            $table->index([
                'effect_definition_id',
                'action_type',
            ], 'effect_definition_action_economy_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'effect_definition_action_economy_modifiers'
        );
    }
};
