<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_sizes', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che modifica la taglia
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Tipo di modifica
            $table->enum('operation', [
                'set',
                'shift'
            ]);

            //Taglia da impostare
            $table->foreignId('size_id')
                ->nullable()
                ->constrained('sizes')
                ->nullOnDelete();

            //Numero di categorie di cui spostare la taglia
            $table->tinyInteger('steps')
                ->nullable();

            //Eventuale condizione
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_sizes');
    }
};
