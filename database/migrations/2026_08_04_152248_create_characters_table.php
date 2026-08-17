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
        Schema::create('characters', function (Blueprint $table) {

            $table->id();

            //Campgna in cui è presente il personaggio
            $table->foreignId('campaign_id')
            ->constrained()
            ->cascadeOnDelete();

            //Giocatore a cui appartiene il personaggio
            $table->foreignId('user_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

            //Nome personaggio
            $table->string('name');

            //Nome giocatore
            $table->string('player_name')
            ->nullable();

            //Background personaggio
            $table->foreignId('background_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

            //Punti esperienza personaggio
            $table->unsignedInteger('experience_points')
            ->default(0);

            //allineamento personaggio
            $table->string('alignment')
            ->nullable();

            //Indica se il personaggio possiede attualmente l'ispirazione
            $table->boolean('inspiration')
                ->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
