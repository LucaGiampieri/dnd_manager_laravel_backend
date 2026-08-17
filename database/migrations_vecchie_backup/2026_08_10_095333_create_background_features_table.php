<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_features', function (Blueprint $table) {

            $table->id();

            //Background che concede la capacità
            $table->foreignId('background_id')
                ->constrained()
                ->cascadeOnDelete();

            //Capacità concessa dal background
            $table->foreignId('feature_id')
                ->constrained()
                ->cascadeOnDelete();

            //Ordine di visualizzazione
            $table->unsignedTinyInteger('sort_order')
                ->default(0);

            $table->timestamps();

            //Evitiamo che il background conceda due volte la stessa capacità
            $table->unique([
                'background_id',
                'feature_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_features');
    }
};
