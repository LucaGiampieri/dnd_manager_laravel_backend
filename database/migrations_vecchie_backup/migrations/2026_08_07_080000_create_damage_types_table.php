<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('damage_types', function (Blueprint $table) {

            $table->id();

            //Nome del tipo di danno
            $table->string('name')
            ->unique();

            //Descrizione
            $table->text('description')
            ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_types');
    }
};
