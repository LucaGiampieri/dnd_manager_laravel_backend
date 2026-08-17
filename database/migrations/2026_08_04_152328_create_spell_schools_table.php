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
        Schema::create('spell_schools', function (Blueprint $table) {

            $table->id();

            //Nome della scuola di magia
            $table->string('name')
            ->unique();

            //Descrizione della scuola di magia
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
        Schema::dropIfExists('spell_schools');
    }
};
