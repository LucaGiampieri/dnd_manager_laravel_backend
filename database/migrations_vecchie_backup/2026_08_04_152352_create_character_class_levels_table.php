<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('character_class_levels', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartiene il livello di classe
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Classe posseduta dal personaggio
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            //Livello raggiunto dal personaggio all'interno di questa specifica classe
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Eventuale sottoclasse scelta
            $table->foreignId('subclass_id')
                ->nullable()
                ->constrained('subclasses')
                ->nullOnDelete();

            //Eventuali note specifiche sulla progressione di questa classe
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Un personaggio può possedere una determinata classe una sola volta
            $table->unique([
                'character_id',
                'class_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_class_levels');
    }
};
