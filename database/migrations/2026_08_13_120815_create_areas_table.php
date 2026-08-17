<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();

            //Elemento a cui appartiene l'area
            $table->morphs('areaable');

            //Forma geometrica dell'area
            $table->foreignId('area_shape_id')
                ->constrained('area_shapes')
                ->cascadeOnDelete();

            //Punto dal quale viene generata o misurata l'area
            $table->enum('origin_type', [
                'self',
                'target_point',
                'target_creature',
                'target_object',
                'special',
            ])->default('target_point');

            //Raggio dell'area in metri
            $table->decimal('radius', 10, 3)
                ->nullable();

            //Lunghezza dell'area in metri
            $table->decimal('length', 10, 3)
                ->nullable();

            //Larghezza dell'area in metri
            $table->decimal('width', 10, 3)
                ->nullable();

            //Altezza dell'area in metri
            $table->decimal('height', 10, 3)
                ->nullable();

            //Indica se l'area si sposta insieme alla propria origine
            $table->boolean('moves_with_origin')
                ->default(false);

            //Indica se l'origine è compresa nell'area
            $table->boolean('includes_origin')
                ->default(true);

            //Condizione particolare dell'area
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione o visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero delle aree di uno specifico elemento
            $table->index([
                'areaable_type',
                'areaable_id',
                'sort_order',
            ], 'areas_areaable_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
