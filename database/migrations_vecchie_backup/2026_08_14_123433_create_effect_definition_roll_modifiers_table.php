<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_roll_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Tipo di tiro modificato
            $table->enum('roll_type', [
                'attack',
                'saving_throw',
                'ability_check',
                'skill_check',
                'initiative',
                'damage',
                'healing',
                'other',
            ]);

            //Caratteristica specifica interessata
            $table->foreignId('ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Skill specifica interessata
            $table->foreignId('skill_id')
                ->nullable()
                ->constrained('skills')
                ->nullOnDelete();

            //Tipo di modifica applicata al tiro
            $table->enum('modifier_type', [
                'bonus',
                'penalty',
                'advantage',
                'disadvantage',
                'set',
                'minimum',
                'maximum',
                'special',
            ]);

            //Bonus o malus fisso
            $table->decimal('value', 10, 3)
                ->nullable();

            //Numero di dadi aggiuntivi
            $table->unsignedTinyInteger('dice_count')
                ->nullable();

            //Numero di facce del dado aggiuntivo
            $table->unsignedTinyInteger('die_size')
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

            //Velocizza il recupero dei modificatori per tipo di tiro
            $table->index([
                'effect_definition_id',
                'roll_type',
            ], 'effect_definition_roll_modifiers_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_roll_modifiers');
    }
};
