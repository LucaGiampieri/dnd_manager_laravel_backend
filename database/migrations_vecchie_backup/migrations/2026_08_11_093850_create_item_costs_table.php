<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('item_costs', function (Blueprint $table) {

            $table->id();

            //Oggetto a cui appartiene questo costo
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            // aluta utilizzata per indicare il prezzo
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->cascadeOnDelete();

            //Quantità della valuta necessaria
            $table->unsignedInteger('amount');

            //Eventuali note sul prezzo
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso oggetto può avere una sola riga per ciascun tipo di valuta
            $table->unique([
                'item_id',
                'currency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_costs');
    }
};
