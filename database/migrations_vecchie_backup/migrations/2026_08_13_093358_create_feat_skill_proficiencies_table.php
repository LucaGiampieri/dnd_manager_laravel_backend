<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feat_skill_proficiencies', function (Blueprint $table) {
            $table->id();

            //Talento che concede la competenza
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Abilità in cui viene concessa la competenza
            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            //Moltiplicatore della competenza
            $table->decimal('proficiency_multiplier', 3, 2)
                ->default(1);

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
                'skill_id',
            ], 'feat_skill_proficiencies_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feat_skill_proficiencies');
    }
};
