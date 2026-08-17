<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feat_languages', function (Blueprint $table) {
            $table->id();

            //Talento che concede la lingua
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Lingua concessa dal talento
            $table->foreignId('language_id')
                ->constrained('languages')
                ->cascadeOnDelete();

            //Permette di comprendere la lingua
            $table->boolean('can_understand')
                ->default(true);

            //Permette di parlare la lingua
            $table->boolean('can_speak')
                ->default(true);

            //Permette di leggere la lingua
            $table->boolean('can_read')
                ->default(true);

            //Permette di scrivere la lingua
            $table->boolean('can_write')
                ->default(true);

            //Condizione necessaria per ottenere la lingua
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte la stessa lingua
            $table->unique([
                'feat_id',
                'language_id',
            ], 'feat_languages_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feat_languages');
    }
};
