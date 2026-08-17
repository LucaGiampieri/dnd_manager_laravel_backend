<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_movement_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che modifica una velocità di movimento
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Tipo di movimento interessato
            $table->foreignId('movement_type_id')
                ->constrained('movement_types')
                ->cascadeOnDelete();

            //Tipo di operazione
            $table->enum('operation', [
                'add',
                'set',
                'min',
                'max'
            ])
                ->default('add');

            //Valore espresso in metri
            $table->float('value');

            //Eventuale condizione necessaria
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
        Schema::dropIfExists('spell_effect_movement_modifiers');
    }
};
