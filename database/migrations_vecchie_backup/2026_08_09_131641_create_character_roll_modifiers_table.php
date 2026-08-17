<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_roll_modifiers', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui si applica il modificatore
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di tiro modificato
            $table->enum('roll_type', [
                'attack_roll',
                'damage_roll',
                'saving_throw',
                'ability_check',
                'skill_check',
                'initiative',
                'death_save',
                'concentration_check',
                'other'
            ]);

            //Bonus o penalità numerica
            $table->integer('modifier')
                ->default(0);

            //Eventuale dado aggiuntivo al tiro
            $table->string('dice')
                ->nullable();

            //Eventuale vantaggio o svantaggio
            $table->enum('advantage_type', [
                'none',
                'advantage',
                'disadvantage'
            ])
                ->default('none');

            //Se il modificatore riguarda una caratteristica specifica, la indichiamo qui
            $table->foreignId('ability_id')
                ->nullable()
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Se il modificatore riguarda una skill specifica, la indichiamo qui
            $table->foreignId('skill_id')
                ->nullable()
                ->constrained('skills')
                ->cascadeOnDelete();

            //Origine del modificatore
            $table->enum('source_type', [
                'class',
                'subclass',
                'race',
                'subrace',
                'background',
                'feat',
                'feature',
                'item',
                'spell',
                'effect',
                'condition',
                'manual',
                'other'
            ])
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Condizione necessaria perché il modificatore venga applicato
            $table->text('condition')
                ->nullable();

            //Inizio opzionale dell'effetto
            $table->timestamp('starts_at')
                ->nullable();

            //Fine opzionale dell'effetto
            $table->timestamp('ends_at')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_roll_modifiers');
    }
};
