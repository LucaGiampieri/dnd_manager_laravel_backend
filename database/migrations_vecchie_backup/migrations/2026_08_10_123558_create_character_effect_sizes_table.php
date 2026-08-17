<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effect_sizes', function (Blueprint $table) {

            $table->id();

            //Effetto attivo che modifica la taglia del personaggio
            $table->foreignId('character_effect_id')
                ->constrained('character_effects')
                ->cascadeOnDelete();

            //Tipo di modifica applicata alla taglia
            $table->enum('operation', [
                'set',
                'shift'
            ]);

            //Taglia impostata direttamente dall'effetto
            $table->foreignId('size_id')
                ->nullable()
                ->constrained('sizes')
                ->nullOnDelete();

            //Numero di categorie di taglia da aumentare o diminuire
            $table->tinyInteger('steps')
                ->nullable();

            //Eventuale condizione necessaria perché la modifica si applichi
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Un singolo effetto può definire una sola modifica alla taglia
            $table->unique('character_effect_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_effect_sizes');
    }
};
