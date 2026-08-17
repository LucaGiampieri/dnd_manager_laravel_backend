<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_death_saves', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui apprtengono i tiri slvezza per la sopravvivenza
            $table->foreignId('character_id')
            ->constrained()
            ->cascadeOnDelete();

            //Tiri salvezza riusciti
            $table->unsignedTinyInteger('successes')
            ->default(0);

            //Tiri salvezza falliti
            $table->unsignedTinyInteger('failures')
            ->default(0);

            //Stato attuale
            $table->boolean('stable')
            ->default(false);

            //Eventuale morte
            $table->boolean('dead')
            ->default(false);

            $table->timestamps();

            //Un solo stato Death Save per personaggio
            $table->unique('character_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_death_saves');
    }
};
