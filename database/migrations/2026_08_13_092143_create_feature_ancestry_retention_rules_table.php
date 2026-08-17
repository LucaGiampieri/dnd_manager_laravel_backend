<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feature_ancestry_retention_rules', function (Blueprint $table) {
            $table->id();

            //Feature che permette di mantenere elementi della vecchia ascendenza
            $table->foreignId('feature_id')
                ->constrained('features')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Tipo di elemento che può essere mantenuto
            $table->enum('retention_type', [
                'skill_proficiency',
                'movement_speed',
                'language',
                'weapon_proficiency',
                'armor_proficiency',
                'tool_proficiency',
                'feature',
                'other',
            ]);

            //Tipo di movimento specifico, se la regola riguarda una velocità
            $table->foreignId('movement_type_id')
                ->nullable()
                ->constrained('movement_types')
                ->nullOnDelete();

            //Numero massimo di elementi mantenibili
            //NULL indica che non esiste un limite numerico
            $table->unsignedTinyInteger('max_retained')
                ->nullable();

            //Condizione necessaria per applicare la regola
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa feature
            $table->unique([
                'feature_id',
                'key',
            ], 'feature_ancestry_retention_rules_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_ancestry_retention_rules');
    }
};
