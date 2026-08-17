<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_conditions', function (Blueprint $table) {

            $table->id();

            //Incantesimo che produce l'effetto sulla condizione
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Condizione interessata
            $table->foreignId('condition_id')
                ->constrained('conditions')
                ->cascadeOnDelete();

            //Cosa fa lo spell con la condizione
            $table->enum('operation', [
                'apply',
                'remove',
                'immunity'
            ]);

            //In quale situazione viene normalmente applicato l'effetto
            $table->enum('application_type', [
                'automatic',
                'on_hit',
                'failed_save',
                'successful_save',
                'special'
            ])
                ->default('automatic');

            //Se true, la condizione termina quando termina l'effetto dello spell
            $table->boolean('ends_with_spell')
                ->default(true);

            //Eventuale condizione/regola aggiuntiva
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione nel caso lo spell gestisca più condizioni
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Eventuali note
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di duplicare esattamente la stessa regola nello stesso spell
            $table->unique([
                'spell_id',
                'condition_id',
                'operation',
                'application_type'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_conditions');
    }
};
