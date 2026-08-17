<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_movements', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene il movimento
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Tipo di movimento
            $table->foreignId('movement_type_id')
                ->constrained('movement_types')
                ->cascadeOnDelete();

            //Velocità del movimento in metri
            $table->decimal('speed', 10, 3);

            //Indica se può restare sospeso senza muoversi
            $table->boolean('can_hover')
                ->default(false);

            //Condizione necessaria per utilizzare il movimento
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte lo stesso tipo di movimento nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'movement_type_id',
            ], 'creature_stat_block_movements_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_movements');
    }
};
