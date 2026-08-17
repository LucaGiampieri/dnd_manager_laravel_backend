<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_hit_dice', function (Blueprint $table) {

            $table->id();

            //Progressione di classe del personaggio alla quale appartengono questi dadi vita
            $table->foreignId('character_class_level_id')
                ->constrained('character_class_levels')
                ->cascadeOnDelete();

            //Numero di dadi vita attualmente disponibili
            $table->unsignedTinyInteger('current_dice')
                ->default(0);

            //Eventuale massimo personalizzato
            $table->unsignedTinyInteger('max_dice_override')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni progressione di classe del personaggio possiede un solo stato dei propri dadi vita
            $table->unique('character_class_level_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_hit_dice');
    }
};
