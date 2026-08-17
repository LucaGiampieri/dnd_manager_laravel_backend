<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_resources', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene la risorsa
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Chiave tecnica della risorsa
            $table->string('key');

            //Nome della risorsa
            $table->string('name');

            //Valore massimo della risorsa
            $table->unsignedSmallInteger('max_value');

            //Modalità di recupero della risorsa
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
            ])->default('none');

            //Valore minimo necessario sul tiro di ricarica
            $table->unsignedTinyInteger('recharge_min')
                ->nullable();

            //Valore massimo del tiro di ricarica
            $table->unsignedTinyInteger('recharge_max')
                ->nullable();

            //Momento particolare in cui viene recuperata
            $table->text('recharge_condition')
                ->nullable();

            //Descrizione della risorsa
            $table->text('description')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'key',
            ], 'creature_stat_block_resources_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_resources');
    }
};
