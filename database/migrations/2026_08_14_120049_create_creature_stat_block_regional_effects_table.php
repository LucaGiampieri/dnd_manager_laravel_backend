<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_regional_effects', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene l'effetto regionale
            $table->foreignId('creature_stat_block_id')
                ->constrained(table: 'creature_stat_blocks', indexName: 'fk_creature_stat_block_regional_effects_creature_stat_b_e8c0614c')
                ->cascadeOnDelete();

            //Chiave tecnica dell'effetto regionale
            $table->string('key');

            //Nome opzionale dell'effetto regionale
            $table->string('name')
                ->nullable();

            //Descrizione completa dell'effetto regionale
            $table->text('description');

            //Distanza massima dalla tana o dalla creatura in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Durata dell'effetto quando applicabile
            $table->string('duration')
                ->nullable();

            //Condizione necessaria perché l'effetto sia attivo
            $table->text('condition')
                ->nullable();

            //Condizione che determina la fine dell'effetto
            $table->text('ending_condition')
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
            ], 'creature_stat_block_regional_effects_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_regional_effects');
    }
};
