<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_movement_speeds', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartiene questa velocità di movimento
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Tipo di movimento
            $table->foreignId('movement_type_id')
                ->constrained('movement_types')
                ->cascadeOnDelete();

            //Velocità del personaggio per questo tipo di movimento, espressa in metri
            $table->decimal('speed', 10, 3)
                ->default(0);

            //Origine della velocità
            $table->string('source_type')
                ->default('manual');

            //ID specifico della fonte
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Eventuali condizioni
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni personaggio mantiene una sola velocità base/finale per ciascun tipo di movimento
            $table->unique([
                'character_id',
                'movement_type_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_movement_speeds');
    }
};
