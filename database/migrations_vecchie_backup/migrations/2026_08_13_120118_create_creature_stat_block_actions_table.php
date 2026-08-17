<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_actions', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene l'azione
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Chiave tecnica dell'azione
            $table->string('key');

            //Nome dell'azione
            $table->string('name');

            //Tipo di azione
            $table->enum('action_type', [
                'action',
                'bonus_action',
                'reaction',
                'legendary_action',
                'lair_action',
                'mythic_action',
                'special',
            ])->default('action');

            //Descrizione completa dell'azione
            $table->text('description')
                ->nullable();

            //Trigger necessario per reazioni o capacità particolari
            $table->text('trigger')
                ->nullable();

            //Numero massimo di utilizzi
            $table->unsignedSmallInteger('max_uses')
                ->nullable();

            //Modalità di recupero dell'utilizzo
            $table->enum('recharge_type', [
                'at_will',
                'recharge_roll',
                'short_rest',
                'long_rest',
                'per_day',
                'other',
            ])->default('at_will');

            //Valore minimo necessario sul tiro di ricarica
            $table->unsignedTinyInteger('recharge_min')
                ->nullable();

            //Valore massimo del tiro di ricarica
            $table->unsignedTinyInteger('recharge_max')
                ->nullable();

            //Costo in azioni leggendarie
            $table->unsignedTinyInteger('legendary_action_cost')
                ->nullable();

            //Condizione necessaria per usare l'azione
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'key',
            ], 'creature_stat_block_actions_unique');

            //Velocizza il recupero delle azioni per categoria
            $table->index([
                'creature_stat_block_id',
                'action_type',
            ], 'creature_stat_block_actions_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_actions');
    }
};
