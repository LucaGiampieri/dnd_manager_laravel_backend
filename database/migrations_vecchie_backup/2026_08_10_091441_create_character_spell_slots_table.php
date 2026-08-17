<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('character_spell_slots', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartiene questo gruppo di slot.
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di slot
            $table->enum('slot_type', [
                'standard',
                'pact',
                'custom'
            ])
                ->default('standard');

            //Livello degli slot
            $table->unsignedTinyInteger('level');

            //Numero massimo di slot disponibili
            $table->unsignedTinyInteger('max_slots')
                ->default(0);

            //Numero di slot attualmente disponibili
            $table->unsignedTinyInteger('current_slots')
                ->default(0);

            //Origine del gruppo di slot
            $table->enum('source_type', [
                'system',
                'class',
                'subclass',
                'feature',
                'item',
                'manual',
                'other'
            ])
                ->default('system');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            // Permette allo stesso personaggio di avere, diverse fonti senza conflitti.
            $table->unique([
                'character_id',
                'slot_type',
                'level',
                'source_type',
                'source_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_spell_slots');
    }
};
