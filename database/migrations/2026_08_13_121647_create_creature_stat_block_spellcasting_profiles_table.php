<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('creature_stat_block_spellcasting_profiles', function (Blueprint $table) {
            $table->id();

            //Stat block a cui appartiene il profilo di incantesimi
            $table->foreignId('creature_stat_block_id')
                ->constrained(table: 'creature_stat_blocks', indexName: 'fk_creature_stat_block_spellcasting_profiles_creature_s_720fda44')
                ->cascadeOnDelete();

            //Chiave tecnica del profilo
            $table->string('key');

            //Nome visualizzato del profilo
            $table->string('name');

            //Tipo di capacità da incantatore
            $table->enum('spellcasting_type', [
                'spellcasting',
                'innate',
                'pact_magic',
                'psionics',
                'other',
            ])->default('spellcasting');

            //Livello da incantatore
            $table->unsignedTinyInteger('caster_level')
                ->nullable();

            //Caratteristica da incantatore
            $table->foreignId('spellcasting_ability_id')
                ->nullable()
                ->constrained(table: 'abilities', indexName: 'fk_creature_stat_block_spellcasting_profiles_spellcasti_93f5996e')
                ->nullOnDelete();

            //CD dei tiri salvezza degli incantesimi
            $table->unsignedSmallInteger('spell_save_dc')
                ->nullable();

            //Bonus agli attacchi con incantesimo
            $table->integer('spell_attack_bonus')
                ->nullable();

            //Indica se utilizza slot incantesimo
            $table->boolean('uses_spell_slots')
                ->default(false);

            //Indica se può lanciare rituali
            $table->boolean('can_cast_rituals')
                ->default(false);

            //Indica se ignora le componenti materiali
            $table->boolean('ignores_material_components')
                ->default(false);

            //Descrizione completa della capacità
            $table->text('description')
                ->nullable();

            //Condizione particolare del profilo
            $table->text('condition')
                ->nullable();

            //Ordine di visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso stat block
            $table->unique([
                'creature_stat_block_id',
                'key',
            ], 'creature_spellcasting_profiles_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creature_stat_block_spellcasting_profiles');
    }
};
