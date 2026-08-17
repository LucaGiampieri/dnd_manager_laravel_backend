<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_sense_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Senso interessato
            $table->foreignId('sense_id')
                ->constrained('senses')
                ->cascadeOnDelete();

            //Operazione applicata al senso
            $table->enum('operation', [
                'grant',
                'remove',
                'set',
                'add',
                'minimum',
                'maximum',
            ]);

            //Portata del senso in metri
            $table->decimal('range', 10, 3)
                ->nullable();

            //Condizione necessaria per applicare il modificatore
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Velocizza il recupero dei modificatori per uno specifico senso
            $table->index([
                'effect_definition_id',
                'sense_id',
            ], 'effect_definition_sense_modifiers_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_sense_modifiers');
    }
};
