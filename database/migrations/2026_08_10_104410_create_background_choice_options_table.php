<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_choice_options', function (Blueprint $table) {

            $table->id();

            //Scelta del background a cui appartiene questa opzione
            $table->foreignId('background_choice_id')
                ->constrained('background_choices')
                ->cascadeOnDelete();

            //Chiave tecnica stabile dell'opzione
            $table->string('key');

            //Tipo dell'opzione
            $table->enum('option_type', [
                'skill',
                'weapon_proficiency',
                'armor_proficiency',
                'tool_proficiency',
                'language',
                'ability',
                'item',
                'feature',
                'other'
            ]);

            //ID del record scelto
            $table->unsignedBigInteger('option_id')
                ->nullable();

            //Testo utilizzabile per opzioni che non appartengono a uno dei cataloghi standard
            $table->string('option_text')
                ->nullable();

            //Quantità, quando l'opzione rappresenta un oggetto
            $table->unsignedInteger('quantity')
                ->default(1);

            //Eventuale valore numerico associato all'opzione
            $table->integer('value')
                ->nullable();

            //Ordine di visualizzazione dell'opzione
            $table->unsignedTinyInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa scelta, anche per opzioni testuali
            $table->unique([
                'background_choice_id',
                'key',
            ], 'background_choice_options_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_choice_options');
    }
};
