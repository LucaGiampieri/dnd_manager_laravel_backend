<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_traits', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene il tratto
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Chiave tecnica del tratto
            $table->string('key');

            //Nome del tratto
            $table->string('name');

            //Descrizione completa del tratto
            $table->text('description');

            //Condizione necessaria per applicare il tratto
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
            ], 'creature_stat_block_traits_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_traits');
    }
};
