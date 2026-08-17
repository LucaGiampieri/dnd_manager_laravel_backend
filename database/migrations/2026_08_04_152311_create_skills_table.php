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
        Schema::create('skills', function (Blueprint $table) {

            $table->id();

            //Nome dell'abilità
            $table->string('name')
                ->unique();

            //Caratteristica normalmente associata alla skill
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Descrizione dell'abilità
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
