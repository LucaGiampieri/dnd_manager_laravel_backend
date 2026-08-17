<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subclass_spellcasting_progressions', function (Blueprint $table) {

            $table->id();

            //Configurazione di spellcasting della sottoclasse
            $table->foreignId('subclass_spellcasting_id')
                ->constrained(table: 'subclass_spellcasting', indexName: 'fk_subclass_spellcasting_progressions_subclass_spellcas_6341ab66')
                ->cascadeOnDelete();

            //Livello della classe principale al quale si applica questa progressione
            $table->unsignedTinyInteger('level');

            //Numero di trucchetti conosciuti
            $table->unsignedTinyInteger('cantrips_known')
                ->nullable();

            //Numero di incantesimi conosciuti
            $table->unsignedTinyInteger('spells_known')
                ->nullable();

            //Numero fisso di incantesimi preparabili
            $table->unsignedTinyInteger('spells_prepared')
                ->nullable();

            //Formula per calcolare gli incantesimi preparabili
            $table->string('prepared_formula')
                ->nullable();

            //Slot di 1° livello
            $table->unsignedTinyInteger('level_1_slots')
                ->default(0);

            //Slot di 2° livello
            $table->unsignedTinyInteger('level_2_slots')
                ->default(0);

            //Slot di 3° livello
            $table->unsignedTinyInteger('level_3_slots')
                ->default(0);

            //Slot di 4° livello
            $table->unsignedTinyInteger('level_4_slots')
                ->default(0);

            //Slot di 5° livello
            $table->unsignedTinyInteger('level_5_slots')
                ->default(0);

            //Slot di 6° livello
            $table->unsignedTinyInteger('level_6_slots')
                ->default(0);

            //Slot di 7° livello
            $table->unsignedTinyInteger('level_7_slots')
                ->default(0);

            //Slot di 8° livello
            $table->unsignedTinyInteger('level_8_slots')
                ->default(0);

            //Slot di 9° livello
            $table->unsignedTinyInteger('level_9_slots')
                ->default(0);

            //Numero di slot Pact Magic
            $table->unsignedTinyInteger('pact_slots')
                ->default(0);

            //Livello degli slot Pact Magic
            $table->unsignedTinyInteger('pact_slot_level')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una sola progressione per ogni livello della sottoclasse
            $table->unique([
                'subclass_spellcasting_id',
                'level'
            ], 'uq_subclass_spellcasting_progressions_subclass_spellcas_774a9bd6');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclass_spellcasting_progressions');
    }
};
