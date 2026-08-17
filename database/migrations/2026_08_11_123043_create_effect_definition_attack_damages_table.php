<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_attack_damages', function (Blueprint $table) {
            $table->id();

            //Attacco al quale appartiene il componente di danno
            $table->foreignId('effect_definition_attack_id')
                ->constrained(
                    table: 'effect_definition_attacks',
                    indexName: 'fk_effect_attack_damages_attack'
                )
                ->cascadeOnDelete();

            //Chiave tecnica del componente di danno
            $table->string('key');

            //Tipo di danno inflitto
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Numero di dadi del danno
            $table->unsignedSmallInteger('dice_count')
                ->nullable();

            //Numero di facce del dado
            $table->unsignedSmallInteger('die_size')
                ->nullable();

            //Bonus fisso al danno
            $table->decimal('flat_bonus', 10, 3)
                ->default(0);

            //Origine di un eventuale modificatore aggiuntivo
            $table->enum('modifier_source_type', [
                'none',
                'caster_spellcasting_ability',
                'source_ability',
                'target_ability',
                'proficiency_bonus',
                'other',
            ])->default('none');

            //Caratteristica esplicita usata dal modificatore
            $table->foreignId('modifier_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Moltiplicatore applicato al modificatore
            $table->decimal('modifier_multiplier', 10, 3)
                ->default(1);

            //Indica il componente principale del danno
            $table->boolean('is_primary')
                ->default(false);

            //Comportamento specifico in caso di TS riuscito
            $table->enum('save_success_damage_override', [
                'none',
                'half',
                'full',
                'special',
            ])->nullable();

            //Condizione necessaria per infliggere il danno
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
                'effect_definition_attack_id',
                'key',
            ], 'effect_attack_damages_unique');

            $table->index([
                'effect_definition_attack_id',
                'sort_order',
            ], 'effect_attack_damages_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_attack_damages');
    }
};
