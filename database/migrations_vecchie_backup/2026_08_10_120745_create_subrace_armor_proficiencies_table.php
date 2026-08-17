<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_armor_proficiencies', function (Blueprint $table) {

            $table->id();

            //Sottorazza che concede automaticamente la competenza nelle armature
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nelle armature concessa dalla sottorazza
            $table->foreignId('armor_proficiency_id')
                ->constrained('armor_proficiencies')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa sottorazza non può concedere due volte la stessa competenza nelle armature
            $table->unique([
                'subrace_id',
                'armor_proficiency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_armor_proficiencies');
    }
};
