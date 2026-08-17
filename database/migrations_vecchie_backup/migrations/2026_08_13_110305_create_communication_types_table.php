<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('communication_types', function (Blueprint $table) {
            $table->id();

            //Nome del tipo di comunicazione
            $table->string('name')
                ->unique();

            //Descrizione del tipo di comunicazione
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
        Schema::dropIfExists('communication_types');
    }
};
