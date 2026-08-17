<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('character_effect_damage_modifiers', function (Blueprint $table) {

            $table->id();

            //Effetto attivo che concede resistenza, immunità o vulnerabilità
            $table->foreignId('character_effect_id')
                ->constrained('character_effects')
                ->cascadeOnDelete();

            //Tipo di danno interessato
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Tipo di modifica al danno
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

            //Lo stesso effetto non può applicare due volte lo stesso modificatore allo stesso tipo di danno
            $table->unique([
                'character_effect_id',
                'damage_type_id',
                'modifier'
            ], 'uq_character_effect_damage_modifiers_character_effect_i_42d587d3');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('character_effect_damage_modifiers');
    }
};
