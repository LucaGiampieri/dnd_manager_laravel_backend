<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_subrace_choices', function (Blueprint $table) {

            $table->id();

            //Personaggio che effettua la scelta
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Opzione della sottorazza realmente scelta
            $table->foreignId('subrace_choice_option_id')
                ->constrained('subrace_choice_options')
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

            //Il personaggio non può scegliere due volte la stessa identica opzione
            $table->unique([
                'character_id',
                'subrace_choice_option_id'
            ], 'uq_character_subrace_choices_character_id_subrace_choic_0e477289');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_subrace_choices');
    }
};
