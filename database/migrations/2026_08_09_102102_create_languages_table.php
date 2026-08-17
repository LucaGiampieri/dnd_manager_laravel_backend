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
        Schema::create('languages', function (Blueprint $table) {

            $table->id();

            //Nome del linguaggio
            $table->string('name')
            ->unique();

            // Famiglia del linguaggio
            $table->string('family')
            ->nullable();

            //Il linguaggio è comune nel mondo di gioco
            $table->boolean('common')
            ->default(false);

            //Il linguaggio può essere scelto liberamente
            $table->boolean('selectable')
            ->default(false);

            //Descrizione
            $table->text('description')
            ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
