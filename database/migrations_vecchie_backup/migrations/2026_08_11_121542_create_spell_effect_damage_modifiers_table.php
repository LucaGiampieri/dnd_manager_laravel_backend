<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_damage_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che concede resistenza, immunità o vulnerabilità
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Tipo di danno interessato
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Tipo di modifica
            $table->enum('modifier', [
                'resistance',
                'immunity',
                'vulnerability'
            ]);

            //Eventuale condizione necessaria
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di duplicare la stessa modifica nello stesso effetto
            $table->unique([
                'spell_effect_id',
                'damage_type_id',
                'modifier'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_damage_modifiers');
    }
};
