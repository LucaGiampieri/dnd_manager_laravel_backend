<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('creature_stat_blocks', function (Blueprint $table) {
            //Indica come deve essere interpretato il testo presente in alignment
            $table->enum('alignment_mode', [
                //La creatura possiede un solo allineamento preciso
                'fixed',

                //La creatura può avere uno degli allineamenti collegati
                'allowed_set',

                //La creatura può avere qualsiasi allineamento
                'any',

                //La creatura non possiede un allineamento
                'unaligned',

                //La regola non può essere rappresentata con i casi precedenti
                'special',
            ])
                ->nullable()
                ->after('alignment');

            //Velocizza i filtri degli stat block in base alla modalità
            $table->index(
                'alignment_mode',
                'creature_stat_blocks_alignment_mode_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('creature_stat_blocks', function (Blueprint $table) {
            //Rimuove prima l'indice associato alla colonna
            $table->dropIndex(
                'creature_stat_blocks_alignment_mode_index'
            );

            //Ripristina la struttura precedente della tabella
            $table->dropColumn('alignment_mode');
        });
    }
};
