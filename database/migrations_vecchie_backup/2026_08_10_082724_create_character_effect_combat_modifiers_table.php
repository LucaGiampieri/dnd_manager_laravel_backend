<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effect_combat_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto attivo che modifica un valore di combattimento
            $table->foreignId('character_effect_id')
                ->constrained('character_effects')
                ->cascadeOnDelete();

            //Valore di combattimento modificato
            $table->enum('type', [
                'armor_class',
                'initiative',
                'max_hit_points',
                'other'
            ]);

            //Tipo di operazione
            //Add: aggiunge o sottrae un valore
            //Set: imposta direttamente il valore
            $table->enum('operation', [
                'add',
                'set'
            ])
                ->default('add');

            //Valore della modifica
            $table->integer('value');

            //Eventuale condizione necessaria perché la modifica si applichi
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
        Schema::dropIfExists('character_effect_combat_modifiers');
    }
};
