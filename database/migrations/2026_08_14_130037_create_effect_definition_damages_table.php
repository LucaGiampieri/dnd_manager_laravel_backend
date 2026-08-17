<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_damages', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il danno
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica del danno
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
                'source_ability_modifier',
                'caster_ability_modifier',
                'target_ability_modifier',
                'proficiency_bonus',
                'other',
            ])->default('none');

            //Caratteristica usata dal modificatore
            $table->foreignId('modifier_ability_id')
                ->nullable()
                ->constrained('abilities')
                ->nullOnDelete();

            //Moltiplicatore del modificatore
            $table->decimal('modifier_multiplier', 10, 3)
                ->default(1);

            //Danno medio già calcolato
            $table->decimal('average_damage', 10, 3)
                ->nullable();

            //Indica il danno principale dell'effetto
            $table->boolean('is_primary')
                ->default(false);

            //Condizione necessaria per infliggere il danno
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
            ], 'effect_definition_damages_unique');

            //Velocizza il recupero dei danni dell'effetto
            $table->index([
                'effect_definition_id',
                'sort_order',
            ], 'effect_definition_damages_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_damages');
    }
};
