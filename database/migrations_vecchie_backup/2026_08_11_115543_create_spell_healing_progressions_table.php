<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_healing_progressions', function (Blueprint $table) {

            $table->id();

            //Componente di cura/PF temporanei a cui appartiene questa progressione
            $table->foreignId('spell_healing_id')
                ->constrained('spell_healings')
                ->cascadeOnDelete();

            //Cosa determina la scalatura
            $table->enum('progression_type', [
                'character_level',
                'slot_level',
                'caster_level'
            ]);

            //Livello a partire dal quale questa riga viene applicata
            $table->unsignedTinyInteger('level');

            //Set: sostituisce il valore precedente
            //Add: aggiunge dadi/bonus al valore base
            $table->enum('operation', [
                'set',
                'add'
            ])
                ->default('set');

            //Dadi risultanti o aggiuntivi
            $table->string('dice')
                ->nullable();

            //Bonus fisso risultante o aggiuntivo
            $table->integer('bonus')
                ->default(0);

            //Eventuale quantità di bersagli o istanze della cura
            $table->unsignedSmallInteger('quantity')
                ->nullable();

            //Eventuale condizione
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una sola progressione dello stesso tipo allo stesso livello per questo componente
            $table->unique([
                'spell_healing_id',
                'progression_type',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_healing_progressions');
    }
};
