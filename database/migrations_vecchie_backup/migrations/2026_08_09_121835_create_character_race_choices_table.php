<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_race_choices', function (Blueprint $table) {

            $table->id();

            //Personaggio che ha effettuato la scelta
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Opzione realmente scelta dal personaggio
            $table->foreignId('race_choice_option_id')
                ->constrained('race_choice_options')
                ->cascadeOnDelete();

            //Eventuale valore personalizzato
            $table->integer('value')
                ->nullable();

            //Eventuale testo personalizzato
            $table->string('value_text')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Il personaggio non può selezionare due volte la stessa identica opzione
            $table->unique([
                'character_id',
                'race_choice_option_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_race_choices');
    }
};
