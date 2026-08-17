<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feat_armor_proficiencies', function (Blueprint $table) {
            $table->id();

            //Talento che concede la competenza
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Competenza nelle armature concessa
            $table->foreignId('armor_proficiency_id')
                ->constrained('armor_proficiencies')
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
                'armor_proficiency_id',
            ], 'feat_armor_proficiencies_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feat_armor_proficiencies');
    }
};
