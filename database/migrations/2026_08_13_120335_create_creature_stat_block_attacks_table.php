<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_attacks', function (Blueprint $table) {
            $table->id();

            //Azione a cui appartiene l'attacco
            $table->foreignId('creature_stat_block_action_id')
                ->constrained(table: 'creature_stat_block_actions', indexName: 'fk_creature_stat_block_attacks_creature_stat_block_acti_f6266ee9')
                ->cascadeOnDelete();

            //Chiave tecnica dell'attacco
            $table->string('key');

            //Nome dell'attacco
            $table->string('name');

            //Modalità con cui viene effettuato l'attacco
            $table->enum('attack_type', [
                'melee',
                'ranged',
                'melee_or_ranged',
                'other',
            ]);

            //Natura dell'attacco
            $table->enum('attack_kind', [
                'weapon',
                'spell',
                'other',
            ])->default('weapon');

            //Bonus base o totale al tiro per colpire
            $table->integer('attack_bonus')
                ->nullable();

            //Caratteristica eventualmente utilizzata per l'attacco
            $table->foreignId('attack_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Portata dell'attacco in mischia in metri
            $table->decimal('reach', 10, 3)
                ->nullable();

            //Gittata normale dell'attacco in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Gittata lunga dell'attacco in metri
            $table->decimal('long_range', 10, 3)
                ->nullable();

            //Numero di bersagli colpibili con un singolo attacco
            $table->unsignedTinyInteger('target_count')
                ->default(1);

            //Condizione necessaria per effettuare l'attacco
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa azione
            $table->unique([
                'creature_stat_block_action_id',
                'key',
            ], 'creature_stat_block_attacks_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_attacks');
    }
};
