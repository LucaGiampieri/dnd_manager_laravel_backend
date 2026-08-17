<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_ancestry_retentions', function (Blueprint $table) {
            $table->id();

            //Ascendenza precedente da cui viene mantenuta la capacità
            $table->foreignId('from_character_ancestry_id')
                ->constrained('character_ancestries')
                ->cascadeOnDelete();

            //Nuova ascendenza che permette di mantenere la capacità
            $table->foreignId('to_character_ancestry_id')
                ->constrained('character_ancestries')
                ->cascadeOnDelete();

            //Tipo di capacità mantenuta
            $table->enum('retention_type', [
                'skill_proficiency',
                'movement_speed',
                'other',
            ]);

            //Competenza in abilità eventualmente mantenuta
            $table->foreignId('skill_id')
                ->nullable()
                ->constrained('skills')
                ->nullOnDelete();

            //Tipo di movimento eventualmente mantenuto
            $table->foreignId('movement_type_id')
                ->nullable()
                ->constrained('movement_types')
                ->nullOnDelete();

            //Velocità mantenuta per il tipo di movimento
            $table->float('movement_speed')
                ->nullable();

            //Descrizione per eventuali casi non standard
            $table->text('description')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di registrare due volte la stessa competenza mantenuta
            $table->unique([
                'from_character_ancestry_id',
                'to_character_ancestry_id',
                'retention_type',
                'skill_id',
                'movement_type_id',
            ], 'character_ancestry_retentions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_ancestry_retentions');
    }
};
