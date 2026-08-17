<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('armor_proficiency_items', function (Blueprint $table) {

            $table->id();

            //Proficienza di armatura
            $table->foreignId('armor_proficiency_id')
                ->constrained('armor_proficiencies')
                ->cascadeOnDelete();

            //Oggetto concreto appartenente a quella proficienza/categoria
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso item non può comparire due volte nella stessa proficienza
            $table->unique([
                'armor_proficiency_id',
                'item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('armor_proficiency_items');
    }
};
