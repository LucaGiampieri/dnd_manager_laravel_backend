<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feature_choice_options', function (Blueprint $table) {
            $table->id();

            //Scelta a cui appartiene l'opzione
            $table->foreignId('feature_choice_id')
                ->constrained('feature_choices')
                ->cascadeOnDelete();

            //Chiave tecnica dell'opzione
            $table->string('key');

            //Tipo di contenuto rappresentato dall'opzione
            $table->enum('option_type', [
                'ability',
                'skill',
                'weapon_proficiency',
                'armor_proficiency',
                'tool_proficiency',
                'language',
                'spell',
                'item',
                'feature',
                'damage_type',
                'movement_type',
                'other',
            ]);

            //ID del contenuto collegato
            $table->unsignedBigInteger('option_id')
                ->nullable();

            //Testo dell'opzione quando non esiste un contenuto specifico
            $table->string('option_text')
                ->nullable();

            //Valore numerico eventualmente assegnato dall'opzione
            $table->integer('value')
                ->nullable();

            //Quantità eventualmente assegnata dall'opzione
            $table->unsignedSmallInteger('quantity')
                ->default(1);

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa scelta
            $table->unique([
                'feature_choice_id',
                'key',
            ], 'feature_choice_options_unique');

            //Velocizza la ricerca del contenuto collegato
            $table->index([
                'option_type',
                'option_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_choice_options');
    }
};
