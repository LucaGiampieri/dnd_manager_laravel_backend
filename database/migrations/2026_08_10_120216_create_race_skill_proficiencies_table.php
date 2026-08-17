<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('race_skill_proficiencies', function (Blueprint $table) {

            $table->id();

            //Razza che concede automaticamente la competenza nell'abilità
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Abilità nella quale la razza concede competenza
            $table->foreignId('skill_id')
                ->constrained()
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa razza non può concedere due volte la stessa competenza
            $table->unique([
                'race_id',
                'skill_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_skill_proficiencies');
    }
};
