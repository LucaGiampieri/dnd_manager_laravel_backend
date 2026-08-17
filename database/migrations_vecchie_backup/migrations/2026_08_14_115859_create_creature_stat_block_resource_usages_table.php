<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_resource_usages', function (Blueprint $table) {
            $table->id();

            //Risorsa utilizzata
            $table->foreignId('creature_stat_block_resource_id')
                ->constrained('creature_stat_block_resources')
                ->cascadeOnDelete();

            //Elemento che utilizza la risorsa
            $table->morphs('resourceable');

            //Quantità di risorsa utilizzata
            $table->unsignedSmallInteger('cost')
                ->default(1);

            //Modalità di utilizzo della risorsa
            $table->enum('usage_type', [
                'consume',
                'require',
                'special',
            ])->default('consume');

            //Condizione particolare dell'utilizzo
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di collegare due volte la stessa risorsa allo stesso elemento
            $table->unique([
                'creature_stat_block_resource_id',
                'resourceable_type',
                'resourceable_id',
            ], 'creature_stat_block_resource_usages_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_resource_usages');
    }
};
