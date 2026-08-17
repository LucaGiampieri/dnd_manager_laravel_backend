<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_item_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto che concede, rimuove o modifica l'oggetto
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Oggetto interessato
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Operazione applicata all'oggetto
            $table->enum('operation', [
                'grant',
                'remove',
                'replace',
                'suppress',
                'special',
            ])->default('grant');

            //Quantità interessata
            $table->unsignedInteger('quantity')
                ->default(1);

            //Stato iniziale di equipaggiamento dell'oggetto concesso
            $table->boolean('equipped')
                ->default(false);

            //Stato iniziale di sintonia dell'oggetto concesso
            $table->boolean('attuned')
                ->default(false);

            //Indica se l'oggetto viene rimosso quando termina l'effetto
            $table->boolean('ends_with_effect')
                ->default(true);

            //Condizione necessaria per applicare la modifica
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'effect_definition_id',
                'item_id',
                'operation',
            ], 'effect_item_modifiers_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_item_modifiers');
    }
};
