<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('target_filters', function (Blueprint $table) {
            $table->id();

            //Definizione dei bersagli a cui appartiene il filtro
            $table->foreignId('target_id')
                ->constrained('targets')
                ->cascadeOnDelete();

            //Chiave tecnica del filtro
            $table->string('key');

            //Gruppo logico del filtro
            $table->unsignedSmallInteger('requirement_group')
                ->default(0);

            //Tipo di requisito sul bersaglio
            $table->enum('filter_type', [
                'creature_type',
                'creature_tag',
                'size',
                'condition',
                'relationship',
                'willing',
                'conscious',
                'living',
                'object',
                'other',
            ]);

            //Operatore utilizzato dal filtro
            $table->enum('operator', [
                'equals',
                'not_equals',
                'less_than',
                'less_than_or_equal',
                'greater_than',
                'greater_than_or_equal',
                'has',
                'does_not_have',
                'is',
                'is_not',
                'special',
            ])->default('equals');

            //Elemento specifico utilizzato dal filtro
            $table->unsignedBigInteger('filter_target_id')
                ->nullable();

            //Valore numerico utilizzato dal filtro
            $table->decimal('numeric_value', 10, 3)
                ->nullable();

            //Valore testuale utilizzato dal filtro
            $table->string('text_value')
                ->nullable();

            //Condizione aggiuntiva del filtro
            $table->text('condition')
                ->nullable();

            //Ordine di valutazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa definizione dei bersagli
            $table->unique([
                'target_id',
                'key',
            ], 'target_filters_unique');

            //Velocizza la valutazione dei filtri
            $table->index([
                'target_id',
                'requirement_group',
                'filter_type',
            ], 'target_filters_requirement_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_filters');
    }
};
