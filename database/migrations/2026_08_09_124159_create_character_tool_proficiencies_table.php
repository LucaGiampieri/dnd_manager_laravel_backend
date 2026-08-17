<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_tool_proficiencies', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede la competenza
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Competenza nello strumento posseduta dal personaggio
            $table->foreignId('tool_proficiency_id')
                ->constrained('tool_proficiencies')
                ->cascadeOnDelete();

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

            //Evita che il personaggio abbia due volte la stessa competenza nello strumento
            $table->unique([
                'character_id',
                'tool_proficiency_id'
            ], 'uq_character_tool_proficiencies_character_id_tool_profi_9d982277');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_tool_proficiencies');
    }
};
