<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_weapon_proficiencies', function (Blueprint $table) {

            $table->id();

            //Background che concede automaticamente la competenza nelle armi
            $table->foreignId('background_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nelle armi concessa dal background
            $table->foreignId('weapon_proficiency_id')
                ->constrained('weapon_proficiencies')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso background non può concedere due volte la stessa competenza nelle armi
            $table->unique([
                'background_id',
                'weapon_proficiency_id'
            ], 'uq_background_weapon_proficiencies_background_id_weapon_ae235cb4');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_weapon_proficiencies');
    }
};
