<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('race_armor_proficiencies', function (Blueprint $table) {

            $table->id();

            //Razza che concede automaticamente la competenza nelle armature
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nelle armature concessa dalla razza
            $table->foreignId('armor_proficiency_id')
                ->constrained('armor_proficiencies')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa razza non può concedere due volte la stessa competenza nelle armature
            $table->unique([
                'race_id',
                'armor_proficiency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_armor_proficiencies');
    }
};
