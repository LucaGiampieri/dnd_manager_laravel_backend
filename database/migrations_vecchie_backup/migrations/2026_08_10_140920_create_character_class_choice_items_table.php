<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_class_choice_items', function (Blueprint $table) {

            $table->id();

            //Scelta di classe effettuata dal personaggio
            $table->foreignId('character_class_choice_id')
                ->constrained('character_class_choices')
                ->cascadeOnDelete();

            //Oggetto concreto scelto dal personaggio
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Quantità dell'oggetto ottenuta
            $table->unsignedInteger('quantity')
                ->default(1);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso oggetto non viene registrato due volte nella stessa scelta
            $table->unique([
                'character_class_choice_id',
                'item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_class_choice_items');
    }
};
