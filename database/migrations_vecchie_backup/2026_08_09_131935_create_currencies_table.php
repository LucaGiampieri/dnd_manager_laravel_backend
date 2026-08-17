<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {

            $table->id();

            //Nome valuta
            $table->string('name')
            ->unique();

            //Abbreviazione
            $table->string('code')
            ->unique();

            //Valore relativo alla valuta base
            $table->decimal('conversion_rate', 12, 4)
            ->default(1);

            //Descrizione
            $table->text('description')
            ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
