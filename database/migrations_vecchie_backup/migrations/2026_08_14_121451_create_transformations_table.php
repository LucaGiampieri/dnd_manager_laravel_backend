<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transformations', function (Blueprint $table) {
            $table->id();

            //Elemento che concede o provoca la trasformazione
            $table->morphs('source');

            //Chiave tecnica della trasformazione
            $table->string('key');

            //Nome della trasformazione
            $table->string('name');

            //Modalità con cui viene scelta la nuova forma
            $table->enum('form_selection_type', [
                'specific',
                'filter',
                'special',
            ])->default('specific');

            //Indica se la trasformazione è temporanea
            $table->boolean('is_temporary')
                ->default(true);

            //Descrizione generale della trasformazione
            $table->text('description')
                ->nullable();

            //Condizione necessaria per utilizzare la trasformazione
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate per la stessa sorgente
            $table->unique([
                'source_type',
                'source_id',
                'key',
            ], 'transformations_source_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformations');
    }
};
