<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_feature', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede la capacità
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Capacità posseduta dal personaggio
            $table->foreignId('feature_id')
                ->constrained()
                ->cascadeOnDelete();

            //Origine dalla quale il personaggio ha ottenuto la capacità
            $table->string('source_type')
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Utilizzi massimi specifici del personaggio
            $table->unsignedInteger('max_uses')
                ->nullable();

            //Utilizzi ancora disponibili
            $table->unsignedInteger('current_uses')
                ->nullable();

            //La capacità è attualmente attiva
            $table->boolean('active')
                ->default(true);

            //Note specifiche del personaggio
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            // Evita di assegnare due volte la stessa capacità dalla stessa fonte
            $table->unique([
                'character_id',
                'feature_id',
                'source_type',
                'source_id'
            ], 'uq_character_feature_character_id_feature_id_source_typ_0950a218');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_feature');
    }
};
