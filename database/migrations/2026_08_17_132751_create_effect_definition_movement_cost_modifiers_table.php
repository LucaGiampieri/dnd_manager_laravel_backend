<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create(
            'effect_definition_movement_cost_modifiers',
            function (Blueprint $table) {
                $table->id();

                //Effetto o regola che genera questo costo di movimento
                $table->foreignId('effect_definition_id');

                $table->foreign(
                    'effect_definition_id',
                    'movement_cost_effect_definition_fk'
                )
                    ->references('id')
                    ->on('effect_definitions')
                    ->cascadeOnDelete();

                //Chiave tecnica univoca all'interno dell'effetto
                $table->string('key');

                //Situazione nella quale si applica la regola:
                //crawling, difficult_terrain, climbing,
                //swimming, squeezing, standing_from_prone...
                $table->string('context_key');

                //Eventuale velocità specifica che annulla il costo.
                //Esempio: una velocità di scalata annulla il costo
                //aggiuntivo normalmente richiesto per scalare.
                $table->foreignId('waived_by_movement_type_id')
                    ->nullable();

                $table->foreign(
                    'waived_by_movement_type_id',
                    'movement_cost_waived_by_type_fk'
                )
                    ->references('id')
                    ->on('movement_types')
                    ->nullOnDelete();

                //Metodo utilizzato per calcolare il costo
                $table->enum('cost_basis', [
                    'per_distance',
                    'total_speed_fraction',
                    'fixed_distance',
                    'special',
                ]);

                //Operazione applicata al costo
                $table->enum('operation', [
                    'add',
                    'multiply',
                    'set',
                    'minimum',
                    'maximum',
                    'suppress',
                    'special',
                ])->default('add');

                //Significato del valore:
                //per_distance: costo aggiuntivo per ogni metro;
                //total_speed_fraction: frazione della velocità totale;
                //fixed_distance: metri consumati;
                //special: valore facoltativo definito dalla regola.
                $table->decimal('value', 10, 3)
                    ->nullable();

                //Eventuale condizione aggiuntiva leggibile
                $table->text('condition')
                  ->nullable();

                //Ordine nel quale applicare più modificatori
                $table->unsignedSmallInteger('sort_order')
                    ->default(0);

                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                $table->unique(
                    [
                        'effect_definition_id',
                        'key',
                    ],
                    'movement_cost_effect_key_unique'
                );

                $table->index(
                    [
                        'context_key',
                        'waived_by_movement_type_id',
                    ],
                    'movement_cost_context_index'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'effect_definition_movement_cost_modifiers'
        );
    }
};
