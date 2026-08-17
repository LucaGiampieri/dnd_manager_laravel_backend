<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_saving_throw_proficiencies', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che concede la competenza nel tiro salvezza
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Caratteristica del tiro salvezza
            $table->foreignId('ability_id')
                ->constrained('abilities')
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

            //Evita di duplicare la stessa competenza nel tiro salvezza concessa dalla spell
            $table->unique([
                'spell_effect_id',
                'ability_id',
                'proficiency_multiplier'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_saving_throw_proficiencies');
    }
};
