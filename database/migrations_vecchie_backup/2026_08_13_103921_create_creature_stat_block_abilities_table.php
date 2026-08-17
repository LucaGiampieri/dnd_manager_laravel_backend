<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_abilities', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene la caratteristica
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Caratteristica dello stat block
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Punteggio della caratteristica
            $table->unsignedTinyInteger('score');

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte la stessa caratteristica nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'ability_id',
            ], 'creature_stat_block_abilities_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_abilities');
    }
};
