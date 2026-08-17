<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_creature_type_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Tipo di creatura interessato
            $table->foreignId('creature_type_id')
                ->constrained('creature_types')
                ->cascadeOnDelete();

            //Modalità con cui viene considerato il tipo
            $table->enum('application_mode', [
                'actual_type',
                'treated_as',
            ])->default('actual_type');

            //Operazione applicata al tipo
            $table->enum('operation', [
                'set',
                'add',
                'remove',
                'suppress',
                'special',
            ]);

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
            ], 'effect_definition_creature_type_modifiers_unique');

            //Velocizza il recupero delle modifiche per tipo di creatura
            $table->index([
                'effect_definition_id',
                'application_mode',
                'creature_type_id',
            ], 'effect_definition_creature_types_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'effect_definition_creature_type_modifiers'
        );
    }
};
