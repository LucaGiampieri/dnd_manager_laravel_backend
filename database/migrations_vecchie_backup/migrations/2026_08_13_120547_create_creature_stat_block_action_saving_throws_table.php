<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_action_saving_throws', function (Blueprint $table) {
            $table->id();

            //Azione che richiede il tiro salvezza
            $table->foreignId('creature_stat_block_action_id')
                ->constrained('creature_stat_block_actions')
                ->cascadeOnDelete();

            //Chiave tecnica del tiro salvezza
            $table->string('key');

            //Caratteristica utilizzata per il tiro salvezza
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Classe Difficoltà del tiro salvezza
            $table->unsignedSmallInteger('save_dc')
                ->nullable();

            //Risultato generale in caso di successo
            $table->enum('success_type', [
                'no_effect',
                'half_damage',
                'special',
            ])->default('no_effect');

            //Effetto descritto in caso di fallimento
            $table->text('failure_description')
                ->nullable();

            //Effetto descritto in caso di successo
            $table->text('success_description')
                ->nullable();

            //Condizione necessaria perché venga richiesto il tiro salvezza
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa azione
            $table->unique([
                'creature_stat_block_action_id',
                'key',
            ], 'creature_action_saving_throws_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_action_saving_throws');
    }
};
