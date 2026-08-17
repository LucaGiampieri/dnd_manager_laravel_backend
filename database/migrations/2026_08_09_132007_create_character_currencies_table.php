<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_currencies', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartiene il denaro
            $table->foreignId('character_id')
            ->constrained()
            ->cascadeOnDelete();

            //Tipo di valuta
            $table->foreignId('currency_id')
            ->constrained()
            ->cascadeOnDelete();

            //Quantità posseduta
            $table->decimal('amount', 12, 2)
            ->default(0);

            $table->timestamps();

            //Ogni personaggio può avere un solo conto
            $table->unique([
                'character_id',
                'currency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_currencies');
    }
};
