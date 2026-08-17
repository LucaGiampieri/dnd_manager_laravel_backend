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
        Schema::create('campaign_user', function (Blueprint $table) {

            //Campagna a cui partecipa l'utente
            $table->foreignId('campaign_id')
            ->constrained()
            ->cascadeOnDelete();

            //Utente che partecipa alla campgna
            $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();

            //Ruolo dell'utente nella specifica campagna
            $table->enum('role', [
                'owner',
                'master',
                'player'
            ]);

            //Impediamo che l'utente venga inserito due volte nella stessa campgna
            $table->primary([
                'campaign_id',
                'user_id'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_user');
    }
};
