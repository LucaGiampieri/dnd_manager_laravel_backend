<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_damage_modifiers', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene il modificatore
            $table->foreignId('creature_stat_block_id')
                ->constrained(table: 'creature_stat_blocks', indexName: 'fk_creature_stat_block_damage_modifiers_creature_stat_b_6258423e')
                ->cascadeOnDelete();

            //Tipo di danno
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Tipo di modificatore al danno
            $table->enum('modifier_type', [
                'resistance',
                'immunity',
                'vulnerability',
            ]);

            //Condizione necessaria per applicare il modificatore
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita duplicati dello stesso modificatore sullo stesso tipo di danno
            $table->unique([
                'creature_stat_block_id',
                'damage_type_id',
                'modifier_type',
            ], 'creature_stat_block_damage_modifiers_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_damage_modifiers');
    }
};
