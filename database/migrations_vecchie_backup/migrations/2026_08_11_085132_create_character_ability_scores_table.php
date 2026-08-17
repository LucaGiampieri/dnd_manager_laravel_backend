<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_ability_scores', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartiene il punteggio di caratteristica
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Caratteristica interessata
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Punteggio BASE della caratteristica
            $table->unsignedTinyInteger('base_score')
                ->default(10);

            //Eventuale massimo permanente specifico del personaggio per questa caratteristica
            $table->unsignedTinyInteger('max_score')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni personaggio può avere un solo punteggio base per caratteristica
            $table->unique([
                'character_id',
                'ability_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_ability_scores');
    }
};
