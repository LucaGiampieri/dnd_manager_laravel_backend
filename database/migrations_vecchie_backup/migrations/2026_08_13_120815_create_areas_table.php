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

            //Raggio dell'area in metri
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
