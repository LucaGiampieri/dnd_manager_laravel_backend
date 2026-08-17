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
        Schema::create('class_spellcasting_progressions', function (Blueprint $table) {

            $table->id();

            //Configurazione di spellcasting della classe
            $table->foreignId('class_spellcasting_id')
                ->constrained('class_spellcasting')
                ->cascadeOnDelete();

            //Livello della classe a cui si riferisce questa progressione
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

            //Formula utilizzata per calcolare il numero di incantesimi preparabili
            $table->string('prepared_formula')
                ->nullable();

            //Slot normali di 1° livello.
            $table->unsignedTinyInteger('level_1_slots')
                ->default(0);

            //Slot normali di 2° livello.
            $table->unsignedTinyInteger('level_2_slots')
                ->default(0);

            //Slot normali di 3° livello.
            $table->unsignedTinyInteger('level_3_slots')
                ->default(0);

            //Slot normali di 4° livello.
            $table->unsignedTinyInteger('level_4_slots')
                ->default(0);

            //Slot normali di 5° livello.
            $table->unsignedTinyInteger('level_5_slots')
                ->default(0);

            //Slot normali di 6° livello.
            $table->unsignedTinyInteger('level_6_slots')
                ->default(0);

            //Slot normali di 7° livello.
            $table->unsignedTinyInteger('level_7_slots')
                ->default(0);

            //Slot normali di 8° livello.
            $table->unsignedTinyInteger('level_8_slots')
                ->default(0);

            //Slot normali di 9° livello.
            $table->unsignedTinyInteger('level_9_slots')
                ->default(0);

            //Numero di slot di Pact Magic.
            $table->unsignedTinyInteger('pact_slots')
                ->default(0);

            //Livello degli slot di Pact Magic.
            $table->unsignedTinyInteger('pact_slot_level')
                ->nullable();

            //Eventuali note sulla progressione.
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una sola progressione per ogni livello della classe
            $table->unique([
                'class_spellcasting_id',
                'level'
            ], 'uq_class_spellcasting_progressions_class_spellcasting_i_ecccdd54');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_spellcasting_progressions');
    }
};
