<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_communications', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene la comunicazione
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Tipo di comunicazione
            $table->foreignId('communication_type_id')
                ->constrained('communication_types')
                ->cascadeOnDelete();

            //Portata della comunicazione in metri
            $table->float('range')
                ->nullable();

            //Indica se la comunicazione è bidirezionale
            $table->boolean('two_way')
                ->default(false);

            //Indica se serve condividere almeno una lingua
            $table->boolean('requires_shared_language')
                ->default(false);

            //Condizione particolare della comunicazione
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita duplicati dello stesso tipo di comunicazione nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'communication_type_id',
            ], 'creature_stat_block_communications_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_communications');
    }
};
