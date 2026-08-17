<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('background_equipment', function (Blueprint $table) {

            $table->id();

            //Background che concede l'oggetto
            $table->foreignId('background_id')
                ->constrained()
                ->cascadeOnDelete();

            //Oggetto concesso dal background
            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            //Quantità dell'oggetto concessa
            $table->unsignedInteger('quantity')
                ->default(1);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso oggetto non deve essere inserito due volte per lo stesso background
            $table->unique([
                'background_id',
                'item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('background_equipment');
    }
};
