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
        Schema::create('background_skill_proficiencies', function (Blueprint $table) {

            $table->id();

            //Background che concede automaticamente la competenza
            $table->foreignId('background_id')
                ->constrained()
                ->cascadeOnDelete();

            //Abilità in cui il background concede competenza
            $table->foreignId('skill_id')
                ->constrained()
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa abilità non può essere concessa due volte dallo stesso background
            $table->unique([
                'background_id',
                'skill_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_skill_proficiencies');
    }
};
