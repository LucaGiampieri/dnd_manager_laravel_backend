<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_features', function (Blueprint $table) {

            $table->id();

            //Classe a cui apprtengono le capacità
            $table->foreignId('class_id')
            ->constrained()
            ->cascadeOnDelete();

            //Capacità che appartengono alla calsse
            $table->foreignId('feature_id')
            ->constrained()
            ->cascadeOnDelete();

            //Livello al quale viene ottenuta
            $table->unsignedTinyInteger('level');

            //Ordine di visualizzazione
            $table->unsignedTinyInteger('sort_order')
            ->default(0);

            $table->timestamps();

            $table->unique([
                'class_id',
                'feature_id',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_features');
    }
};
