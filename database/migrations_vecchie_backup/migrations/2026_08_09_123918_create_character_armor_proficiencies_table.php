<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_armor_proficiencies', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede la competenza
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nelle armature posseduta dal personaggio
            $table->foreignId('armor_proficiency_id')
                ->constrained('armor_proficiencies')
                ->cascadeOnDelete();

            //Origine della competenza
            $table->enum('source_type', [
                'class',
                'subclass',
                'race',
                'subrace',
                'background',
                'feat',
                'item',
                'manual',
                'other'
            ])
            ->nullable();

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita che il personaggio abbia due volte la stessa competenza nelle armature
            $table->unique([
                'character_id',
                'armor_proficiency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_armor_proficiencies');
    }
};
