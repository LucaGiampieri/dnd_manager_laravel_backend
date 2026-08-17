<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_damage_progressions', function (Blueprint $table) {

            $table->id();

            //Componente di danno dello spell a cui appartiene questa progressione
            $table->foreignId('spell_damage_id')
                ->constrained('spell_damages')
                ->cascadeOnDelete();

            //Cosa determina la scalatura
            $table->enum('progression_type', [
                'character_level',
                'slot_level',
                'caster_level'
            ]);

            //Livello a partire dal quale questa progressione si applica
            $table->unsignedTinyInteger('level');

            //Come deve essere interpretato il valore di questa riga
            //Set: sostituisce il danno base
            //Add: aggiunge questo danno al valore precedente/base
            $table->enum('operation', [
                'set',
                'add'
            ])
                ->default('set');

            //Dadi applicati a questo livello
            $table->string('dice')
                ->nullable();

            //Eventuale bonus fisso
            $table->integer('bonus')
                ->default(0);

            //Moltiplicatore/numero di istanze del componente di danno
            $table->unsignedSmallInteger('quantity')
                ->nullable();

            //Eventuale condizione particolare
            $table->text('condition')
                ->nullable();

            //Note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Per lo stesso componente di danno possiamo avere una sola progressione dello stesso tipo allo stesso livello
            $table->unique([
                'spell_damage_id',
                'progression_type',
                'level'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_damage_progressions');
    }
};
