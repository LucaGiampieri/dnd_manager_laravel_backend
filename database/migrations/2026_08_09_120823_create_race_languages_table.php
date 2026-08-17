<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('race_languages', function (Blueprint $table) {

            $table->id();

            //Razza che concede automaticamente la lingua
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Lingua concessa dalla razza
            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa razza non può concedere due volte la stessa lingua
            $table->unique([
                'race_id',
                'language_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_languages');
    }
};
