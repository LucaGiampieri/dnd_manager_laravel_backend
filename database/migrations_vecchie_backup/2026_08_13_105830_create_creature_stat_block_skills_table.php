<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_skills', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene la competenza
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Skill utilizzata
            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            //Moltiplicatore del bonus di competenza
            $table->decimal('proficiency_multiplier', 3, 2)
                ->default(1);

            //Bonus aggiuntivo oltre al normale calcolo
            $table->integer('bonus_modifier')
                ->default(0);

            //Bonus totale da usare al posto del calcolo normale
            $table->integer('override_bonus')
                ->nullable();

            //Valore passivo specifico della skill
            $table->unsignedSmallInteger('passive_score')
                ->nullable();

            //Condizione particolare della competenza
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte la stessa skill nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'skill_id',
            ], 'creature_stat_block_skills_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_skills');
    }
};
