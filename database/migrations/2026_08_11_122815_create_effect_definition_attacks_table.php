<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_attacks', function (Blueprint $table) {
            $table->id();

            //Effetto che concede o descrive l'attacco
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica dell'attacco
            $table->string('key');

            //Nome visualizzato dell'attacco
            $table->string('name');

            //Modalità con cui viene risolto l'attacco
            $table->enum('resolution_type', [
                'attack_roll',
                'saving_throw',
                'automatic',
                'special',
            ]);

            //Tipo di tiro per colpire
            $table->enum('attack_type', [
                'melee',
                'ranged',
                'special',
            ])->nullable();

            //Origine della caratteristica usata per colpire
            $table->enum('attack_ability_source_type', [
                'caster_spellcasting_ability',
                'source_ability',
                'fixed',
                'none',
                'special',
            ])->default('caster_spellcasting_ability');

            //Caratteristica esplicita usata per colpire
            $table->foreignId('attack_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Indica se si aggiunge il bonus di competenza
            $table->boolean('attack_uses_proficiency')
                ->default(true);

            //Bonus aggiuntivo al tiro per colpire
            $table->integer('attack_bonus')
                ->default(0);

            //Valore fisso che sostituisce il bonus calcolato
            $table->integer('attack_bonus_override')
                ->nullable();

            //Caratteristica del tiro salvezza richiesto
            $table->foreignId('saving_throw_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Origine della CD del tiro salvezza
            $table->enum('save_dc_source_type', [
                'caster_spell_save_dc',
                'source_save_dc',
                'source_ability_dc',
                'fixed',
                'special',
            ])->default('caster_spell_save_dc');

            //CD fissa che sostituisce quella calcolata
            $table->unsignedSmallInteger('save_dc_override')
                ->nullable();

            //Risultato normale sul danno in caso di TS riuscito
            $table->enum('save_success_damage', [
                'none',
                'half',
                'full',
                'special',
            ])->nullable();

            //Portata massima in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Condizione necessaria per poter usare l'attacco
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione o visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'effect_definition_id',
                'key',
            ], 'effect_definition_attacks_unique');

            $table->index([
                'effect_definition_id',
                'sort_order',
            ], 'effect_definition_attacks_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_attacks');
    }
};
