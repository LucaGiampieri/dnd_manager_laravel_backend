<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_saving_throw_proficiencies', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede la competenza nel tiro salvezza
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Caratteristica del tiro salvezza
            $table->foreignId('ability_id')
                ->constrained('abilities')
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

            //Evita che la stessa identica fonte assegni due volte la stessa competenza
            $table->unique([
                'character_id',
                'ability_id',
                'source_type',
                'source_id'
            ], 'uq_character_saving_throw_proficiencies_character_id_ab_c6da056f');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_saving_throw_proficiencies');
    }
};
