<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_inventory_instances', function (Blueprint $table) {
            $table->id();

            //Riga del catalogo personale a cui appartiene questa copia concreta
            $table->foreignId('character_inventory_id')
                ->constrained('character_inventory')
                ->cascadeOnDelete();

            //Etichetta facoltativa per distinguere copie dello stesso oggetto
            $table->string('label')->nullable();

            $table->boolean('equipped')->default(false);
            $table->boolean('attuned')->default(false);

            $table->unsignedInteger('max_charges')->nullable();
            $table->unsignedInteger('current_charges')->nullable();

            //Condizione o stato fisico della singola copia
            $table->string('condition')->nullable();

            //Posizione diversa da quella generale della riga d'inventario
            $table->string('location')->nullable();

            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'character_inventory_id',
                'equipped',
            ], 'inventory_instances_equipped_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_inventory_instances');
    }
};
