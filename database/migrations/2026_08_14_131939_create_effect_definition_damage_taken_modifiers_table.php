<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_damage_taken_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained(table: 'effect_definitions', indexName: 'fk_effect_definition_damage_taken_modifiers_effect_defi_ef6f67bb')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Tipo di danno specifico interessato
            $table->foreignId('damage_type_id')
                ->nullable()
                ->constrained('damage_types')
                ->nullOnDelete();

            //Indica se la regola riguarda tutti i tipi di danno
            $table->boolean('applies_to_all_damage_types')
                ->default(false);

            //Operazione applicata al danno ricevuto
            $table->enum('operation', [
                'add',
                'subtract',
                'multiply',
                'set',
                'minimum',
                'maximum',
                'prevent',
                'special',
            ]);

            //Valore fisso utilizzato dalla regola
            $table->decimal('value', 10, 3)
                ->nullable();

            //Numero di dadi utilizzati dalla regola
            $table->unsignedSmallInteger('dice_count')
                ->nullable();

            //Numero di facce del dado
            $table->unsignedSmallInteger('die_size')
                ->nullable();

            //Origine di un eventuale modificatore aggiuntivo
            $table->enum('modifier_source_type', [
                'none',
                'source_ability_modifier',
                'target_ability_modifier',
                'proficiency_bonus',
                'character_level',
                'class_level',
                'other',
            ])->default('none');

            //Caratteristica utilizzata dal modificatore
            $table->foreignId('modifier_ability_id')
                ->nullable()
                ->constrained(table: 'abilities', indexName: 'fk_effect_definition_damage_taken_modifiers_modifier_ab_afc607c2')
                ->nullOnDelete();

            //Classe utilizzata quando il valore dipende dal livello di classe
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();

            //Moltiplicatore applicato al modificatore
            $table->decimal('modifier_multiplier', 10, 3)
                ->default(1);

            //Condizione necessaria per applicare il modificatore
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso effetto
            $table->unique([
                'effect_definition_id',
                'key',
            ], 'effect_definition_damage_taken_unique');

            //Velocizza il recupero dei modificatori per tipo di danno
            $table->index([
                'effect_definition_id',
                'damage_type_id',
            ], 'effect_definition_damage_taken_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'effect_definition_damage_taken_modifiers'
        );
    }
};
