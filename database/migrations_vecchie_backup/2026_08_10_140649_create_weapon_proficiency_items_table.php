<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('weapon_proficiency_items', function (Blueprint $table) {

            $table->id();

            //Competenza/categoria di armi
            $table->foreignId('weapon_proficiency_id')
                ->constrained('weapon_proficiencies')
                ->cascadeOnDelete();

            //Arma concreta appartenente a questa competenza/categoria
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Lo stesso oggetto non può essere collegato due volte alla stessa competenza
            $table->unique([
                'weapon_proficiency_id',
                'item_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weapon_proficiency_items');
    }
};
