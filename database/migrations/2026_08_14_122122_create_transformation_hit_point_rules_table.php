<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transformation_hit_point_rules', function (Blueprint $table) {
            $table->id();

            //Trasformazione a cui appartiene la regola
            $table->foreignId('transformation_id')
                ->constrained('transformations')
                ->cascadeOnDelete();

            //Modalità con cui vengono gestiti i Punti Ferita
            $table->enum('hit_point_mode', [
                'replace',
                'retain',
                'temporary_pool',
                'add',
                'special',
            ])->default('replace');

            //Indica se vengono memorizzati i PF originali
            $table->boolean('stores_original_hit_points')
                ->default(true);

            //Comportamento quando i PF della forma arrivano a zero
            $table->enum('on_zero', [
                'revert',
                'end_transformation',
                'remain',
                'special',
            ])->default('revert');

            //Indica se il danno in eccesso passa alla forma originale
            $table->boolean('transfers_excess_damage')
                ->default(true);

            //Indica se tornando alla forma originale
            //vengono ripristinati i PF precedenti
            $table->boolean('restores_original_hit_points')
                ->default(true);

            //Modalità con cui le cure vengono applicate durante la trasformazione
            $table->enum('healing_target', [
                'current_form',
                'original_form',
                'both',
                'special',
            ])->default('current_form');

            //Condizione particolare della regola
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Ogni trasformazione possiede una sola regola base dei PF
            $table->unique(
                'transformation_id',
                'transformation_hit_point_rules_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_hit_point_rules');
    }
};
