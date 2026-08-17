<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_target_progressions', function (Blueprint $table) {

            $table->id();

            //Regola di bersaglio dello spella cui appartiene questa progressione
            $table->foreignId('spell_target_id')
                ->constrained('spell_targets')
                ->cascadeOnDelete();

            //Cosa determina la progressione
            $table->enum('progression_type', [
                'slot_level',
                'character_level',
                'caster_level'
            ]);

            //Livello a partire dal quale questa regola viene applicata
            $table->unsignedTinyInteger('level');

            //Come modificare il numero di bersagli
            $table->enum('operation', [
                'set',
                'add'
            ])
                ->default('set');

            //Numero di bersagli risultante oppure aggiuntivo
            $table->unsignedSmallInteger('quantity');

            //Eventuale condizione particolare
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di avere due progressioni dello stesso tipo allo stesso livello per la stessa regola di bersaglio
            $table->unique([
                'spell_target_id',
                'progression_type',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_target_progressions');
    }
};
