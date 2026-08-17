<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effect_senses', function (Blueprint $table) {

            $table->id();

            //Effetto del personaggio che concede, modifica oppure rimuove un senso
            $table->foreignId('character_effect_id')
                ->constrained('character_effects')
                ->cascadeOnDelete();

            //Senso interessato dall'effetto
            $table->foreignId('sense_id')
                ->constrained('senses')
                ->cascadeOnDelete();

            //Portata del senso in metri
            $table->float('range')
                ->nullable();

            //true = l'effetto concede/imposta il senso
            //false = l'effetto lo rimuove o lo disabilita
            $table->boolean('grants')
                ->default(true);

            //Eventuale condizione necessaria perché l'effetto sul senso si applichi
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso effetto non dovrebbe contenere due volte la stessa modifica sullo stesso senso
            $table->unique([
                'character_effect_id',
                'sense_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_effect_senses');
    }
};
