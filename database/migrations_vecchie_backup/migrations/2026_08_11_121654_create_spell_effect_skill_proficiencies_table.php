<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_skill_proficiencies', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che concede la competenza nella skill
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Skill interessata
            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            //Moltiplicatore del bonus di competenza
            $table->decimal('proficiency_multiplier', 3, 2)
                ->default(1.00);

            //Eventuale condizione
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso effetto non deve concedere due volte la stessa competenza con lo stesso moltiplicatore
            $table->unique([
                'spell_effect_id',
                'skill_id',
                'proficiency_multiplier'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_skill_proficiencies');
    }
};
