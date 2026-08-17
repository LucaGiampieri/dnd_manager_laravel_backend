<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feat_tool_proficiencies', function (Blueprint $table) {
            $table->id();

            //Talento che concede la competenza
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Competenza nello strumento concessa
            $table->foreignId('tool_proficiency_id')
                ->constrained('tool_proficiencies')
                ->cascadeOnDelete();

            //Condizione necessaria per ottenere la competenza
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte la stessa competenza
            $table->unique([
                'feat_id',
                'tool_proficiency_id',
            ], 'feat_tool_proficiencies_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feat_tool_proficiencies');
    }
};
