<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_hit_points', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartengono i Punti Ferita
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Valore medio dei Punti Ferita indicato nello stat block
            $table->unsignedInteger('average_hit_points')
                ->nullable();

            //Numero di Dadi Vita
            $table->unsignedSmallInteger('hit_dice_count')
                ->nullable();

            //Numero di facce del Dado Vita
            $table->unsignedTinyInteger('hit_die_size')
                ->nullable();

            //Bonus o malus totale applicato ai Dadi Vita
            $table->integer('hit_dice_modifier')
                ->default(0);

            //Formula particolare non rappresentabile con i campi standard
            $table->text('special_calculation')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni stat block possiede una sola definizione base dei Punti Ferita
            $table->unique(
                'creature_stat_block_id',
                'creature_stat_block_hit_points_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_hit_points');
    }
};
