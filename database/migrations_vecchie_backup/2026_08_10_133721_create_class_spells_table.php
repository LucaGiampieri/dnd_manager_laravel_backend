<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_spells', function (Blueprint $table) {

            $table->id();

            //Classe alla cui lista appartiene l'incantesimo
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            //Incantesimo disponibile per la classe
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Indica se questo incantesimo è opzionale rispetto alla lista standard
            $table->boolean('optional')
                ->default(false);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso incantesimo non può comparire due volte nella lista della stessa classe
            $table->unique([
                'class_id',
                'spell_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_spells');
    }
};
