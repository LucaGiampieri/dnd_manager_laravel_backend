<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_saving_throws', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene il tiro salvezza
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Caratteristica del tiro salvezza
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Moltiplicatore del bonus di competenza
            $table->decimal('proficiency_multiplier', 3, 2)
                ->default(1);

            //Bonus aggiuntivo oltre al normale calcolo
            $table->integer('bonus_modifier')
                ->default(0);

            //Bonus totale da usare al posto del calcolo normale
            $table->integer('override_bonus')
                ->nullable();

            //Condizione particolare del tiro salvezza
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte lo stesso tiro salvezza nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'ability_id',
            ], 'creature_stat_block_saving_throws_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_saving_throws');
    }
};
