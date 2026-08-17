<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_ability_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell a cui appartiene questo modificatore
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Caratteristica modificata
            $table->foreignId('ability_id')
                ->constrained('abilities')
                ->cascadeOnDelete();

            //Tipo di operazione
            $table->enum('operation', [
                'add',
                'set',
                'min',
                'max'
            ])
                ->default('add');

            //Valore applicato
            $table->integer('value');

            //Eventuale condizione necessaria perché il modificatore si applichi
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di duplicare esattamente la stessa operazione sulla stessa caratteristica nello stesso effetto
            $table->unique([
                'spell_effect_id',
                'ability_id',
                'operation'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_ability_modifiers');
    }
};
