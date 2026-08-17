<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_areas', function (Blueprint $table) {

            $table->id();

            //Incantesimo che utilizza questa area
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Forma geometrica dell'area
            $table->foreignId('area_shape_id')
                ->constrained('area_shapes')
                ->cascadeOnDelete();

            //Punto da cui nasce l'area
            $table->enum('origin_type', [
                'caster',
                'point_in_range',
                'target',
                'special'
            ])
                ->default('point_in_range');

            //Raggio in metri
            $table->float('radius')
                ->nullable();

            //Lunghezza dell'area in metri
            $table->float('length')
                ->nullable();

            //Larghezza dell'area in metri
            $table->float('width')
                ->nullable();

            //Altezza dell'area in metri
            $table->float('height')
                ->nullable();

            //Indica se l'area si sposta insieme alla creatura/oggetto su cui è centrata
            $table->boolean('moves_with_origin')
                ->default(false);

            //Indica se la creatura o il punto da cui nasce l'area viene considerato parte dell'area stessa
            $table->boolean('includes_origin')
                ->default(true);

            //Eventuale condizione o regola particolare
            $table->text('condition')
                ->nullable();

            //Ordine nel caso uno spell definisca più aree
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_areas');
    }
};
