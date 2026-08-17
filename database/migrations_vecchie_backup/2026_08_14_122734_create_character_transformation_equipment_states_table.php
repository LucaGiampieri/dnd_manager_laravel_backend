<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_transformation_equipment_states', function (Blueprint $table) {
            $table->id();

            //Trasformazione concreta del personaggio
            $table->foreignId('character_transformation_id')
                ->constrained('character_transformations')
                ->cascadeOnDelete();

            //Oggetto dell'inventario interessato
            $table->foreignId('character_inventory_id')
                ->constrained('character_inventory')
                ->cascadeOnDelete();

            //Stato dell'oggetto durante la trasformazione
            $table->enum('equipment_state', [
                'dropped',
                'merged',
                'remain_worn',
                'removed',
                'special',
            ]);

            //Indica se l'oggetto era equipaggiato prima della trasformazione
            $table->boolean('was_equipped')
                ->default(false);

            //Indica se l'oggetto continua a funzionare
            //durante la trasformazione
            $table->boolean('remains_functional')
                ->default(false);

            //Indica se lo stato precedente viene ripristinato quando termina la trasformazione
            $table->boolean('restore_on_end')
                ->default(true);

            //Condizione particolare relativa all'oggetto
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di registrare due volte lo stesso oggetto nella stessa trasformazione
            $table->unique([
                'character_transformation_id',
                'character_inventory_id',
            ], 'character_transformation_equipment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_transformation_equipment_states');
    }
};
