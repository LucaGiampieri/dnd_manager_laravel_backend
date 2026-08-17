<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_skill_proficiencies', function (Blueprint $table) {

            $table->id();

            //Sottorazza che concede automaticamente la competenza nell'abilità
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Abilità nella quale la sottorazza
            $table->foreignId('skill_id')
                ->constrained()
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa sottorazza non può concedere due volte la stessa competenza
            $table->unique([
                'subrace_id',
                'skill_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_skill_proficiencies');
    }
};
