<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_multiattack_sequence_items', function (Blueprint $table) {
            $table->id();

            //Sequenza a cui appartiene l'elemento
            $table->foreignId('creature_stat_block_multiattack_sequence_id')
                ->constrained('creature_stat_block_multiattack_sequences')
                ->cascadeOnDelete();

            //Azione eseguita nella sequenza
            $table->foreignId('creature_stat_block_action_id')
                ->constrained('creature_stat_block_actions')
                ->cascadeOnDelete();

            //Chiave tecnica dell'elemento
            $table->string('key');

            //Numero di volte in cui viene eseguita l'azione
            $table->unsignedSmallInteger('quantity')
                ->default(1);

            //Condizione particolare per eseguire questa parte
            $table->text('condition')
                ->nullable();

            //Ordine di esecuzione nella sequenza
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa sequenza
            $table->unique([
                'creature_stat_block_multiattack_sequence_id',
                'key',
            ], 'creature_multiattack_sequence_items_unique');

            //Velocizza la ricerca delle azioni usate nei Multiattacchi
            $table->index(
                'creature_stat_block_action_id',
                'creature_multiattack_sequence_items_action_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_multiattack_sequence_items');
    }
};
