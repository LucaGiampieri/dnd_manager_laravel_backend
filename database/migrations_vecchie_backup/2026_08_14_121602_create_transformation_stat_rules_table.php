<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transformation_stat_rules', function (Blueprint $table) {
            $table->id();

            //Trasformazione a cui appartiene la regola
            $table->foreignId('transformation_id')
                ->constrained('transformations')
                ->cascadeOnDelete();

            //Chiave tecnica della regola
            $table->string('key');

            //Parte delle statistiche interessata
            $table->enum('stat_type', [
                'ability',
                'armor_class',
                'hit_points',
                'movement',
                'sense',
                'skill',
                'saving_throw',
                'creature_type',
                'size',
                'language',
                'proficiency',
                'trait',
                'action',
                'spellcasting',
                'other',
            ]);

            //Elemento specifico interessato dalla regola
            $table->unsignedBigInteger('target_id')
                ->nullable();

            //Modalità con cui viene determinato il valore finale
            $table->enum('rule_type', [
                'replace',
                'retain',
                'merge',
                'use_higher',
                'use_lower',
                'remove',
                'special',
            ]);

            //Condizione necessaria per applicare la regola
            $table->text('condition')
                ->nullable();

            //Ordine di applicazione
            $table->unsignedSmallInteger('sort_order')
                ->default(0);

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nella stessa trasformazione
            $table->unique([
                'transformation_id',
                'key',
            ], 'transformation_stat_rules_unique');

            //Velocizza la ricerca delle regole per tipo
            $table->index([
                'transformation_id',
                'stat_type',
                'target_id',
            ], 'transformation_stat_rules_target_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transformation_stat_rules');
    }
};
