<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_spellcasting_slots', function (Blueprint $table) {
            $table->id();

            //Profilo da incantatore a cui appartengono gli slot
            $table->foreignId('creature_stat_block_spellcasting_profile_id')
                ->constrained('creature_stat_block_spellcasting_profiles')
                ->cascadeOnDelete();

            //Livello degli slot
            $table->unsignedTinyInteger('spell_level');

            //Numero massimo di slot disponibili
            $table->unsignedTinyInteger('slot_count');

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte gli slot dello stesso livello nello stesso profilo da incantatore
            $table->unique([
                'creature_stat_block_spellcasting_profile_id',
                'spell_level',
            ], 'creature_spellcasting_slots_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_spellcasting_slots');
    }
};
