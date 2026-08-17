<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_senses', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene il senso
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Tipo di senso
            $table->foreignId('sense_id')
                ->constrained('senses')
                ->cascadeOnDelete();

            //Portata del senso in metri
            $table->float('range')
                ->nullable();

            //Condizione necessaria per utilizzare il senso
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte lo stesso senso nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'sense_id',
            ], 'creature_stat_block_senses_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_senses');
    }
};
