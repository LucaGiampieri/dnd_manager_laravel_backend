<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('campaign_invitations', function (Blueprint $table) {
            $table->id();

            //Campagna alla quale si viene invitati
            $table->foreignId('campaign_id')
                ->constrained('campaigns')
                ->cascadeOnDelete();

            //Utente che ha creato l'invito
            $table->foreignId('invited_by_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            //Email del destinatario
            $table->string('email');

            //Ruolo assegnato dopo l'accettazione
            $table->enum('role', [
                'master',
                'player',
            ])->default('player');

            //Nel database si salva soltanto l'hash del token
            $table->string('token_hash', 64)->unique();

            //Stato dell'invito
            $table->enum('status', [
                'pending',
                'accepted',
                'expired',
                'revoked',
            ])->default('pending');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'campaign_id',
                'email',
                'status',
            ], 'campaign_invitations_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_invitations');
    }
};
