<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('armor_proficiencies', function (Blueprint $table) {

            $table->id();

            //Nome della competenza nelle armature
            $table->string('name')
                ->unique();

            //Indica se la competenza riguarda una categoria, un'armatura specifica oppure una competenza personalizzata
            $table->enum('type', [
                'category',
                'specific',
                'custom'
            ])
            ->default('category');

            //Se la competenza riguarda un'armatura specifica, possiamo collegarla all'oggetto corrispondente
            $table->foreignId('item_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            //Descrizione opzionale della competenza
            $table->text('description')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('armor_proficiencies');
    }
};
