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
        Schema::create('character_combats', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartengono questi dati di combattimento
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Punti ferita massimi permanenti/base
            $table->unsignedInteger('max_hit_points')
                ->default(1);

            //Punti ferita attualmente posseduti
            $table->unsignedInteger('current_hit_points')
                ->default(1);

            //Punti ferita temporanei attualmente posseduti
            $table->unsignedInteger('temporary_hit_points')
                ->default(0);

            //CA impostata manualmente
            $table->unsignedSmallInteger('armor_class_override')
                ->nullable();

            //Iniziativa impostata manualmente
            $table->integer('initiative_override')
                ->nullable();

            //Eventuali note relative allo stato di combattimento
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni personaggio deve avere una sola riga di stato di combattimento
            $table->unique('character_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_combats');
    }
};
