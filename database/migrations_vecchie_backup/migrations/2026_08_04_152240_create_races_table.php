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
        Schema::create('races', function (Blueprint $table) {

            $table->id();

            //Nome della razza
            $table->string('name')
                ->unique();

            //Descrizione generale della razza
            $table->text('description')
                ->nullable();

            //Allineamento tipico della razza
            $table->string('typical_alignment')
                ->nullable();

            //Eventuali note aggiuntive
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
        Schema::dropIfExists('races');
    }
};
