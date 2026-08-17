<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subrace_movements', function (Blueprint $table) {

            $table->id();

            //Sottorazza a cui appartiene il tipo di movimento
            $table->foreignId('subrace_id')
            ->constrained()
            ->cascadeOnDelete();

            //Tipo di movimento della sottorazza
            $table->foreignId('movement_type_id')
            ->constrained()
            ->cascadeOnDelete();

            //Velocità in metri
            $table->decimal('speed', 10, 3);

            //Eventuale condizione
            $table->text('condition')
            ->nullable();

            $table->timestamps();

            //Evitiamo la ripetizione del tipo di movimento per la sottorazza
            $table->unique([
                'subrace_id',
                'movement_type_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subrace_movements');
    }
};
