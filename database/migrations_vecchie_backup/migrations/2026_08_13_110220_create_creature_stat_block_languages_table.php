<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_languages', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene il linguaggio
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Chiave tecnica della voce
            $table->string('key');

            //Origine del linguaggio
            $table->enum('language_source', [
                'specific',
                'all',
                'caster',
                'creator',
                'other',
            ])->default('specific');

            //Linguaggio specifico conosciuto
            $table->foreignId('language_id')
                ->nullable()
                ->constrained('languages')
                ->nullOnDelete();

            //Indica se comprende il linguaggio
            $table->boolean('can_understand')
                ->default(true);

            //Indica se può parlare il linguaggio
            $table->boolean('can_speak')
                ->default(true);

            //Indica se può leggere il linguaggio
            $table->boolean('can_read')
                ->default(true);

            //Indica se può scrivere il linguaggio
            $table->boolean('can_write')
                ->default(true);

            //Condizione particolare del linguaggio
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'key',
            ], 'creature_stat_block_languages_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_languages');
    }
};
