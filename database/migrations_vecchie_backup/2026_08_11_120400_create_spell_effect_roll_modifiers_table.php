<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_roll_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che genera questo modificatore ai tiri
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Tipo di tiro interessato
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

            //Eventuale caratteristica specifica
            $table->foreignId('ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Eventuale skill specifica
            $table->foreignId('skill_id')
                ->nullable()
                ->constrained('skills')
                ->nullOnDelete();

            //Bonus/malus numerico fisso
            $table->integer('modifier')
                ->default(0);

            //Eventuale dado aggiuntivo al tiro
            $table->string('dice')
                ->nullable();

            //Vantaggio o svantaggio
            $table->enum('advantage_type', [
                'none',
                'advantage',
                'disadvantage'
            ])
                ->default('none');

            //Eventuale condizione necessaria
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_roll_modifiers');
    }
};
