<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('weapon_proficiencies', function (Blueprint $table) {

            $table->id();

            //Nome della competenza
            $table->string('name')
                ->unique();

            //Indica se riguarda una categoria, un'arma specifica oppure una competenza custom
            $table->enum('type', [
                'category',
                'specific',
                'custom'
            ])
            ->default('category');

            //Se la competenza riguarda un'arma specifica, può essere collegata all'oggetto corrispondente
            $table->foreignId('item_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            //Descrizione competenza
            $table->text('description')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weapon_proficiencies');
    }
};
