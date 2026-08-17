<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_tags', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene il tag
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Tag associato allo stat block
            $table->foreignId('creature_tag_id')
                ->constrained('creature_tags')
                ->cascadeOnDelete();

            //Condizione particolare del tag
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di assegnare due volte lo stesso tag
            //allo stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'creature_tag_id',
            ], 'creature_stat_block_tags_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_tags');
    }
};
