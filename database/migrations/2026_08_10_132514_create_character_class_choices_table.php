<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_class_choices', function (Blueprint $table) {

            $table->id();

            //Personaggio che ha effettuato la scelta
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Opzione di classe effettivamente scelta
            $table->foreignId('class_choice_option_id')
                ->constrained('class_choice_options')
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

            //Impedisce allo stesso personaggio di scegliere due volte la stessa identica opzione
            $table->unique([
                'character_id',
                'class_choice_option_id'
            ], 'uq_character_class_choices_character_id_class_choice_op_1dade00b');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_class_choices');
    }
};
