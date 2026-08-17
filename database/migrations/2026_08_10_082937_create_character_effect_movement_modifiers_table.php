<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effect_movement_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto del personaggio che modifica una velocità di movimento
            $table->foreignId('character_effect_id')
                ->constrained('character_effects')
                ->cascadeOnDelete();

            //Tipo di movimento interessato
            $table->foreignId('movement_type_id')
                ->constrained('movement_types')
                ->cascadeOnDelete();

            //Valore della modifica alla velocità, in metri
            $table->decimal('modifier', 10, 3)
                ->default(0);

            //False: modifier viene aggiunto/sottratto alla velocità esistente
            //True: modifier imposta direttamente la velocità
            $table->boolean('sets_speed')
                ->default(false);

            //Eventuale condizione necessaria perché la modifica sia applicata

            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso effetto può avere una sola modifica per ciascun tipo di movimento
            $table->unique([
                'character_effect_id',
                'movement_type_id'
            ], 'uq_character_effect_movement_modifiers_character_effect_3412aa08');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_effect_movement_modifiers');
    }
};
