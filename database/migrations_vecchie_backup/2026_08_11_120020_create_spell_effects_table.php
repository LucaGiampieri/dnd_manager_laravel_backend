<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('spell_effects', function (Blueprint $table) {

            $table->id();

            //Incantesimo che genera questo effetto
            $table->foreignId('spell_id')
                ->constrained('spells')
                ->cascadeOnDelete();

            //Nome opzionale dell'effetto
            $table->string('name')
                ->nullable();

            //Quando viene applicato
            $table->enum('application_type', [
                'automatic',
                'on_hit',
                'failed_save',
                'successful_save',
                'special'
            ])
                ->default('automatic');

            //A chi si applica normalmente
            $table->enum('target_type', [
                'self',
                'target',
                'targets',
                'area',
                'special'
            ])
                ->default('target');

            //Indica se questo effetto termina quando termina lo spell
            $table->boolean('ends_with_spell')
                ->default(true);

            //Eventuale regola/condizione particolare
            $table->text('condition')
                ->nullable();

            //Descrizione completa dell'effetto
            $table->text('description')
                ->nullable();

            //Ordine rispetto agli altri effetti dello stesso spell
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note
            $table->text('notes')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spell_effects');
    }
};
