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
        Schema::create('race_damage_modifiers', function (Blueprint $table) {

            $table->id();

            //Razza che concede automaticamente questa resistenza, immunità o vulnerabilità
            $table->foreignId('race_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di danno interessato
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Tipo di modifica applicata al danno
            $table->enum('modifier', [
                'resistance',
                'immunity',
                'vulnerability'
            ]);

            //Eventuale condizione necessaria perché la modifica si applichi
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di registrare due volte la stessa modifica per la stessa razza e lo stesso tipo di danno
            $table->unique([
                'race_id',
                'damage_type_id',
                'modifier'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_damage_modifiers');
    }
};
