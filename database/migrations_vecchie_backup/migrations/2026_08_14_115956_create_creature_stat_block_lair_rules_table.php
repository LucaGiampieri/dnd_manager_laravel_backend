<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_lair_rules', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartengono le regole della tana
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Nome della tana o del gruppo di azioni di tana
            $table->string('name')
                ->nullable();

            //Conteggio di iniziativa a cui agisce la tana
            $table->unsignedTinyInteger('initiative_count')
                ->nullable();

            //Indica se perde i pareggi di iniziativa
            $table->boolean('loses_initiative_ties')
                ->default(true);

            //Indica se la stessa azione può essere ripetuta consecutivamente
            $table->boolean('can_repeat_same_action')
                ->default(true);

            //Descrizione generale delle regole della tana
            $table->text('description')
                ->nullable();

            //Condizione necessaria perché le azioni di tana siano disponibili
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni stat block possiede una sola configurazione base della tana
            $table->unique(
                'creature_stat_block_id',
                'creature_stat_block_lair_rules_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_lair_rules');
    }
};
