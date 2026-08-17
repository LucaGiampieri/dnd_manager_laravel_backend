<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('character_skill', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede questa competenza
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Skill interessata
            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            //Moltiplicatore del bonus di competenza
            $table->decimal('proficiency_multiplier', 3, 2)
                ->default(1.00);

            //Origine della competenza
            $table->string('source_type')
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa fonte non può assegnare due volte la stessa competenza alla stessa skill
            $table->unique([
                'character_id',
                'skill_id',
                'source_type',
                'source_id'
            ], 'uq_character_skill_character_id_skill_id_source_type_so_f6a9d06c');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_skill');
    }
};
