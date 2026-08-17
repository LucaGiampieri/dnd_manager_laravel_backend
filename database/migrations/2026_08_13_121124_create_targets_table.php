<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->id();

            //Elemento a cui appartiene la regola di bersaglio
            $table->morphs('targetable');

            //Tipo di bersaglio
            $table->foreignId('target_type_id')
                ->constrained('target_types')
                ->cascadeOnDelete();

            //Modalità con cui viene determinato il numero di bersagli
            $table->enum('selection_type', [
                'exact',
                'up_to',
                'range',
                'all',
                'special',
            ])->default('exact');

            //Numero minimo di bersagli
            $table->unsignedSmallInteger('minimum_targets')
                ->nullable();

            //Numero massimo di bersagli
            $table->unsignedSmallInteger('maximum_targets')
                ->nullable();

            //Distanza massima del bersaglio in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Indica se possono essere scelti soltanto bersagli consenzienti
            $table->boolean('willing_only')
                ->default(false);

            //Indica se l'utilizzatore può scegliere se stesso
            $table->boolean('can_target_self')
                ->default(false);

            //Indica se l'utilizzatore è incluso nell'effetto
            $table->boolean('includes_source')
                ->default(false);

            //Indica se è richiesta linea di vista
            $table->boolean('requires_line_of_sight')
                ->default(false);

            //Condizione che limita i bersagli validi
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione o visualizzazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero dei bersagli di uno specifico elemento
            $table->index([
                'targetable_type',
                'targetable_id',
                'sort_order',
            ], 'targets_targetable_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
