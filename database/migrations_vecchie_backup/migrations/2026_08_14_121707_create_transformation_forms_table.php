<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transformation_forms', function (Blueprint $table) {
            $table->id();

            //Trasformazione a cui appartiene la forma
            $table->foreignId('transformation_id')
                ->constrained('transformations')
                ->cascadeOnDelete();

            //Chiave tecnica della forma
            $table->string('key');

            //Stat block utilizzato dalla trasformazione
            $table->foreignId('creature_stat_block_id')
                ->constrained('creature_stat_blocks')
                ->cascadeOnDelete();

            //Nome opzionale della forma
            $table->string('name')
                ->nullable();

            //Indica se questa è la forma predefinita
            $table->boolean('is_default')
                ->default(false);

            //Condizione necessaria per scegliere questa forma
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa trasformazione
            $table->unique([
                'transformation_id',
                'key',
            ], 'transformation_forms_unique');

            //Evita di collegare due volte lo stesso stat block alla stessa trasformazione
            $table->unique([
                'transformation_id',
                'creature_stat_block_id',
            ], 'transformation_forms_stat_block_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_forms');
    }
};
