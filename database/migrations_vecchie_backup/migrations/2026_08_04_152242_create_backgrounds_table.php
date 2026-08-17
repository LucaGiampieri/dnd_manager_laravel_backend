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
        Schema::create('backgrounds', function (Blueprint $table) {

            $table->id();

            //Nome del background
            $table->string('name')
                ->unique();

            //Descrizione generale del background
            $table->text('description')
                ->nullable();

            //Descrizione narrativa/origine
            $table->text('origin_description')
                ->nullable();

            //Eventuali suggerimenti per personalità, ideali, legami e difetti
            $table->text('roleplay_suggestions')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backgrounds');
    }
};
