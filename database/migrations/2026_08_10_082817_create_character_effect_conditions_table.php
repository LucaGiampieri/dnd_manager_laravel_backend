<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effect_conditions', function (Blueprint $table) {

            $table->id();

            //Effetto attivo che interagisce con una determinata condizione
            $table->foreignId('character_effect_id')
                ->constrained('character_effects')
                ->cascadeOnDelete();

            //Condizione interessata
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Cosa fa l'effetto rispetto alla condizione
            //Apply: applica la condizione al personaggio
            //Remove: rimuove la condizione dal personaggio
            //Immunity: rende il personaggio temporaneamente immune alla condizione
            $table->enum('operation', [
                'apply',
                'remove',
                'immunity'
            ]);

            //Se operation = apply:
            //True: la condizione deve terminare quando termina anche l'effetto
            //False: la condizione può continuare anche dopo la fine dell'effetto
            $table->boolean('ends_with_effect')
                ->default(true);

            //Eventuale condizione aggiuntiva necessaria perché questa regola si applichi
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso effetto non deve applicaredue volte la stessa operazione sulla stessa condizione
            $table->unique([
                'character_effect_id',
                'condition_id',
                'operation'
            ], 'uq_character_effect_conditions_character_effect_id_cond_f3b49ff0');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_effect_conditions');
    }
};
