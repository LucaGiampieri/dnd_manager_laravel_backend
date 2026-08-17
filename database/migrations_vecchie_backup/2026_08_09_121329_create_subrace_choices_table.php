<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_choices', function (Blueprint $table) {

            $table->id();

            //Sottorazza a cui appartiene la scelta
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Identificatore interno della scelta
            $table->string('key');

            //Nome mostrato all'utente
            $table->string('name');

            //Tipo di elemento che deve essere scelto
            $table->enum('choice_type', [
                'ability',
                'skill',
                'language',
                'weapon_proficiency',
                'armor_proficiency',
                'tool_proficiency',
                'feature',
                'item',
                'size',
                'sense',
                'movement_type',
                'damage_type',
                'other'
            ]);

            //Numero di opzioni che devono essere scelte
            $table->unsignedTinyInteger('choose')
                ->default(1);

            //Livello dal quale la scelta diventa disponibile
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Indica se la scelta è obbligatoria
            $table->boolean('required')
                ->default(true);

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Descrizione della scelta
            $table->text('description')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa sottorazza non può avere due scelte con la stessa chiave
            $table->unique([
                'subrace_id',
                'key'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_choices');
    }
};
