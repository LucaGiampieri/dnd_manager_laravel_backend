<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('creature_tags', function (Blueprint $table) {
            //Ordine di visualizzazione dei tag
            $table->unsignedSmallInteger('sort_order')
                ->default(0);
        });

        Schema::table('creature_stat_blocks', function (Blueprint $table) {
            //Indica se lo stat block rappresenta uno sciame
            $table->boolean('is_swarm')
                ->default(false);

            //Taglia delle singole creature che compongono lo sciame
            $table->foreignId('swarm_component_size_id')
                ->nullable()
                ->constrained('sizes')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('creature_stat_blocks', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'swarm_component_size_id'
            );

            $table->dropColumn('is_swarm');
        });

        Schema::table('creature_tags', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
