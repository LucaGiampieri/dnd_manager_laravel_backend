<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_armor_proficiencies', function (Blueprint $table) {

            $table->id();

            //Classe a cui appartiene la competenza nelle armature
            $table->foreignId('class_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nelle armature concessa dalla classe
            $table->foreignId('armor_proficiency_id')
                ->constrained('armor_proficiencies')
                ->cascadeOnDelete();

            //Indica se la competenza viene concessa come classe iniziale, tramite multiclasse oppure in entrambi i casi
            $table->enum('acquisition_context', [
                'initial',
                'multiclass',
                'both'
            ])
            ->default('initial');

            $table->timestamps();

            //Evita che la stessa classe abbia due volte la stessa competenza nelle armature
            $table->unique([
                'class_id',
                'armor_proficiency_id',
                'acquisition_context'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_armor_proficiencies');
    }
};
