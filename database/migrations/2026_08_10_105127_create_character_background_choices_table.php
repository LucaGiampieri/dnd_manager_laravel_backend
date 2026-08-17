<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_background_choices', function (Blueprint $table) {

            $table->id();

            //Personaggio che ha effettuato la scelta
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Opzione del background effettivamente scelta
            $table->foreignId('background_choice_option_id')
                ->constrained('background_choice_options')
                ->cascadeOnDelete();

            //Eventuale valore personalizzato associato alla scelta
            $table->integer('value')
                ->nullable();

            //Eventuale testo personalizzato
            $table->string('value_text')
                ->nullable();

            //Eventuali note specifiche del personaggio
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso personaggio non può selezionare due volte la stessa identica opzione
            $table->unique([
                'character_id',
                'background_choice_option_id'
            ], 'uq_character_background_choices_character_id_background_6203ef73');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_background_choices');
    }
};
