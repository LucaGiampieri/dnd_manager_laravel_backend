<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_conditions', function (Blueprint $table) {

            $table->id();

            //Personaggio su cui è attiva la condizione
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Condizione attualmente applicata
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Effetto che ha eventualmente causato questa condizione
            $table->foreignId('character_effect_id')
                ->nullable()
                ->constrained('character_effects')
                ->nullOnDelete();

            //Origine concreta della condizione; può essere assente per inserimenti manuali
            $table->nullableMorphs(
                'source',
                'character_conditions_source_index'
            );

            //Livello della condizione quando il regolamento usa una progressione
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Indica se la condizione è attualmente attiva
            $table->boolean('active')
                ->default(true);

            //Momento in cui la condizione è iniziata
            $table->timestamp('started_at')
                ->nullable();

            //Momento in cui termina automaticamente
            $table->timestamp('ends_at')
                ->nullable();

            //Round ancora rimanenti
            $table->unsignedInteger('remaining_rounds')
                ->nullable();

            //Eventuale testo che descrive come o quando termina la condizione
            $table->text('end_condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index([
                'character_id',
                'active',
            ], 'character_conditions_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_conditions');
    }
};
