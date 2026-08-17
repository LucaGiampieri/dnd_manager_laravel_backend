<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_resource_progressions', function (Blueprint $table) {

            $table->id();

            //Risorsa della classe che sta progredendo
            $table->foreignId('class_resource_id')
                ->constrained('class_resources')
                ->cascadeOnDelete();

            //Livello della classe al quale si applica questa variazione
            $table->unsignedTinyInteger('level');

            //Proprietà della risorsa che cambia
            $table->string('key');

            //Eventuale valore numerico
            $table->integer('value')
                ->nullable();

            //Eventuale dado
            $table->string('dice')
                ->nullable();

            //Eventuale formula o valore testuale
            $table->string('value_text')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa proprietà della stessa risorsanon può essere definita due volte allo stesso livello
            $table->unique([
                'class_resource_id',
                'level',
                'key'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_resource_progressions');
    }
};
