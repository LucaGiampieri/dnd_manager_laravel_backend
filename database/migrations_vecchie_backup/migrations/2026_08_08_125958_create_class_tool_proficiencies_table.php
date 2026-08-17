<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_tool_proficiencies', function (Blueprint $table) {

            $table->id();

            //Classe a cui appartiene la competenza nello strumento
            $table->foreignId('class_id')
                ->constrained()
                ->cascadeOnDelete();

            // Competenza nello strumento concessa dalla classe
            $table->foreignId('tool_proficiency_id')
                ->constrained('tool_proficiencies')
                ->cascadeOnDelete();

            //Momento in cui viene concessa la competenza nello strumento
            $table->enum('acquisition_context', [
                'initial',
                'multiclass',
                'both'
            ])
            ->default('initial');

            $table->timestamps();

            //Evita che una classe abbia due volte la stessa competenza nello strumento
            $table->unique([
                'class_id',
                'tool_proficiency_id',
                'acquisition_context'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_tool_proficiencies');
    }
};
