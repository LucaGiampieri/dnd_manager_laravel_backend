<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('effect_definition_feature_modifiers', function (Blueprint $table) {
            $table->id();

            //Effetto a cui appartiene il modificatore
            $table->foreignId('effect_definition_id')
                ->constrained('effect_definitions')
                ->cascadeOnDelete();

            //Feature interessata
            $table->foreignId('feature_id')
                ->constrained('features')
                ->cascadeOnDelete();

            //Operazione applicata alla feature
            $table->enum('operation', [
                'grant',
                'remove',
                'suppress',
            ])->default('grant');

            //Numero massimo di utilizzi concesso dall'effetto
            $table->unsignedInteger('max_uses')
                ->nullable();

            //Condizione necessaria per applicare la modifica
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita di applicare due volte la stessa operazione alla stessa feature nello stesso effetto
            $table->unique([
                'effect_definition_id',
                'feature_id',
                'operation',
            ], 'effect_definition_feature_modifiers_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_definition_feature_modifiers');
    }
};
