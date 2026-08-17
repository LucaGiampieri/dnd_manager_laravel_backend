<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('area_shapes', function (Blueprint $table) {
            $table->id();

            //Chiave tecnica della forma
            $table->string('key')
                ->unique();

            //Nome della forma
            $table->string('name')
                ->unique();

            //Descrizione della forma
            $table->text('description')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_shapes');
    }
};
