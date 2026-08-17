<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('spell_summon_template_forms', function (Blueprint $table) {

            //Stat block utilizzato dalla forma evocata
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('spell_summon_template_forms', function (Blueprint $table) {

            //Rimuove il collegamento allo stat block
            $table->dropConstrainedForeignId('creature_stat_block_id');
        });
    }
};
