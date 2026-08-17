<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_weapon_proficiencies', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che concede o rimuove temporaneamente una competenza nelle armi
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Proficienza interessata
            $table->foreignId('weapon_proficiency_id')
                ->constrained('weapon_proficiencies')
                ->cascadeOnDelete();

            //Operazione effettuata
            $table->enum('operation', [
                'grant',
                'remove'
            ])
                ->default('grant');

            //Eventuale condizione
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di inserire due volte la stessa operazione sulla stessa proficienza d'arma all'interno dello stesso effetto dello spell
            $table->unique([
                'spell_effect_id',
                'weapon_proficiency_id',
                'operation'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_weapon_proficiencies');
    }
};
