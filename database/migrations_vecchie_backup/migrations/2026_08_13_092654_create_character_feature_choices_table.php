<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_feature_choices', function (Blueprint $table) {
            $table->id();

            //Feature posseduta dal personaggio
            $table->foreignId('character_feature_id')
                ->constrained('character_feature')
                ->cascadeOnDelete();

            //Opzione scelta dal personaggio
            $table->foreignId('feature_choice_option_id')
                ->constrained('feature_choice_options')
                ->cascadeOnDelete();

            //Valore numerico eventualmente scelto
            $table->integer('value')
                ->nullable();

            //Valore testuale eventualmente scelto
            $table->string('value_text')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di selezionare due volte la stessa opzione
            //per la stessa feature posseduta
            $table->unique([
                'character_feature_id',
                'feature_choice_option_id',
            ], 'character_feature_choices_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_feature_choices');
    }
};
