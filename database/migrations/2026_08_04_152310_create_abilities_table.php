<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('abilities', function (Blueprint $table) {

            $table->id();

            //Nome della caratteristica
            $table->string('name')
            ->unique();

            //Abbreviazione
            $table->string('short_name')
            ->unique();

            //Descrizione
            $table->text('description')
            ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abilities');
    }
};
