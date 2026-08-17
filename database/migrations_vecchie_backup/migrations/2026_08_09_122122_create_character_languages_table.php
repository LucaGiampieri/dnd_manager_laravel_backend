<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_languages', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartengono i linguaggi
            $table->foreignId('character_id')
            ->constrained()
            ->cascadeOnDelete();

            //Lingua conosciuta dal persoanggio
            $table->foreignId('language_id')
            ->constrained()
            ->cascadeOnDelete();

            //Origine della lingua
            $table->enum('source_type', [
                'race',
                'subrace',
                'class',
                'background',
                'feat',
                'item',
                'spell',
                'manual',
                'other'
            ])
            ->default('manual');

            //ID della fonte, quando necessario
            $table->unsignedBigInteger('source_id')
            ->nullable();

            //Note
            $table->text('notes')
            ->nullable();

            $table->timestamps();

            //Una lingua non può essere conosciuta due volte dallo stesso personaggio
            $table->unique([
                'character_id',
                'language_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_languages');
    }
};
