<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_combat_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Dato di combattimento modificato
            $table->enum('target_type', [
                'armor_class',
                'max_hit_points',
                'attack_count',
                'reach',
                'normal_range',
                'long_range',
                'critical_minimum',
                'critical_extra_dice',
                'other',
            ]);

            //Operazione applicata al valore
            $table->enum('operation', [
                'add',
                'set',
                'minimum',
                'maximum',
                'multiply',
            ])->default('add');

            //Valore utilizzato dall'operazione
            $table->decimal('value', 10, 3);

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

            //Velocizza il recupero dei modificatori
            //per tipo di dato
            $table->index([
                'effect_definition_id',
                'target_type',
            ], 'effect_definition_combat_modifiers_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_combat_modifiers');
    }
};
