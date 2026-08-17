<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_weapon_proficiencies', function (Blueprint $table) {

            $table->id();

            //Classe a cui la competenza all'arma è collegata
            $table->foreignId('class_id')
            ->constrained()
            ->cascadeOnDelete();

            //Competenza nelle armi concessa dalla classe
            $table->foreignId('weapon_proficiency_id')
            ->constrained('weapon_proficiencies')
            ->cascadeOnDelete();

            //Indica quando la classe concede questa competenza
            $table->enum('acquisition_context', [
                'initial',
                'multiclass',
                'both'
            ])
            ->default('initial');

            $table->timestamps();

            //Una classe non può essere competente due volte nella stessa tipologia di arma
            $table->unique([
                'class_id',
                'weapon_proficiency_id',
                'acquisition_context'
                ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_weapon_proficiencies');
    }
};
