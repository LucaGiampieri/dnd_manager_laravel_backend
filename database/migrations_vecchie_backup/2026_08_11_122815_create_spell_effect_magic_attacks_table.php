<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effect_magic_attacks', function (Blueprint $table) {

            $table->id();

            //Effetto dello spell che concede questo nuovo attacco temporaneo
            $table->foreignId('spell_effect_id')
                ->constrained('spell_effects')
                ->cascadeOnDelete();

            //Nome dell'attacco
            $table->string('name');

            //Come viene risolto l'attacco
            $table->enum('resolution_type', [
                'attack_roll',
                'saving_throw',
                'automatic'
            ]);

            //Mischia o distanza
            $table->enum('attack_type', [
                'melee',
                'ranged'
            ])
                ->nullable();

            //Se true, usa la normale caratteristica da incantatore del personaggio
            $table->boolean('uses_spellcasting_ability')
                ->default(true);

            //Eventuale caratteristica specifica che sostituisce quella da incantatore
            $table->foreignId('attack_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Indica se aggiungere il bonus di competenza al tiro per colpire
            $table->boolean('attack_uses_proficiency')
                ->default(true);

            //Eventuale bonus fisso aggiuntivoal tiro per colpire
            $table->integer('attack_bonus')
                ->default(0);

            //Caratteristica del TS richiesto al bersaglio
            $table->foreignId('saving_throw_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Se true, utilizza la normale CD degli incantesimi del personaggio
            $table->boolean('uses_spell_save_dc')
                ->default(true);

            //Eventuale CD fissa
            $table->unsignedSmallInteger('save_dc_override')
                ->nullable();

            //Cosa succede al danno se il bersaglio supera il TS
            $table->enum('save_success_damage', [
                'none',
                'half',
                'full'
            ])
                ->nullable();

            //Portata in metri
            $table->float('range')
                ->nullable();

            //Eventuale condizione necessaria per poter utilizzare l'attacco
            $table->text('condition')
                ->nullable();

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita due attacchi con lo stesso nome all'interno dello stesso spell effect
            $table->unique([
                'spell_effect_id',
                'name'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effect_magic_attacks');
    }
};
