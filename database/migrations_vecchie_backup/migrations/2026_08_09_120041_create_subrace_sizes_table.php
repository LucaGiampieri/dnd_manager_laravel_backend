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
        Schema::create('subrace_sizes', function (Blueprint $table) {

            $table->id();

            //Sottorazza che modifica o definisce la taglia della razza principale
            $table->foreignId('subrace_id')
                ->constrained()
                ->cascadeOnDelete();

            //Taglia concessa dalla sottorazza
            $table->foreignId('size_id')
                ->constrained('sizes')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Una sottorazza può definire una sola taglia automatica
            $table->unique('subrace_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subrace_sizes');
    }
};
