<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feature_choices', function (Blueprint $table) {
            $table->id();

            //Feature a cui appartiene la scelta
            $table->foreignId('feature_id')
                ->constrained('features')
                ->cascadeOnDelete();

            //Chiave tecnica della scelta
            $table->string('key');

            //Nome della scelta
            $table->string('name');

            //Descrizione della scelta
            $table->text('description')
                ->nullable();

            //Numero di opzioni da scegliere
            $table->unsignedTinyInteger('choose')
                ->default(1);

            //Tipo di scelta
            $table->enum('choice_type', [
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

            //Livello minimo necessario per effettuare la scelta
            $table->unsignedTinyInteger('level')
                ->nullable();

            //Indica se la scelta è obbligatoria
            $table->boolean('required')
                ->default(true);

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa feature
            $table->unique([
                'feature_id',
                'key',
                'level',
            ], 'feature_choices_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_choices');
    }
};
