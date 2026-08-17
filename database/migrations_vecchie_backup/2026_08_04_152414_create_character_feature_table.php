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
            $table->enum('source_type', [
                'class',
                'subclass',
                'race',
                'subrace',
                'background',
                'feat',
                'item',
                'manual',
                'other'
            ])
            ->nullable();

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->nullable();

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
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_feature');
    }
};
