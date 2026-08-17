<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_healings', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene la cura
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica della cura
            $table->string('key');

            //Tipo di punti ferita concessi
            $table->enum('healing_type', [
                'hit_points',
                'temporary_hit_points',
                'other',
            ])->default('hit_points');

            //Numero di dadi della cura
            $table->unsignedSmallInteger('dice_count')
                ->nullable();

            //Numero di facce del dado
            $table->unsignedSmallInteger('die_size')
                ->nullable();

            //Bonus fisso alla cura
            $table->decimal('flat_bonus', 10, 3)
                ->default(0);

            //Origine di un eventuale modificatore aggiuntivo
            $table->enum('modifier_source_type', [
                'none',
                'source_ability_modifier',
                'caster_ability_modifier',
                'target_ability_modifier',
                'proficiency_bonus',
                'other',
            ])->default('none');

            //Caratteristica utilizzata dal modificatore
            $table->foreignId('modifier_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Moltiplicatore del modificatore
            $table->decimal('modifier_multiplier', 10, 3)
                ->default(1);

            //Valore medio già calcolato
            $table->decimal('average_healing', 10, 3)
                ->nullable();

            //Regola di applicazione dei PF temporanei
            $table->enum('temporary_hit_point_rule', [
                'replace_if_higher',
                'replace',
                'add',
                'special',
            ])->nullable();

            //Indica la cura principale dell'effetto
            $table->boolean('is_primary')
                ->default(false);

            //Condizione necessaria per applicare la cura
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
            ], 'effect_definition_healings_unique');

            //Velocizza il recupero delle cure dell'effetto
            $table->index([
                'effect_definition_id',
                'sort_order',
            ], 'effect_definition_healings_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_healings');
    }
};
