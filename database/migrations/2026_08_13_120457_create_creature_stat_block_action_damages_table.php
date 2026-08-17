<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_action_damages', function (Blueprint $table) {
            $table->id();

            //Azione che provoca il danno
            $table->foreignId('creature_stat_block_action_id')
                ->constrained(table: 'creature_stat_block_actions', indexName: 'fk_creature_stat_block_action_damages_creature_stat_blo_6e41727b')
                ->cascadeOnDelete();

            //Attacco specifico che provoca il danno
            $table->foreignId('creature_stat_block_attack_id')
                ->nullable()
                ->constrained(table: 'creature_stat_block_attacks', indexName: 'fk_creature_stat_block_action_damages_creature_stat_blo_040ac823')
                ->cascadeOnDelete();

            //Tipo di danno
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Numero di dadi del danno
            $table->unsignedTinyInteger('dice_count')
                ->nullable();

            //Numero di facce del dado
            $table->unsignedTinyInteger('die_size')
                ->nullable();

            //Bonus o malus fisso al danno
            $table->integer('bonus')
                ->default(0);

            //Valore medio del danno indicato nello stat block
            $table->unsignedInteger('average_damage')
                ->nullable();

            //Indica se è la componente principale del danno
            $table->boolean('is_primary')
                ->default(false);

            //Condizione necessaria per applicare il danno
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione e visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero dei danni dell'azione
            $table->index([
                'creature_stat_block_action_id',
                'sort_order',
            ], 'creature_stat_block_action_damages_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_action_damages');
    }
};
