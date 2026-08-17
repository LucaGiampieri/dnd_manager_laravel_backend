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
        Schema::create('class_feature_progressions', function (Blueprint $table) {

            $table->id();

            //Collegamento alla capacità specifica della classe
            $table->foreignId('class_feature_id')
                ->constrained('class_features')
                ->cascadeOnDelete();

            //Livello della classe a cui si applica questa progressione
            $table->unsignedTinyInteger('level');

            //Nome del parametro che sta progredendo
            $table->string('key');

            //Valore numerico
            $table->integer('value')
                ->nullable();

            //Eventuale dado
            $table->string('dice')
                ->nullable();

            //Valore testuale per progressioni particolari
            $table->string('value_text')
                ->nullable();

            //Note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa proprietà della stessa capacità non può comparire due volte allo stesso livello
            $table->unique([
                'class_feature_id',
                'level',
                'key'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_feature_progressions');
    }
};
