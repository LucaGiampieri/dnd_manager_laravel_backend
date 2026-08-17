<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {

            $table->id();

            // Nome della classe
            $table->string('name')
                ->unique();

            //Descrizione generale della classe
            $table->text('description')
                ->nullable();

            // Dado vita della classe
            $table->string('hit_die');

            //Eventuali note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
