<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_combat_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che genera questo modificatore
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Valore di combattimento modificato
            $table->enum('type', [
                'armor_class',
                'initiative',
                'max_hit_points',
                'other'
            ]);

            //Come viene applicato il valore
            $table->enum('operation', [
                'add',
                'set',
                'min',
                'max'
            ])
                ->default('add');

            //Valore applicato
            $table->integer('value');

            //Eventuale condizione
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
        Schema::dropIfExists('spell_effect_combat_modifiers');
    }
};
