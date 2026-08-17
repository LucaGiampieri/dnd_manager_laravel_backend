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
        Schema::create('campaigns', function (Blueprint $table) {

            $table->id();

            //Utente che ha creato e possiede la campagna
            $table->foreignId('owner_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            //Nome campagna
            $table->string('name');

            //Descrizione campagna
            $table->text('description')
            ->nullable();

            $table->timestamps();

            $table->index([
                'owner_user_id',
                'name',
            ], 'campaigns_owner_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
