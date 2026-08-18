<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(
            'creature_stat_block_alignments',
            function (Blueprint $table) {
                $table->id();

                //Stat block a cui appartiene l'opzione di allineamento
                $table->foreignId('creature_stat_block_id')
                    ->constrained('creature_stat_blocks')
                    ->cascadeOnDelete();

                //Allineamento ammesso per lo stat block
                $table->foreignId('alignment_id')
                    ->constrained('alignments')
                    ->cascadeOnDelete();

                //Indica se questo è l'allineamento tipico o principale
                //quando lo stat block ammette più possibilità
                $table->boolean('is_typical')
                    ->default(false);

                //Ordine con cui mostrare le opzioni di allineamento
                $table->unsignedSmallInteger('sort_order')
                    ->default(0);

                //Eventuali spiegazioni specifiche per questa opzione
                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                //Impedisce di collegare due volte lo stesso allineamento
                //allo stesso stat block
                $table->unique([
                    'creature_stat_block_id',
                    'alignment_id',
                ], 'creature_stat_block_alignments_unique');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'creature_stat_block_alignments'
        );
    }
};
