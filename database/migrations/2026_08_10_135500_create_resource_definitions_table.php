<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('resource_definitions', function (Blueprint $table) {
            $table->id();

            //Elemento che concede o possiede la risorsa
            $table->morphs('source');

            //Chiave tecnica della risorsa
            $table->string('key');

            //Nome della risorsa
            $table->string('name');

            //Tipo di livello usato per stabilire quando la risorsa viene ottenuta
            $table->enum('minimum_level_type', [
                'none',
                'character_level',
                'class_level',
                'source_level',
                'special',
            ])->default('none');

            //Livello minimo necessario per ottenere la risorsa
            $table->unsignedTinyInteger('minimum_level')
                ->nullable();

            //Valore massimo base della risorsa
            $table->unsignedInteger('base_max_value')
                ->nullable();

            //Numero base di dadi associati alla risorsa
            $table->unsignedSmallInteger('base_dice_count')
                ->nullable();

            //Numero di facce del dado associato alla risorsa
            $table->unsignedSmallInteger('base_die_size')
                ->nullable();

            //Modalità con cui viene recuperata la risorsa
            $table->enum('recharge_type', [
                'none',
                'turn',
                'round',
                'short_rest',
                'long_rest',
                'day',
                'dawn',
                'recharge_roll',
                'special',
                'other',
            ])->default('none');

            //Valore minimo necessario sul tiro di ricarica
            $table->unsignedTinyInteger('recharge_min')
                ->nullable();

            //Valore massimo del tiro di ricarica
            $table->unsignedTinyInteger('recharge_max')
                ->nullable();

            //Condizione particolare di recupero
            $table->text('recharge_condition')
                ->nullable();

            //Descrizione della risorsa
            $table->text('description')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate per la stessa sorgente
            $table->unique([
                'source_type',
                'source_id',
                'key',
            ], 'resource_definitions_source_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_definitions');
    }
};
