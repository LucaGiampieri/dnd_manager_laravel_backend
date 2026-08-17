<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_forced_movements', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il movimento forzato
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Chiave tecnica del movimento
            $table->string('key');

            //Tipo di movimento forzato
            $table->enum('movement_type', [
                'push',
                'pull',
                'move',
                'special',
            ]);

            //Punto rispetto al quale viene calcolata la direzione
            $table->enum('origin_type', [
                'source',
                'target',
                'area_center',
                'chosen_point',
                'other',
            ])->default('source');

            //Direzione del movimento
            $table->enum('direction_type', [
                'away_from_origin',
                'toward_origin',
                'chosen_direction',
                'random_direction',
                'special',
            ]);

            //Distanza del movimento in metri
            $table->decimal('distance', 10, 3)
                ->nullable();

            //Indica se il movimento può essere inferiore alla distanza massima
            $table->boolean('up_to_distance')
                ->default(false);

            //Indica se il movimento deve essere in linea retta
            $table->boolean('straight_line')
                ->default(true);

            //Indica se il movimento si interrompe contro un ostacolo
            $table->boolean('stops_at_obstacle')
                ->default(true);

            //Gestione degli attacchi di opportunità
            $table->enum('opportunity_attack_rule', [
                'default',
                'provokes',
                'does_not_provoke',
                'special',
            ])->default('default');

            //Condizione necessaria per applicare il movimento
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
            ], 'effect_definition_forced_movements_unique');

            //Velocizza il recupero dei movimenti dell'effetto
            $table->index([
                'effect_definition_id',
                'sort_order',
            ], 'effect_definition_forced_movements_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_forced_movements');
    }
};
