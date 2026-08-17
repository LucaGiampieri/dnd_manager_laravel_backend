<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_sizes', function (Blueprint $table) {

            $table->id();

            //Personaggio a cui appartiene la taglia
            $table->foreignId('character_id')
                ->constrained()
                ->cascadeOnDelete();

            //Taglia base/persistente attuale del personaggio
            $table->foreignId('size_id')
                ->constrained('sizes')
                ->cascadeOnDelete();

            //Origine della taglia
            $table->string('source_type')
                ->default('manual');

            //ID della fonte specifica
            $table->unsignedBigInteger('source_id')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni personaggio può avere una sola taglia base/persistente alla volta
            $table->unique('character_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_sizes');
    }
};
