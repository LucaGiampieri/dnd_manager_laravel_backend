<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('class_choice_options', function (Blueprint $table) {

            $table->id();

            //Scelta di classe alla quale appartiene questa opzione
            $table->foreignId('class_choice_id')
                ->constrained('class_choices')
                ->cascadeOnDelete();

            //Identificatore interno dell'opzione
            $table->string('key');

            //Tipo dell'opzione
            $table->enum('option_type', [
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
                'equipment_bundle',
                'other'
            ]);

            //ID dell'elemento selezionabile
            $table->unsignedBigInteger('option_id')
                ->nullable();

            //Testo libero per opzioni personalizzate o homebrew
            $table->string('option_text')
                ->nullable();

            //Eventuale valore numerico associato
            $table->integer('value')
                ->nullable();

            //Quantità
            $table->unsignedInteger('quantity')
                ->default(1);

            //Numero di elementi concreti che devono essere selezionati all'interno dell'opzione
            $table->unsignedTinyInteger('selection_count')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni opzione deve avere una chiave univoca all'interno della stessa scelta
            $table->unique([
                'class_choice_id',
                'key'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_choice_options');
    }
};
