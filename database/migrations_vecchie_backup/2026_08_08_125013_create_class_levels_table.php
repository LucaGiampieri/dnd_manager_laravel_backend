<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_levels', function (Blueprint $table) {

            $table->id();

            //Classe a cui appartiene questo livello
            $table->foreignId('class_id')
                ->constrained()
                ->cascadeOnDelete();

            //Livello della classe
            $table->unsignedTinyInteger('level');

            //Eventuali informazioni particolari relative a questo livello della classe
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una classe può avere una sola configurazione per ogni livello
            $table->unique([
                'class_id',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_levels');
    }
};
