<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_tool_proficiencies', function (Blueprint $table) {

            $table->id();

            //Background che concede automaticamente la competenza nello strumento
            $table->foreignId('background_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nello strumento concessa
            $table->foreignId('tool_proficiency_id')
                ->constrained('tool_proficiencies')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso background non può concedere due volte la stessa competenza
            $table->unique([
                'background_id',
                'tool_proficiency_id'
            ], 'uq_background_tool_proficiencies_background_id_tool_pro_cafd4856');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_tool_proficiencies');
    }
};
