<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('race_weapon_proficiencies', function (Blueprint $table) {

            $table->id();

            //Razza che concede automaticamente la competenza nelle armi
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nelle armi concessa dalla razza
            $table->foreignId('weapon_proficiency_id')
                ->constrained('weapon_proficiencies')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa razza non può concedere due volte la stessa competenza nelle armi
            $table->unique([
                'race_id',
                'weapon_proficiency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('race_weapon_proficiencies');
    }
};
