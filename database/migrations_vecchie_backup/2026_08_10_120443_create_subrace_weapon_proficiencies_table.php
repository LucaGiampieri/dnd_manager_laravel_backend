<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_weapon_proficiencies', function (Blueprint $table) {

            $table->id();

            //Sottorazza che concede automaticamente la competenza nelle armi
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nelle armi concessa dalla sottorazza
            $table->foreignId('weapon_proficiency_id')
                ->constrained('weapon_proficiencies')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa sottorazza non può concedere due volte la stessa competenza nelle armi
            $table->unique([
                'subrace_id',
                'weapon_proficiency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_weapon_proficiencies');
    }
};
