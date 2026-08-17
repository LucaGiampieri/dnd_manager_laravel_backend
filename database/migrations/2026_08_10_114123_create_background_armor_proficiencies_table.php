<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_armor_proficiencies', function (Blueprint $table) {

            $table->id();

            //Background che concede automaticamente la competenza nelle armature
            $table->foreignId('background_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nelle armature concessa dal background
            $table->foreignId('armor_proficiency_id')
                ->constrained('armor_proficiencies')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso background non può concedere due volte la stessa competenza nelle armature
            $table->unique([
                'background_id',
                'armor_proficiency_id'
            ], 'uq_background_armor_proficiencies_background_id_armor_p_6ba8531e');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_armor_proficiencies');
    }
};
