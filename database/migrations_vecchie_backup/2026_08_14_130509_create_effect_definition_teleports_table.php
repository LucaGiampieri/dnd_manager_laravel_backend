<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_teleports', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il teletrasporto
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica del teletrasporto
            $table->string('key');

            //Elemento che viene teletrasportato
            $table->enum('subject_type', [
                'source',
                'effect_targets',
                'source_and_effect_targets',
                'special',
            ])->default('effect_targets');

            //Tipo di destinazione
            $table->enum('destination_type', [
                'chosen_space',
                'source_space',
                'target_space',
                'area_center',
                'nearest_valid_space',
                'random_space',
                'swap',
                'special',
            ])->default('chosen_space');

            //Distanza massima del teletrasporto in metri
            $table->decimal('maximum_distance', 10, 3)
                ->nullable();

            //Distanza minima del teletrasporto in metri
            $table->decimal('minimum_distance', 10, 3)
                ->nullable();

            //Distanza massima dalla destinazione di riferimento
            $table->decimal('destination_radius', 10, 3)
                ->nullable();

            //Richiede che la destinazione sia visibile
            $table->boolean('requires_visible_destination')
                ->default(false);

            //Richiede uno spazio non occupato
            $table->boolean('requires_unoccupied_space')
                ->default(true);

            //Regola relativa al piano di esistenza
            $table->enum('plane_rule', [
                'same_plane',
                'any_plane',
                'specific_plane',
                'special',
            ])->default('same_plane');

            //Piano specifico richiesto dalla regola
            $table->string('specific_plane')
                ->nullable();

            //Condizione necessaria per applicare il teletrasporto
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
            ], 'effect_definition_teleports_unique');

            //Velocizza il recupero dei teletrasporti dell'effetto
            $table->index([
                'effect_definition_id',
                'sort_order',
            ], 'effect_definition_teleports_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_teleports');
    }
};
