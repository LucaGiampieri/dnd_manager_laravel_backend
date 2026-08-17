<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transformation_form_filters', function (Blueprint $table) {
            $table->id();

            //Trasformazione a cui appartiene il filtro
            $table->foreignId('transformation_id')
                ->constrained('transformations')
                ->cascadeOnDelete();

            //Chiave tecnica del filtro
            $table->string('key');

            //Gruppo logico del requisito
            $table->unsignedSmallInteger('requirement_group')
                ->default(1);

            //Tipo di dato controllato dal filtro
            $table->enum('filter_type', [
                'creature_type',
                'creature_tag',
                'size',
                'challenge_rating',
                'movement_type',
                'monster',
                'stat_block',
                'other',
            ]);

            //Operatore utilizzato per il confronto
            $table->enum('operator', [
                'equals',
                'not_equals',
                'less_than',
                'less_than_or_equal',
                'greater_than',
                'greater_than_or_equal',
                'exists',
                'not_exists',
                'other',
            ]);

            //ID dell'elemento eventualmente utilizzato nel confronto
            $table->unsignedBigInteger('target_id')
                ->nullable();

            //Valore numerico eventualmente utilizzato nel confronto
            $table->decimal('numeric_value', 10, 3)
                ->nullable();

            //Valore testuale eventualmente utilizzato nel confronto
            $table->string('text_value')
                ->nullable();

            //Condizione particolare del filtro
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa trasformazione
            $table->unique([
                'transformation_id',
                'key',
            ], 'transformation_form_filters_unique');

            //Velocizza la ricerca dei filtri per tipo
            $table->index([
                'transformation_id',
                'filter_type',
                'target_id',
            ], 'transformation_form_filters_target_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_form_filters');
    }
};
