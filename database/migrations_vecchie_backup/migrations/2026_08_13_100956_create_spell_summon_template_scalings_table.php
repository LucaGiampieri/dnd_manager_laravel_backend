<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_summon_template_scalings', function (Blueprint $table) {
            $table->id();

            //Forma dello stat block a cui appartiene la regola
            $table->foreignId('spell_summon_template_form_id')
                ->constrained('spell_summon_template_forms')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Dato dello stat block che viene modificato
            $table->enum('target_type', [
                'armor_class',
                'hit_points',
                'attack_bonus',
                'damage_bonus',
                'damage_dice_count',
                'attack_count',
                'save_dc',
                'movement_speed',
                'other',
            ]);

            //ID dell'elemento specifico modificato
            $table->unsignedBigInteger('target_id')
                ->nullable();

            //Valore da cui viene calcolato lo scaling
            $table->enum('source_type', [
                'slot_level',
                'caster_proficiency_bonus',
                'caster_spell_attack_bonus',
                'caster_spell_save_dc',
                'caster_ability_modifier',
                'character_level',
                'class_level',
                'fixed',
                'other',
            ]);

            //Caratteristica del caster eventualmente utilizzata
            $table->foreignId('source_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Operazione applicata al valore dello stat block
            $table->enum('operation', [
                'add',
                'set',
                'multiply',
            ]);

            //Valore sottratto alla sorgente prima del calcolo
            $table->decimal('source_offset', 10, 3)
                ->default(0);

            //Moltiplicatore applicato alla sorgente
            $table->decimal('multiplier', 10, 3)
                ->default(1);

            //Divisore applicato alla sorgente
            $table->decimal('divisor', 10, 3)
                ->default(1);

            //Valore fisso aggiunto al risultato
            $table->decimal('flat_value', 10, 3)
                ->default(0);

            //Tipo di arrotondamento del risultato
            $table->enum('rounding', [
                'none',
                'floor',
                'ceil',
                'round',
            ])->default('none');

            //Valore minimo della sorgente per applicare la regola
            $table->decimal('minimum_source', 10, 3)
                ->nullable();

            //Valore massimo della sorgente per applicare la regola
            $table->decimal('maximum_source', 10, 3)
                ->nullable();

            //Valore minimo ottenibile dalla regola
            $table->decimal('minimum_result', 10, 3)
                ->nullable();

            //Valore massimo ottenibile dalla regola
            $table->decimal('maximum_result', 10, 3)
                ->nullable();

            //Condizione necessaria per applicare la regola
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione della regola
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa forma
            $table->unique([
                'spell_summon_template_form_id',
                'key',
            ], 'spell_summon_template_scalings_unique');

            //Velocizza la ricerca della statistica modificata
            $table->index([
                'target_type',
                'target_id',
            ], 'spell_summon_template_scalings_target_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_summon_template_scalings');
    }
};
