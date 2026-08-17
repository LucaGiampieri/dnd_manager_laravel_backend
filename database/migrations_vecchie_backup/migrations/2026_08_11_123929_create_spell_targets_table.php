<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_targets', function (Blueprint $table) {

            $table->id();

            //Incantesimo a cui appartiene questa regola di bersaglio
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Tipo di bersaglio selezionabile
            $table->enum('target_type', [
                'creature',
                'object',
                'creature_or_object',
                'point',
                'self',
                'area',
                'special'
            ]);

            //Come interpretare quantity
            //Exact: esattamente quella quantità
            //Up_to: fino a quella quantità
            //All: tutti i bersagli validi
            //Special: quantità determinata da una regola particolare
            $table->enum('quantity_type', [
                'exact',
                'up_to',
                'all',
                'special'
            ])
                ->default('exact');

            //Numero base di bersagli
            $table->unsignedSmallInteger('quantity')
                ->nullable();

            //Se true, il bersaglio deve essere consenziente
            $table->boolean('willing_only')
                ->default(false);

            //Se true, lo spell richiede esplicitamente che l'incantatore possa vedere il bersaglio
            $table->boolean('requires_sight')
                ->default(false);

            //Se true, l'incantatore può essere scelto tra i bersagli
            $table->boolean('can_target_self')
                ->default(true);

            //Eventuale limitazione o requisito
            $table->text('condition')
                ->nullable();

            //Ordine se lo spell possiede più regole di bersaglio
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_targets');
    }
};
