<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('subrace_choice_options', function (Blueprint $table) {

            $table->id();

            //Scelta della sottorazza alla quale appartiene questa opzione
            $table->foreignId('subrace_choice_id')
                ->constrained('subrace_choices')
                ->cascadeOnDelete();

            //Chiave tecnica stabile dell'opzione
            $table->string('key');

            //Tipo dell'opzione
            $table->enum('option_type', [
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

            //ID dell'elemento selezionabile
            $table->unsignedBigInteger('option_id')
                ->nullable();

            //Testo libero per opzioni personalizzate/homebrew
            $table->string('option_text')
                ->nullable();

            //Eventuale valore numerico associato
            $table->integer('value')
                ->nullable();

            //Quantità, soprattutto utile per gli oggetti
            $table->unsignedInteger('quantity')
                ->default(1);

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa scelta, anche per opzioni testuali
            $table->unique([
                'subrace_choice_id',
                'key',
            ], 'subrace_choice_options_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subrace_choice_options');
    }
};
