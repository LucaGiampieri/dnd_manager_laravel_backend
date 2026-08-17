<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_language_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Origine o insieme dei linguaggi interessati
            $table->enum('language_source', [
                'specific',
                'all',
                'source',
                'caster',
                'creator',
                'other',
            ])->default('specific');

            //Linguaggio specifico interessato
            $table->foreignId('language_id')
                ->nullable()
                ->constrained('languages')
                ->nullOnDelete();

            //Operazione applicata al linguaggio
            $table->enum('operation', [
                'grant',
                'remove',
                'suppress',
            ])->default('grant');

            //Modifica la capacità di comprendere il linguaggio
            $table->boolean('can_understand')
                ->nullable();

            //Modifica la capacità di parlare il linguaggio
            $table->boolean('can_speak')
                ->nullable();

            //Modifica la capacità di leggere il linguaggio
            $table->boolean('can_read')
                ->nullable();

            //Modifica la capacità di scrivere il linguaggio
            $table->boolean('can_write')
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
            ], 'effect_definition_language_modifiers_unique');

            //Velocizza la ricerca delle regole linguistiche
            $table->index([
                'effect_definition_id',
                'language_source',
                'language_id',
            ], 'effect_definition_language_modifiers_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_language_modifiers');
    }
};
