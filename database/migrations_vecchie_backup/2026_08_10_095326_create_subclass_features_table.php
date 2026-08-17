<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subclass_features', function (Blueprint $table) {

            $table->id();

            //Sottoclasse che concede la capacità
            $table->foreignId('subclass_id')
                ->constrained()
                ->cascadeOnDelete();

            //Capacità concessa dalla sottoclasse
            $table->foreignId('feature_id')
                ->constrained()
                ->cascadeOnDelete();

            //Livello al quale viene ottenuta
            $table->unsignedTinyInteger('level');

            //Ordine di visualizzazione
            $table->unsignedTinyInteger('sort_order')
                ->default(0);

            $table->timestamps();

            //Evitiamo che la sottoclasse conceda due volte la stessa capacità
            $table->unique([
                'subclass_id',
                'feature_id',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subclass_features');
    }
};
