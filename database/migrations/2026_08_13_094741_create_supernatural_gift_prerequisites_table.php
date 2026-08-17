<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('supernatural_gift_prerequisites', function (Blueprint $table) {
            $table->id();

            //Dono soprannaturale a cui appartiene il prerequisito
            $table->foreignId('supernatural_gift_id')
                ->constrained('supernatural_gifts')
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
                'creature_type',
                'class',
                'subclass',
                'spellcasting',
                'skill_proficiency',
                'feat',
                'supernatural_gift',
                'other',
            ]);

            //ID del contenuto richiesto
            $table->unsignedBigInteger('prerequisite_id')
                ->nullable();

            //Valore minimo eventualmente richiesto
            $table->integer('minimum_value')
                ->nullable();

            //Condizione particolare del prerequisito
            $table->text('condition')
                ->nullable();

            //Note aggiuntive
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            //Evita chiavi duplicate nello stesso dono
            $table->unique([
                'supernatural_gift_id',
                'key',
            ], 'supernatural_gift_prerequisites_unique');

            //Velocizza la ricerca dei contenuti richiesti
            $table->index([
                'prerequisite_type',
                'prerequisite_id',
            ], 'ix_supernatural_gift_prerequisites_prerequisite_type_pr_f7d128fe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supernatural_gift_prerequisites');
    }
};
