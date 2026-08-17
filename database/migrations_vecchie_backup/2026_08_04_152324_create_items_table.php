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
        Schema::create('items', function (Blueprint $table) {

            $table->id();

            //Nome dell'oggetto
            $table->string('name');

            //Categoria generale dell'oggetto
            $table->foreignId('item_type_id')
                ->constrained('item_types')
                ->cascadeOnDelete();

            //Descrizione generale dell'oggetto
            $table->text('description')
                ->nullable();

            //Peso dell'oggetto in chilogrammi
            $table->float('weight_kg')
                ->nullable();

            //Rarità dell'oggetto
            $table->enum('rarity', [
                'common',
                'uncommon',
                'rare',
                'very_rare',
                'legendary',
                'artifact'
            ])
                ->nullable();

            //Indica se l'oggetto è magico
            $table->boolean('is_magical')
                ->default(false);

            //Indica se l'oggetto richiede sintonia
            $table->boolean('requires_attunement')
                ->default(false);

            //Eventuali requisiti necessari per utilizzare o sintonizzarsi con l'oggetto
            $table->text('requirements')
                ->nullable();

            //Eventuali note generali
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
        Schema::dropIfExists('items');
    }
};
