<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('feat_prerequisites', function (Blueprint $table) {
            $table->id();

            //Talento a cui appartiene il prerequisito
            $table->foreignId('feat_id')
                ->constrained('feats')
                ->cascadeOnDelete();

            //Chiave tecnica del prerequisito
            $table->string('key');

            //Gruppo logico del prerequisito
            $table->unsignedSmallInteger('requirement_group')
                ->default(1);

            //Tipo di prerequisito
            $table->enum('prerequisite_type', [
                'ability_score',
                'level',
                'race',
                'subrace',
                'class',
                'subclass',
                'spellcasting',
                'skill_proficiency',
                'weapon_proficiency',
                'armor_proficiency',
                'tool_proficiency',
                'feat',
                'other',
            ]);

            //ID del contenuto richiesto
            $table->unsignedBigInteger('prerequisite_id')
                ->nullable();

            //Valore minimo richiesto
            $table->integer('minimum_value')
                ->nullable();

            //Descrizione per requisiti particolari
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso talento
            $table->unique([
                'feat_id',
                'key',
            ], 'feat_prerequisites_unique');

            //Velocizza la ricerca dei prerequisiti collegati
            $table->index([
                'prerequisite_type',
                'prerequisite_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feat_prerequisites');
    }
};
