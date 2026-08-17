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
        Schema::create('subclasses', function (Blueprint $table) {

            $table->id();

            //Classe principale a cui appartiene la sottoclasse
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            //Nome della sottoclasse
            $table->string('name');

            //Livello della classe al quale normalmente viene scelta/ottenuta la sottoclasse
            $table->unsignedTinyInteger('selection_level')
                ->default(1);

            //Descrizione generale della sottoclasse
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Non possono esistere due sottoclassi con lo stesso nome nella stessa classe
            $table->unique([
                'class_id',
                'name'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subclasses');
    }
};
