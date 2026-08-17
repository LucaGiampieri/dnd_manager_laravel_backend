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
        Schema::create('character_inventory', function (Blueprint $table) {

            $table->id();

            //Personaggio che possiede l'oggetto
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Oggetto base del catalogo
            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            //Nome personalizzato dell'istanza
            $table->string('custom_name')
                ->nullable();

            //Numero di copie non tracciate singolarmente, utile per gli oggetti impilabili
            $table->unsignedInteger('stack_quantity')
                ->default(0);

            //Posizione o contenitore dell'oggetto
            $table->string('location')
                ->nullable();

            //Eventuale descrizione specifica di questa copia dell'oggetto
            $table->text('description')
                ->nullable();

            //Note del giocatore/master
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('character_inventory');
    }
};
