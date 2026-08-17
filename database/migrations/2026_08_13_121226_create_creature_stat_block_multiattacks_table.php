<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_multiattacks', function (Blueprint $table) {
            $table->id();

            //Azione che rappresenta il Multiattacco
            $table->foreignId('creature_stat_block_action_id')
                ->constrained(table: 'creature_stat_block_actions', indexName: 'fk_creature_stat_block_multiattacks_creature_stat_block_8f80f1a3')
                ->cascadeOnDelete();

            //Modalità con cui vengono scelte le combinazioni
            $table->enum('selection_type', [
                'fixed',
                'choice',
                'special',
            ])->default('fixed');

            //Descrizione particolare del Multiattacco
            $table->text('description')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni azione può avere una sola definizione di Multiattacco
            $table->unique(
                'creature_stat_block_action_id',
                'creature_stat_block_multiattacks_action_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_multiattacks');
    }
};
