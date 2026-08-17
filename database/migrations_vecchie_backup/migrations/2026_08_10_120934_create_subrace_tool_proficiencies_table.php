<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_tool_proficiencies', function (Blueprint $table) {

            $table->id();

            //Sottorazza che concede automaticamente la competenza nello strumento
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nello strumento concessa dalla sottorazza
            $table->foreignId('tool_proficiency_id')
                ->constrained('tool_proficiencies')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa sottorazza non può concedere due volte la stessa competenza nello strumento
            $table->unique([
                'subrace_id',
                'tool_proficiency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_tool_proficiencies');
    }
};
