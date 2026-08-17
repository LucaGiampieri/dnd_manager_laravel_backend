<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_ability_modifiers', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui si applica la modifica
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Caratteristica modificata
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Tipo di modifica applicata
            //Add: aggiunge o sottrae un valore
            //Set: imposta direttamente il punteggio
            //Max: modifica/imposta il limite massimo
            $table->enum('operation', [
                'add',
                'set',
                'max'
            ])
                ->default('add');

            //Valore della modifica
            $table->integer('value');

            //Origine della modifica
            $table->string('source_type')
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Eventuale condizione necessaria
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_ability_modifiers');
    }
};
