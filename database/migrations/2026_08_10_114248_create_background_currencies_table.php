<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_currencies', function (Blueprint $table) {

            $table->id();

            //Background che concede il denaro iniziale
            $table->foreignId('background_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di valuta concessa
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->cascadeOnDelete();

            //Quantità di questa valuta concessa dal background
            $table->unsignedInteger('amount')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso background può avere una sola riga per ogni tipo di valuta
            $table->unique([
                'background_id',
                'currency_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_currencies');
    }
};
