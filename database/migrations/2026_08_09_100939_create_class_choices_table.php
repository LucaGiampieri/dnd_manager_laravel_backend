<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_choices', function (Blueprint $table) {

            $table->id();

            //Classe a cui appartiene la scelta
            $table->foreignId('class_id')
                ->constrained()
                ->cascadeOnDelete();

            //Livello della classe al quale il personaggio effettua la scelta
            $table->unsignedTinyInteger('level')
                ->default(1);

            //Identificatore interno della scelta
            $table->string('key');

            //Nome della scelta mostrato all'utente
            $table->string('name');

            //Descrizione della scelta
            $table->text('description')
                ->nullable();

            //Numero di elementi che devono essere scelti
            $table->unsignedTinyInteger('choose')
                ->default(1);

            //Tipo di elemento che deve essere scelto
            $table->enum('choice_type', [
                'skill',
                'feature',
                'subclass',
                'feat',
                'spell',
                'weapon_proficiency',
                'armor_proficiency',
                'tool_proficiency',
                'language',
                'ability',
                'item',
                'other'
            ]);

            //Situazione nella quale deve essere effettuata la scelta
            $table->enum('acquisition_context', [
                'initial',
                'level_up',
                'multiclass',
                'any'
            ])
            ->default('level_up');

            //Indica se la scelta è obbligatoria
            $table->boolean('required')
                ->default(true);

            //Ordine con cui mostrare la scelta
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //La stessa classe può avere una scelta con la stessa chiave a livelli differenti, ma non due volte allo stesso livello
            $table->unique([
                'class_id',
                'key',
                'level',
                'acquisition_context'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_choices');
    }
};
