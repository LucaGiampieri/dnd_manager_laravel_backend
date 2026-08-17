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
        Schema::create('race_sizes', function (Blueprint $table) {

            $table->id();

            //Razza a cui appartiene la taglia base
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Taglia base della razza
            $table->foreignId('size_id')
                ->constrained('sizes')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una razza può avere una sola taglia automatica di base
            $table->unique('race_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_sizes');
    }
};
