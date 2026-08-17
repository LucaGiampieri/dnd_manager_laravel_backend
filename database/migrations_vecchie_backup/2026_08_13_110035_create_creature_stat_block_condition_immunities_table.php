<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_condition_immunities', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene l'immunità
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Condizione a cui la creatura è immune
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Condizione particolare dell'immunità
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte la stessa immunità nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'condition_id',
            ], 'creature_stat_block_condition_immunities_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_condition_immunities');
    }
};
