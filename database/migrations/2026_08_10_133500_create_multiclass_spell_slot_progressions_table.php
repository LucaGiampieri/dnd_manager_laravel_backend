<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('multiclass_spell_slot_progressions', function (Blueprint $table) {
            $table->id();

            //Regolamento a cui appartiene la tabella degli slot condivisi
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Livello effettivo da incantatore dopo aver combinato le classi
            $table->unsignedTinyInteger('caster_level');

            $table->unsignedTinyInteger('level_1_slots')->default(0);
            $table->unsignedTinyInteger('level_2_slots')->default(0);
            $table->unsignedTinyInteger('level_3_slots')->default(0);
            $table->unsignedTinyInteger('level_4_slots')->default(0);
            $table->unsignedTinyInteger('level_5_slots')->default(0);
            $table->unsignedTinyInteger('level_6_slots')->default(0);
            $table->unsignedTinyInteger('level_7_slots')->default(0);
            $table->unsignedTinyInteger('level_8_slots')->default(0);
            $table->unsignedTinyInteger('level_9_slots')->default(0);

            $table->timestamps();

            $table->unique([
                'ruleset_id',
                'caster_level',
            ], 'multiclass_spell_slots_level_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multiclass_spell_slot_progressions');
    }
};
