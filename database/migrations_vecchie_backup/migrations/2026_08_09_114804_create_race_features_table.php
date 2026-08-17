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
        Schema::create('race_features', function (Blueprint $table) {

            $table->id();

            //Razza che concede automaticamente la capacità
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Capacità concessa dalla razza
            $table->foreignId('feature_id')
                ->constrained('features')
                ->cascadeOnDelete();

            //Livello del personaggio al quale la capacità viene ottenuta
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Ordine di visualizzazione delle capacità
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note specifiche
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di assegnare accidentalmente la stessa capacità due volte alla stessa razza allo stesso livello
            $table->unique([
                'race_id',
                'feature_id',
                'level'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_features');
    }
};
