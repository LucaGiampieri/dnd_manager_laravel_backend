<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_damage_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Tipo di danno interessato
            $table->foreignId('damage_type_id')
                ->constrained('damage_types')
                ->cascadeOnDelete();

            //Tipo di modifica al danno
            $table->enum('modifier_type', [
                'resistance',
                'immunity',
                'vulnerability',
            ]);

            //Operazione applicata al modificatore
            $table->enum('operation', [
                'grant',
                'remove',
                'suppress',
            ])->default('grant');

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

            //Evita di applicare due volte la stessa operazione allo stesso tipo di modificatore
            $table->unique([
                'effect_definition_id',
                'damage_type_id',
                'modifier_type',
                'operation',
            ], 'effect_definition_damage_modifiers_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_damage_modifiers');
    }
};
