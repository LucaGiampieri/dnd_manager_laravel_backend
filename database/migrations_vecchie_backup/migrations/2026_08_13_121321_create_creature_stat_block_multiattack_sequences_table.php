<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_multiattack_sequences', function (Blueprint $table) {
            $table->id();

            //Multiattacco a cui appartiene la sequenza
            $table->foreignId('creature_stat_block_multiattack_id')
                ->constrained('creature_stat_block_multiattacks')
                ->cascadeOnDelete();

            //Chiave tecnica della sequenza
            $table->string('key');

            //Nome opzionale della sequenza
            $table->string('name')
                ->nullable();

            //Condizione necessaria per poter scegliere la sequenza
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso Multiattacco
            $table->unique([
                'creature_stat_block_multiattack_id',
                'key',
            ], 'creature_multiattack_sequences_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_multiattack_sequences');
    }
};
