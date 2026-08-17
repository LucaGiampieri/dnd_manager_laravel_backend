<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effect_ability_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto attivo che modifica la caratteristica
            $table->foreignId('character_effect_id')
                ->constrained('character_effects')
                ->cascadeOnDelete();

            //Caratteristica modificata dall'effetto
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Tipo di modifica
            //Add: aggiunge o sottrae un valore
            //Set: imposta direttamente il punteggio
            //Max: modifica/imposta temporaneamente
            $table->enum('operation', [
                'add',
                'set',
                'max'
            ])
                ->default('add');

            //Valore applicato
            $table->integer('value');

            //Eventuale condizione aggiuntiva
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso effetto può modificare la stessa caratteristica con operazioni differenti, ma non duplicare la stessa operazione
            $table->unique([
                'character_effect_id',
                'ability_id',
                'operation'
            ], 'uq_character_effect_ability_modifiers_character_effect__bdf98465');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_effect_ability_modifiers');
    }
};
