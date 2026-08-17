<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_skill_proficiencies', function (Blueprint $table) {

            $table->id();

            //Classe che concede automaticamente la competenza nell'abilità
            $table->foreignId('class_id')
                ->constrained()
                ->cascadeOnDelete();

            //Abilità nella quale viene concessa automaticamente la competenza
            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            //Livello della classe dal quale viene concessa la competenza
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Contesto nel quale viene concessa automaticamente la competenza nell'abilità
            $table->enum('acquisition_context', [
                'initial',
                'multiclass',
                'both'
            ])
            ->default('initial');

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa classe non può concedere due volte la stessa skill allo stesso livello
            $table->unique([
                'class_id',
                'skill_id',
                'level',
                'acquisition_context'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_skill_proficiencies');
    }
};
