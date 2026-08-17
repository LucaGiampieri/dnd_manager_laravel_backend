<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('conditions', function (Blueprint $table) {

            $table->id();

            //Regolamento al quale appartiene la condizione
            $table->foreignId('ruleset_id')
                ->constrained('rulesets')
                ->cascadeOnDelete();

            //Chiave tecnica stabile della condizione
            $table->string('key');

            //Nome della condizione
            $table->string('name');

            //Descrizione degli effetti
            $table->text('description')
            ->nullable();

            //Indica se la condizione usa livelli, come l'indebolimento
            $table->boolean('is_level_based')
                ->default(false);

            //Numero massimo di livelli previsto dal regolamento
            $table->unsignedTinyInteger('maximum_level')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'ruleset_id',
                'key',
            ], 'conditions_ruleset_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};
